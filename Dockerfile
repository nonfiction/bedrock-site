FROM nonfiction/bedrock:web

# Persist uploads in this volume
VOLUME /srv/web/app/uploads

# Give Apache permissions for /app dir
RUN chown -R www-data:www-data /srv/web/app

# Add this mu-plugin to auto-load our plugin located in /app/site
COPY --chown=www-data:www-data ./mu-plugins/site.php /srv/web/app/mu-plugins/site.php

# Add any 3rd-party plugins not available on https://wpackagist.org
# COPY --chown=www-data:www-data ./plugins/not-on-wpackagist /srv/web/app/plugins/not-on-wpackagist

# Add any 3rd-party themes not available on https://wpackagist.org
# COPY --chown=www-data:www-data ./themes/not-on-wpackagist /srv/web/app/themes/not-on-wpackagist

# Copy the site's composer.json for install
COPY --chown=www-data:www-data ./site/composer.json /srv/web/app/site/composer.json

# Install all PHP packages including Wordpress
RUN composer update -d /srv

# Copy the full codebase
COPY --chown=www-data:www-data ./site /srv/web/app/site
