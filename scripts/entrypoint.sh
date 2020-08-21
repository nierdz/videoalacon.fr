#!/usr/bin/env bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

BEDROCK_DIR="/var/www/bedrock"

if ! runuser - www-data -s /bin/bash -c "cd $BEDROCK_DIR && /usr/local/bin/wp core is-installed"; then
  runuser - www-data -s /bin/bash -c "
    cd $BEDROCK_DIR && \
    /usr/local/bin/wp \
      core install \
      --title=\"Vidéos à la con de l'internet\" \
      --admin_user=\"tmtk\" \
      --admin_password=\"${TMTK_PASSWORD}\" \
      --admin_email=\"nierdz@example.com\" \
      --skip-email"
fi

if ! runuser - www-data -s /bin/bash -c "cd $BEDROCK_DIR && /usr/local/bin/wp theme is-active madrabbit"; then
  runuser - www-data -s /bin/bash -c "
    cd $BEDROCK_DIR && \
    /usr/local/bin/wp \
    theme \
    activate madrabbit"
fi

rm -rf "$BEDROCK_DIR/web/wp/wp-content/themes/"
rm -rf "$BEDROCK_DIR/web/wp/wp-content/plugins/"

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
  set -- php-fpm "$@"
fi

exec "$@"
