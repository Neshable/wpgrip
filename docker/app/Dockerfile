FROM php:8.4-fpm-bookworm

LABEL maintainer="WPGrip"

ARG WWWUSER=33
ARG WWWGROUP=33

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# ── System packages ─────────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        git curl unzip zip gnupg gosu ca-certificates \
        libpng-dev libjpeg62-turbo-dev libwebp-dev libfreetype6-dev \
        libzip-dev libxml2-dev libcurl4-openssl-dev \
        libonig-dev libpq-dev libmagickwand-dev \
        libssl-dev libicu-dev \
        openssh-server supervisor \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── PHP core extensions ──────────────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath ctype curl dom exif fileinfo gd \
        intl mbstring mysqli opcache pcntl \
        pdo pdo_mysql pdo_pgsql pgsql \
        posix sockets xml zip

# ── PECL extensions ──────────────────────────────────────────────────────────
RUN pecl install imagick igbinary msgpack redis \
    && docker-php-ext-enable imagick igbinary msgpack redis

# ── Composer ─────────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── SSH server ───────────────────────────────────────────────────────────────
RUN mkdir /var/run/sshd \
    && echo 'root:wpgrip' | chpasswd \
    && sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config \
    && sed -i 's/^#*PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config

# ── Match host uid/gid for www-data so bind-mounts work ─────────────────────
RUN groupmod -g ${WWWGROUP} www-data 2>/dev/null || true \
    && usermod -u ${WWWUSER} -g ${WWWGROUP} www-data 2>/dev/null || true

# ── Config files ─────────────────────────────────────────────────────────────
COPY php.ini         /usr/local/etc/php/conf.d/99-app.ini
COPY php-fpm.conf    /usr/local/etc/php-fpm.d/zz-app.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh   /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/laravel

# 9000 = php-fpm   22 = sshd
EXPOSE 9000 22

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
