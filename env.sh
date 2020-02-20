export DOMAIN=$(hostname)

alias up="docker-compose up"
alias down="docker-compose down"
alias d="docker-compose exec web"

# alias php="docker-compose exec web wp php"
# alias wp="docker-compose exec web wp --allow-root"
# alias npm="docker-compose exec web npm"

php() {
  docker-compose exec web php $@
}

wp() {
  docker-compose exec web wp --allow-root $@
}

npm() {
  docker-compose exec web npm $@
}

composer() {
  if [ -e "$1" ]; then 
    docker-compose exec web composer $@ -d ../../..
  else
    docker-compose exec web composer help -d ../../..
  fi
}
