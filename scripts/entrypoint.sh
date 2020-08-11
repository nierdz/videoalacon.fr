#!/usr/bin/env bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

if ! runuser - www-data -s /bin/bash -c "/usr/local/bin/wp core is-installed"; then
  runuser - www-data -s /bin/bash -c "
    /usr/local/bin/wp \
      --allow-root \
      core install \
      --title=\"Vidéos à la con de l'internet\" \
      --admin_user=\"tmtk\" \
      --admin_password=\"${TMTK_PASSWORD}\" \
      --admin_email=\"nierdz@example.com\" \
      --skip-email"
fi

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
  set -- php-fpm "$@"
fi

exec "$@"
