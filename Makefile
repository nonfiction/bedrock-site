include .env

all:
	@echo "[[ $(APP) Makefile tasks ]]"
	@echo "init update build composer npm webpack dev prod shell"

# - Do this first -
# Create .env, databases and then update packages
init: .env db update

# - Periodically do this -
# Update all the things: 
update: build composer npm webpack 

# - Run Server -

# Launch in dev mode
dev: 
	HOSTNAME=$(shell hostname) docker-compose up

# Launch in prod mode
prod: update
	HOSTNAME=$(shell hostname) docker-compose -f docker-compose.yml -f docker-compose.prod.yml up


# --------------
# UPDATE STEPS:
# --------------

# 1. Docker: build image
build:
	HOSTNAME=$(shell hostname) docker-compose build web

# 2. Wordpress: update all composer packages
composer: 
	HOSTNAME=$(shell hostname) docker-compose run web composer update -d /srv

# 3. npm: update all node modules necessary for webpack
npm: 
	HOSTNAME=$(shell hostname) docker-compose run dev npm update --save-dev

# 4. Webpack: bundle static assets
webpack: 
	HOSTNAME=$(shell hostname) docker-compose run dev webpack --progress


# --------------
# INIT STEPS:
# --------------

# 1. Build .env file
.env:
	touch .env
	docker run --rm -it -e HOSTNAME=$(shell hostname) -v $(PWD)/.env:/.env nonfiction/bedrock:tasks env

# 2. Create development and production databases, and database user
db: .env
	docker run --rm -it -v $(PWD)/.env:/.env nonfiction/bedrock:tasks db
