dev: 
	NODE_ENV=development docker-compose up

prod: 
	NODE_ENV=production docker-compose up web

build: 
	NODE_ENV=production docker-compose run web composer update -d /srv
	NODE_ENV=production docker-compose run dev npm update
	NODE_ENV=production docker-compose run dev webpack --progress

image:
	NODE_ENV=production docker-compose build -t nonfiction/bedrock-site web
