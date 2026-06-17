FROM wordpress:latest

# WP-CLI
RUN curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Plugins (download direto do wordpress.org, sem necessidade de DB)
RUN apt-get update -qq && apt-get install -y -qq unzip && rm -rf /var/lib/apt/lists/*
RUN for plugin in akismet contact-form-7 kadence-blocks really-simple-ssl \
        regenerate-thumbnails w3-total-cache wordfence wordpress-seo; do \
      curl -sL "https://downloads.wordpress.org/plugin/${plugin}.latest-stable.zip" \
           -o /tmp/${plugin}.zip \
      && unzip -q /tmp/${plugin}.zip -d /var/www/html/wp-content/plugins/ \
      && rm /tmp/${plugin}.zip; \
    done

# Tema
COPY wp-content/themes/guiaantifraude /var/www/html/wp-content/themes/guiaantifraude
RUN cp -a /var/www/html/wp-content/themes/guiaantifraude \
          /var/www/html/wp-content/themes/faroseguro

# Permissões
RUN chown -R www-data:www-data /var/www/html/wp-content/themes \
                                /var/www/html/wp-content/plugins

EXPOSE 80
