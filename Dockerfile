FROM wordpress:latest

# Instalar WP-CLI
RUN curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp

# Copiar o child theme para dentro da imagem
COPY wp-content/themes/faroseguro /var/www/html/wp-content/themes/faroseguro

# Permissões corretas
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/faroseguro

EXPOSE 80

