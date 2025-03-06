# Usando PHP 8.3 com FPM
FROM php:8.3-fpm

# Instalar extensões e dependências necessárias
RUN apt-get update && apt-get install -y \
    curl zip unzip git libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto para dentro do container
COPY . .

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissões para storage e bootstrap
RUN chmod -R 777 storage bootstrap/cache

CMD ["php-fpm"]
