include .env

all:
	@echo "【 $(APP_NAME):make 】"
	@echo "   ‣ install"
	@echo "   ‣ build"
	@echo "   ‣ up up-prod down"
	@echo "   ‣ logs clean"


# --------------
# RUN SERVER:
# --------------

# Launch in development mode
up: 
	docker-compose up -d
	docker-compose logs -f

# Launch in production mode (with build
up-prod: build
	docker-compose -f docker-compose.yml -f docker-compose.production.yml up -d
	docker-compose logs -f

# Shut it all down
down:
	docker-compose down

logs:
	docker-compose logs -f


# --------------
# UPDATE STEPS:
# --------------

# 1. npm: update all node modules necessary for webpack
# 2. Webpack: bundle static assets
# 3. Docker: add code, install composer packages, build image
# 4. WordPress: run database updates
build: 
	docker-compose run --rm dev npm update --save-dev
	docker-compose run --rm dev webpack --progress
	COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose build web
	docker-compose run wp core update-db

# --------------
# INSTALL STEPS:
# --------------

# 1. Rename bedrock-site, build .env file, databases, and database user
.env:
	docker run --rm -it -e APP_NAME=$(notdir $(shell pwd)) -e APP_HOST=$(shell hostname -f) -v $(PWD):/srv nonfiction/bedrock:env dotenv

# 3. Install WP database, activate plugins and theme
install: build
	docker-compose run wp core install --url=https://$(APP_NAME).$(APP_HOST) --title=$(APP_NAME) --admin_email=web@nonfiction.ca --admin_user=nonfiction --admin_password=$(DB_PASSWORD)
	docker-compose run wp plugin activate --all
	docker-compose run wp theme activate theme
	@echo 
	@echo URL: https://$(APP_NAME).$(APP_HOST)/wp/wp-login.php
	@echo Username: nonfiction
	@echo Password: $(DB_PASSWORD)

clean:
	rm -rf data/* && touch data/.gitkeep
