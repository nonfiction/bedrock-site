include .env

all:
	@echo "[[ $(APP) Makefile tasks ]]"
	@echo "init update build composer npm webpack dev pro shell"

# - Do this first -
# Create .env, databases and then update packages
init: .env db update

# - Periodically do this -
# Update all the things: 
update: build composer npm webpack 

# - Run Server -

# Launch in development mode
dev: 
	docker-compose up

# Launch in production mode
pro: update
	docker-compose -f docker-compose.yml -f docker-compose.pro.yml up


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
# INIT STEPS:
# --------------

# 1. Build .env file, databases, and database user
.env:
	docker run --rm -it -e APP_HOST=$(shell hostname -f) -v $(PWD):/srv nonfiction/bedrock:tasks dotenv

install: .env
	docker-compose run wp core install --url=https://$(APP_NAME).$(APP_HOST) --title=$(APP_NAME) --admin_email=web@nonfiction.ca --admin_user=nonfiction --admin_password=$(DB_PASSWORD)
	docker-compose run wp theme activate theme
	@echo https://$(APP_NAME).$(APP_HOST)/wp/wp-login.php
