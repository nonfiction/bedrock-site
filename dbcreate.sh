#!/bin/bash

source .env

DB_ROOT_USER=doadmin
read -sp 'Please enter your root database password: ' DB_ROOT_PASSWORD          

readarray -d : -t DB <<< "$DB_HOST"
DB_HOST=${DB[0]}
DB_PORT=${DB[1]}

DB_USER=$DB_USER
DB_PASSWORD=$DB_PASSWORD

DB_NAME_DEVELOPMENT=${APP}_development
DB_NAME_PRODUCTION=${APP}_production

function db () {
 echo ""
 echo "$1"
  docker run --rm -it mysql mysql -h$DB_HOST -P$DB_PORT -u$DB_ROOT_USER -p$DB_ROOT_PASSWORD -e "$1"
}

db "show databases;"
