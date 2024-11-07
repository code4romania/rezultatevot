FROM code4romania/php:8.2 AS vendor

WORKDIR /var/www

COPY --chown=www-data:www-data . /var/www

# install extensions
RUN set -ex; \
    install-php-extensions \
    redis

RUN set -ex; \
    composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-plugins \
    --no-dev \
    --prefer-dist

FROM node:20-alpine AS assets

WORKDIR /build

COPY \
    package.json \
    package-lock.json \
    postcss.config.js \
    vite.config.js \
    ./

RUN set -ex; \
    npm ci --no-audit --ignore-scripts

COPY --from=vendor /var/www /build

RUN set -ex; \
    npm run build

FROM vendor

# Needed for splitting CSVs
RUN set -ex; \
    apk add --no-cache \
    gawk;

COPY docker/s6-rc.d /etc/s6-overlay/s6-rc.d
COPY --from=assets --chown=www-data:www-data /build/public/build /var/www/public/build

# Enable the worker
ENV WORKER_ENABLED=false

# The number of jobs to process before stopping
ENV WORKER_MAX_JOBS=50

# Number of seconds to sleep when no job is available
ENV WORKER_SLEEP=30

# Number of seconds to rest between jobs
ENV WORKER_REST=1

# The number of seconds a child process can run
ENV WORKER_TIMEOUT=600

# Number of times to attempt a job before logging it failed
ENV WORKER_TRIES=3

EXPOSE 80
