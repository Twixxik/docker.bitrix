FROM composer:2 as composer

FROM mlocati/php-extension-installer:1.5 as extension_installer

FROM busybox:glibc AS busybox

FROM php:7.4-fpm-bullseye AS php_fpm

RUN apt-get update -qq \
    && apt-get install -y --no-install-recommends \
        git \
        htop \
        less \
        procps \
        wget \
        graphviz \
    && rm -rf /var/lib/apt/lists /var/cache/apt/archives

COPY --from=busybox /bin/sendmail /usr/sbin/

ENV \
#    PHP_DATE_TIMEZONE=UTC \
#    PHP_MEMORY_LIMIT=256M \
#    PHP_MAX_INPUT_VARS=1000 \
#    PHP_UPLOAD_MAX_FILESIZE=25M \
#    PHP_POST_MAX_SIZE=25M \
#    PHP_MAX_EXECUTION_TIME=30 \
#    PHP_FPM_MAX_CHILDREN=10 \
#    PHP_FPM_REQUEST_TERMINATE_TIMEOUT=60 \
    PICKLE_PECL_UNSAFE=1 \
    IPE_GD_WITHOUTAVIF=1

# https://github.com/mlocati/docker-php-extension-installer#supported-php-extensions
COPY --from=extension_installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions \
        bcmath-stable \
        gd-stable \
        intl-stable \
        mysqli-stable \
        opcache-stable \
        pdo_mysql-stable \
        soap-stable \
        uuid-stable \
        xdebug-3.1.6 \
        xhprof-stable \
        zip-stable \
    && rm -rf /usr/bin/install-php-extensions /var/lib/apt/lists /var/cache/apt/archives

COPY php/ /

ARG UID=1000
ARG GID=1000

RUN addgroup --gid $GID warp \
    && adduser --system --home /home/warp --shell /bin/bash --uid $UID --ingroup warp --disabled-password warp \
    && mkdir -p /home/warp/app \
    && chown -R warp:warp /home/warp

COPY --from=composer /usr/bin/composer /usr/local/bin/

WORKDIR /home/warp/app
USER warp

# Inspired by: https://github.com/ikarpovich/bitrix-push-server
FROM node:16-alpine AS push_server

ARG ALPINE_REPO=http://dl-cdn.alpinelinux.org/alpine/
RUN sed -i -r 's#^http.+/(.+/main)#'${ALPINE_REPO%/}'/\1#' /etc/apk/repositories \
    && sed -i -r 's#^http.+/(.+/community)#'${ALPINE_REPO%/}'/\1#' /etc/apk/repositories \
    && sed -i -r 's#^http.+/(.+/testing)#'${ALPINE_REPO%/}'/\1#' /etc/apk/repositories \
    && echo /etc/apk/repositories \
    && apk add --no-cache --update \
        bash \
        less

COPY push_server/ /

ARG UID=1000
ARG GID=1000

RUN deluser --remove-home node \
    && addgroup -g $GID warp \
    && adduser -D -S -h /home/warp -s /bin/bash -G warp -u $UID warp \
    && mkdir -p /home/warp/app \
    && chown -R warp:warp /home/warp

USER warp
WORKDIR /home/warp/app

# https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=135&LESSON_ID=20906
ADD --chown=$UID:$GID https://repo.bitrix.info/vm/push-server-0.3.0.tgz ./
RUN npm install --omit=dev ./push-server-0.3.0.tgz \
    && rm -f ./push-server-0.3.0.tgz \
    && ln -s node_modules/push-server/server.js start.js
EXPOSE 3000
