#!/bin/sh
# Starts php-fpm and nginx in the background, then runs the container command as
# www-data. The deployments run the queue worker as the command, which is how the
# previous s6-based bkuhl/fpm-nginx image was used.
set -e

php-fpm --daemonize
nginx

exec setpriv --reuid=www-data --regid=www-data --init-groups "$@"
