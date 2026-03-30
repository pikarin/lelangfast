############################################
# Stage 1: Composer dependencies (prod)
############################################
FROM serversideup/php:8.5-frankenphp AS deps

USER root

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./

# Install dependencies without dev packages
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-scripts

# Copy full application source
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

############################################
# Stage 1b: Composer dependencies (dev)
############################################
FROM deps AS deps-dev

RUN composer install \
    --no-interaction \
    --no-scripts \
    && composer dump-autoload

############################################
# Stage 2: Frontend build
############################################
FROM node:22-alpine AS frontend

WORKDIR /app

# Copy package files first for layer caching
COPY package.json package-lock.json ./

# Install npm dependencies
RUN npm ci

# Copy source files needed for build
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

# Build frontend assets
RUN npm run build

############################################
# Stage 3: Production image
############################################
FROM serversideup/php:8.5-frankenphp AS production

# Set environment variables for serversideup
ENV AUTORUN_ENABLED="true" \
    SSL_MODE="off" \
    PHP_OPCACHE_ENABLE="1" \
    PHP_MEMORY_LIMIT="256M"

WORKDIR /var/www/html

# Copy application code from deps stage
COPY --from=deps /var/www/html /var/www/html

# Copy built frontend assets from frontend stage
COPY --from=frontend /app/public/build /var/www/html/public/build

# Set proper ownership
USER root
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
USER www-data

EXPOSE 8080 8443

############################################
# Stage 4: Development image
############################################
FROM serversideup/php:8.5-frankenphp AS development

ENV AUTORUN_ENABLED="true" \
    AUTORUN_LARAVEL_OPTIMIZE="false" \
    SSL_MODE="off" \
    PHP_OPCACHE_ENABLE="0" \
    PHP_MEMORY_LIMIT="512M"

WORKDIR /var/www/html

# Copy application with dev dependencies
COPY --from=deps-dev /var/www/html /var/www/html

# Set proper ownership
USER root
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
USER www-data

EXPOSE 8080 8443
