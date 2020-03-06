FROM nonfiction/bedrock:web

# Copy the codebase
COPY . /srv/web/app/site
RUN chown -R www-data:www-data /srv/web

# RUN rm -rf /srv/web/app/themes && ln -s /srv/web/wp-content/themes /srv/web/app/themes

# Install all PHP packages including Wordpress
RUN composer update -d /srv 
