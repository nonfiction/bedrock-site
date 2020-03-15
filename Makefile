include .env 

# Variables
prod  := -f docker-compose.yml -f docker-compose.production.yml
deploy := DOCKER_HOST=ssh://root@$(DEPLOY_HOST) APP_HOST=$(DEPLOY_HOST)

all:
	@echo "【 $(APP_NAME)@$(APP_HOST) => $(DEPLOY_HOST) 】"
	@echo "   ‣ install"
	@echo "   ‣ assets ‣ build ‣ deploy-build"
	@echo "   ‣ plugin add=PLUGIN ‣ theme add=THEME ‣ module add=MODULE"
	@echo "   ‣ up ‣ upp"
	@echo "   ‣ deploy ‣ undeploy ‣ target ‣ logs"
	@echo ""


# --------------
# UPDATE STEPS:
# --------------

# 1. npm: update all node modules necessary for webpack
# 2. Webpack: bundle static assets
# 3. Docker: add code, install composer packages, build image
# 4. WordPress: run database updates

up: assets build
	docker-compose up --remove-orphans

# Launch in production mode
upp: assets build
	docker-compose $(prod) up --remove-orphans web


# --------------
# DEPLOY STEPS:
# --------------

target: 
	docker-compose run --rm env deploy_host

deploy: assets deploy-build
	$(deploy) docker-compose $(prod) up --remove-orphans -d web
	$(deploy) docker-compose logs -f

undeploy:  
	$(deploy) docker-compose down --remove-orphans 

logs:
	$(deploy) docker-compose logs -f


# --------------
# INSTALL STEPS:
# --------------

# 1. Rename bedrock-site, build .env file, databases, and database user
.env:
	docker run --rm -it \
		-e APP_NAME=$(notdir $(shell pwd)) \
		-e APP_HOST=$(shell hostname -f) \
		-v $(PWD):/srv \
		nonfiction/bedrock:env dotenv

# 2. Compile assets before building web image
assets:
	docker-compose run --rm dev npm update --save-dev
	docker-compose run --rm dev webpack --progress

# 3a. Build web image and perform any DB updates
build:
	docker-compose build --pull web
	docker-compose run wp core update-db

# 3b. Build web image and perform any DB updates on deploy host
deploy-build:
	$(deploy) docker-compose build --pull web
	$(deploy) docker-compose run wp core update-db

# 4. Install WP database, activate plugins and theme
install: .env assets build
	docker-compose build --pull web 
	docker-compose run wp core install \
		--url=https://$(APP_NAME).$(APP_HOST) \
		--title=$(APP_NAME) \
		--admin_email=web@nonfiction.ca \
		--admin_user=nf-$(APP_NAME) \
		--admin_password=$(DB_PASSWORD)
	docker-compose run wp plugin activate --all
	docker-compose run wp theme activate theme
	docker-compose run wp rewrite structure /%postname%/
	@echo 
	@echo URL: https://$(APP_NAME).$(APP_HOST)/wp/wp-login.php
	@echo Username: nf-$(APP_NAME)
	@echo Password: $(DB_PASSWORD)

# make plugin add=plugin_name
plugin:
	@test $(add)
	docker-compose run --rm web composer require --no-update wpackagist-plugin/$(add)

# make theme add=theme_name
theme:
	@test $(add)
	docker-compose run --rm web composer require --no-update wpackagist-theme/$(add)

# make module add=module_name
module:
	@test $(add)
	docker-compose run --rm dev npm install $(add) --save-dev

clean:
	rm -rf data/* && touch data/.gitkeep

db:
	docker-compose run --rm env db_create

# db-push:
# 	docker-compose run --rm env db_push
#
# db-pull:
# 	docker-compose run --rm env db_pull
