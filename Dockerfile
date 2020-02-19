FROM nonfiction/bedrock:latest

# Install node, npm, npx
ARG NODEJS=https://nodejs.org/dist/v12.16.1/node-v12.16.1-linux-x64.tar.xz
RUN set -ex; \
  cd /tmp && mkdir nodejs; \
  curl ${NODEJS} > nodejs.tar.xz; \
  tar -xJf nodejs.tar.xz -C nodejs --strip-components 1; \
  mv nodejs/bin/* /usr/local/bin/; \
  mv nodejs/lib/node_modules /usr/local/lib/; \
  rm -rf nodejs*

# Copy the codebase
COPY . /srv/web/app/site
RUN chown -R www-data:www-data /srv/web

# Install all PHP packages including Wordpress
RUN composer update

# Install all JS modules for theme development
RUN cd /srv/web/app/site && npm update --dev
