FROM nonfiction/bedrock:latest

# Copy the codebase
COPY . /srv/web/app/site
RUN chown -R www-data:www-data /srv/web

# Install all PHP packages including Wordpress
RUN composer update -d /srv 
