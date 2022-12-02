#!/bin/bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

if ! "/usr/bin/wp core --allow-root is-installed"; then
  /usr/bin/wp \
    core install \
    --allow-root \
    --url="$WP_HOME" \
    --title="Vidéos à la con de l'internet" \
    --admin_user="madrabbit" \
    --admin_password="${TMTK_PASSWORD}" \
    --admin_email="nierdz@example.com" \
    --skip-email
fi

if ! "/usr/bin/wp theme is-active madrabbit"; then
  /usr/bin/wp \
    theme activate \
    --allow-root \
    madrabbit
fi

/usr/bin/wp \
  language core \
  --allow-root \
  install fr_FR
/usr/bin/wp \
  language core \
  --allow-root \
  activate fr_FR
/usr/bin/wp \
  language core \
  --allow-root \
  update

echo "$0: Configuration complete; ready for start up"

exec "$@"
