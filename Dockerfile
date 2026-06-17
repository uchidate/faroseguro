FROM wordpress:latest

# Instalar WP-CLI
RUN curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp

# Copiar o child theme para dentro da imagem
COPY wp-content/themes/guiaantifraude /var/www/html/wp-content/themes/guiaantifraude

# Compatibilidade de transição: evita quebra se o banco ainda estiver com
# o stylesheet antigo ativo até o tema Guia Antifraude ser selecionado.
RUN cp -a /var/www/html/wp-content/themes/guiaantifraude /var/www/html/wp-content/themes/faroseguro

# Permissões corretas
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/guiaantifraude /var/www/html/wp-content/themes/faroseguro

EXPOSE 80
