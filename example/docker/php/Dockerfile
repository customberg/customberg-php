FROM phpdockerio/php:8.1-fpm
WORKDIR "/app"

ENV TERM=linux
ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update; \
    apt-get -y --no-install-recommends install software-properties-common; \
    add-apt-repository ppa:openswoole/ppa -y; \
    apt-get update; \
    apt-get -y --no-install-recommends install \
        php8.1-gd \
        php8.1-sqlite3; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN groupadd -r app -g 1000 && useradd -u 1000 -r -g app -m -d /home/app -s /usr/bin/bash -c "App user" app
