FROM php:7.2-cli

# Version
ENV PHPREDIS_VERSION 4.0.1
ENV SWOOLE_VERSION 4.2.13

# Timezone
RUN /bin/cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
    && echo 'Asia/Shanghai' > /etc/timezone

# Libs
RUN apt-get update \
    && apt-get install -y \
    curl \
    wget \
    git \
    zip \
    libz-dev \
    libssl-dev \
    libnghttp2-dev \
    libpcre3-dev \
    procps \
    psmisc \
    && apt-get clean \
    && apt-get autoremove

# PDO extension
RUN docker-php-ext-install pdo_mysql

# Redis extension
RUN pecl install redis-${PHPREDIS_VERSION} \
    && docker-php-ext-enable redis

# Swoole extension
RUN wget https://github.com/swoole/swoole-src/archive/v${SWOOLE_VERSION}.tar.gz -O swoole.tar.gz \
    && mkdir -p swoole \
    && tar -xf swoole.tar.gz -C swoole --strip-components=1 \
    && rm swoole.tar.gz \
    && ( \
    cd swoole \
    && phpize \
    && ./configure --enable-async-redis --enable-mysqlnd --enable-openssl --enable-http2 \
    && make -j$(nproc) \
    && make install \
    ) \
    && rm -r swoole \
    && docker-php-ext-enable swoole

WORKDIR /var/www/code

COPY . .

EXPOSE 80

RUN touch /tmp/swoole.log

CMD ["tail", "-f", "/tmp/swoole.log"]
# CMD ["php", "/var/www/code/server/http/http_svr.php", "start"]