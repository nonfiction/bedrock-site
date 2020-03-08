FROM nonfiction/bedrock:web

# Add this mu-plugin to auto-load our plugin located in /app/site
COPY ./mu-plugins/site.php /srv/web/app/mu-plugins/site.php

# Add any 3rd-party plugins not available on https://wpackagist.org
# COPY ./plugins/plugin-not-on-wpackagist /srv/web/app/plugins/plugin-not-on-wpackagist

# Copy the codebase
COPY ./site /srv/web/app/site

# Persist uploads in this volume
VOLUME /srv/web/app/uploads

# Give Apache permissions for /app dir
RUN chown -R www-data:www-data /srv/web/app

# Install all PHP packages including Wordpress
RUN composer update -d /srv
