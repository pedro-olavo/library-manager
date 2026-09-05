FROM php:8.2-apache

# Extensões necessárias para conexão com PostgreSQL via PDO
RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Corrige "More than one MPM loaded" no build: algumas variações da imagem
# base vêm com mais de um Multi-Processing Module do Apache habilitado ao
# mesmo tempo (prefork + event/worker). mod_php só é compatível com
# mpm_prefork. A correção definitiva (que também roda a cada boot do
# container, por segurança) está em docker-entrypoint.sh.
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

# O entrypoint reaplica a correção de MPM a cada início de container (não
# apenas no build), garantindo que o Apache suba corretamente mesmo que a
# plataforma de deploy reintroduza o módulo conflitante de alguma forma que
# não conseguimos reproduzir/depurar diretamente.
RUN sed -i 's/\r$//' /var/www/html/docker-entrypoint.sh \
    && chmod +x /var/www/html/docker-entrypoint.sh
CMD ["/var/www/html/docker-entrypoint.sh"]

EXPOSE 80
