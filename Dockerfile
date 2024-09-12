FROM php:8.2.4-apache-bullseye

#GERA UPGRADE DAS BIBLIOTECAS
RUN apt-get update && apt-get -y upgrade git;

#MARCA PARA GIT IGNORAR O CERTIFICADO
RUN git config --global http.sslVerify false;

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .
COPY .env .
COPY .htaccess .

RUN docker-php-ext-install pdo pdo_mysql

RUN chown -R www-data:www-data /var/www

# Criar o diretório uploads
RUN mkdir -p /var/www/html/uploads

# Alterar as permissões do diretório uploads para 777 (leitura, escrita e execução para todos os usuários)
RUN chmod -R 777 /var/www/html/uploads


# ##INSTALL COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1

EXPOSE 80

RUN set -x && \ 
    composer install --no-dev --working-dir=/var/www/html && \ 
    chown -R www-data:www-data /var/www/html && \ 
    rm -rf /usr/local/bin/composer;

 CMD ["apache2-foreground"]