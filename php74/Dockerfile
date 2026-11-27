FROM php:7.4.33-fpm-bullseye

RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list \
    && sed -i '/bullseye-updates/d' /etc/apt/sources.list \
    && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until \
    && echo 'APT::Get::AllowUnauthenticated "true";' >> /etc/apt/apt.conf.d/99no-check-valid-until \
    && apt-get -o Acquire::Retries=3 update \
    && apt-get -o Acquire::Retries=3 install -y --allow-unauthenticated --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libicu-dev \
        libxml2-dev \
        libmagickwand-dev \
        libmcrypt-dev \
        libbz2-dev \
        libgmp-dev \
        gettext \
        libgettextpo-dev \
        libldap2-dev \
        unixodbc-dev \
        freetds-dev \
        libpq-dev \
        libaspell-dev \
        aspell \
        aspell-en \
        libtidy-dev \
        libxslt1-dev \
        libdb-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure ldap --with-libdir=lib/$(uname -m)-linux-gnu/ \
    && docker-php-ext-install -j"$(nproc)" \
        bz2 \
        calendar \
        dba \
        gettext \
        gmp \
        ldap \
        mbstring \
        mysqli \
        pcntl \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        shmop \
        sysvmsg \
        sysvsem \
        sysvshm \
        tidy \
        xmlrpc \
        xsl \
        gd \
        zip \
        bcmath \
        exif \
        intl \
        opcache \
        soap \
        sockets \
    && pecl install redis-5.3.7 imagick-3.7.0 xdebug-2.9.8 mcrypt-1.0.7 \
    && docker-php-ext-enable redis imagick xdebug mcrypt \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# brew 另有 PDO_ODBC / pdo_dblib（独立 odbc 扩展在 Debian 镜像上编译困难）
RUN mkdir -p /usr/local/incl \
    && ln -sf /usr/include/sqlext.h /usr/local/incl/sqlext.h \
    && ln -sf /usr/include/sql.h /usr/local/incl/sql.h \
    && docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \
    && docker-php-ext-install pdo_odbc \
    && docker-php-ext-configure pdo_dblib --with-libdir=lib/$(uname -m)-linux-gnu/ \
    && docker-php-ext-install pdo_dblib

COPY conf/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY conf/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY conf/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

CMD ["php-fpm", "-F"]
