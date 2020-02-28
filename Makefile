include .env

all:
	@echo "[[ $(APP) Makefile tasks ]]"
	@echo "update build composer npm webpack dev prod shell -- password: $(APP)"

# Update all the things: 
update: build composer npm webpack 

# 1. Docker: build image
build:
	HOSTNAME=$(shell hostname) docker-compose build web

# 2. Wordpress: update all composer packages
composer: 
	HOSTNAME=$(shell hostname) docker-compose run web composer update -d /srv

# 3. npm: update all node modules necessary for webpack
npm: 
	HOSTNAME=$(shell hostname) docker-compose run dev npm install

# 4. Webpack: bundle static assets
webpack: 
	HOSTNAME=$(shell hostname) docker-compose run dev webpack --progress

init: db

.env:
	@echo "[[ Generating .env ]]"
	touch .env; docker run --rm -it -v "$(PWD)/.env":/.env nonfiction/bedrock dotenv
	# docker run --rm -it mysql mysql -hmysql.nfweb.ca -P25060 -udoadmin -p -e "show databases;"
	# touch .env

db: .env
	docker run --rm -it -v "$(PWD)/.env":/.env nonfiction/bedrock db
	docker run --rm -it mysql mysql -h$(DB_HOST_ONLY) -P$(DB_HOST_PORT) -udoadmin -p$(DB_HOST_PASSWORD) -e "show databases;"


# Launch in dev mode
dev: 
	HOSTNAME=$(shell hostname) docker-compose up

# Launch in prod mode
prod: update
	HOSTNAME=$(shell hostname) docker-compose -f docker-compose.yml -f docker-compose.prod.yml up
