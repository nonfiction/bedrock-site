include .env

all:
	@echo "【 $(APP_NAME):make 】"
	@echo "   ‣ install"
	@echo "   ‣ update"
	@echo "   ‣ build composer npm webpack"
	@echo "   ‣ up down logs"
	@echo "   ‣ dev stag prod"


# --------------
# RUN SERVER:
# --------------

# Launch in development mode (quickly)
up: 
	docker-compose up -d
	docker-compose logs -f

# Shut it all down
down:
	docker-compose down

logs:
	docker-compose logs -f

# Launch in development mode
dev: update
	docker-compose down
	docker-compose up -d
	docker-compose logs -f

# Launch in staging mode
stag: update
	docker-compose down
	docker-compose -f docker-compose.yml -f docker-compose.staging.yml up -d
	docker-compose logs -f

# Launch in production mode
prod: update
	docker-compose down
	docker-compose -f docker-compose.yml -f docker-compose.production.yml up -d
	docker-compose logs -f


# --------------
# UPDATE STEPS:
# --------------

# 1. Docker: build image
build:
	docker-compose build web

# 2. Wordpress: update all composer packages
composer: 
	docker-compose run web composer update -d /srv

# 3. npm: update all node modules necessary for webpack
npm: 
	docker-compose run dev npm update --save-dev

# 4. Webpack: bundle static assets
webpack: 
	docker-compose run dev webpack --progress


# --------------
# INSTALL STEPS:
# --------------

# 1. Rename bedrock-site, build .env file, databases, and database user
.env:
	docker run --rm -it -e APP_NAME=$(notdir $(shell pwd)) -e APP_HOST=$(shell hostname -f) -v $(PWD):/srv nonfiction/bedrock:env dotenv

# 2. Build docker image, composer packages, npm modules, webpack bundles 
update: build composer npm webpack

# 3. Install WP database, activate plugins and theme
install: update 
	docker-compose run wp core install --url=https://$(APP_NAME).$(APP_HOST) --title=$(APP_NAME) --admin_email=web@nonfiction.ca --admin_user=nonfiction --admin_password=$(DB_PASSWORD)
	docker-compose run wp plugin activate --all
	docker-compose run wp theme activate theme
	@echo 
	@echo URL: https://$(APP_NAME).$(APP_HOST)/wp/wp-login.php
	@echo Username: nonfiction
	@echo Password: $(DB_PASSWORD)
