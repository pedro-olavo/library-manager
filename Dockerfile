FROM php:8.2-apache

# Extensões necessárias para conexão com PostgreSQL via PDO
RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Corrige "More than one MPM loaded": algumas variações da imagem base
# vêm com mais de um Multi-Processing Module do Apache habilitado ao mesmo
# tempo (prefork + event/worker), o que impede o Apache de iniciar.
# mod_php só é compatível com mpm_prefork, então desabilitamos os demais
# explicitamente antes de habilitar o rewrite.
RUN a2dismod -f mpm_event mpm_worker >/dev/null 2>&1 || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Aponta o document root da aplicação para a pasta public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Permite que o .htaccess sobrescreva as regras de roteamento
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 80
