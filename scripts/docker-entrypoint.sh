#!/bin/bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

if ! /usr/bin/wp core --allow-root is-installed; then
  /usr/bin/wp \
    core install \
    --allow-root \
    --url="$WP_HOME" \
    --title="Vidéos à la con de l'internet" \
    --admin_user="valc" \
    --admin_password="${VALC_PASSWORD}" \
    --admin_email="nierdz@example.com" \
    --skip-email
fi

if ! /usr/bin/wp --allow-root theme is-active valc; then
  /usr/bin/wp \
    theme activate \
    --allow-root \
    valc
fi

if ! wp --allow-root language core is-installed fr_FR; then
  /usr/bin/wp \
    language core install \
    --allow-root \
    fr_FR
fi
/usr/bin/wp \
  site switch-language \
  --allow-root \
  fr_FR
/usr/bin/wp \
  language core \
  --allow-root \
  update

echo "$0: Configuration complete; ready for start up"

exec "$@"
