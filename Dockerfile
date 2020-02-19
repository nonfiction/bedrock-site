FROM nonfiction/bedrock:latest

# Copy the codebase
COPY . /srv/web/app/site
RUN chown -R www-data:www-data /srv/web

# Install all PHP packages including Wordpress
RUN composer update

# Install all JS modules for theme development
RUN cd /srv/web/app/site && npm update --dev
