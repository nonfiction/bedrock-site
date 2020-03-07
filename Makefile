include .env

all:
	@echo "[$(APP_NAME):make]"
	@echo "[1] install"
	@echo "[2] update"
	@echo "[3] build composer npm webpack"
	@echo "[4] dev pro down"


# --------------
# RUN SERVER:
# --------------

# Launch in development mode
dev: 
	docker-compose up -d
	docker-compose logs -f

# Launch in production mode
pro: build composer npm webpack 
	docker-compose -f docker-compose.yml -f docker-compose.pro.yml up -d
	docker-compose logs -f

down:
	docker-compose down


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
