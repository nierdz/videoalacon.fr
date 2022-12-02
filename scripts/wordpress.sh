#!/usr/bin/env bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

if ! runuser - www-data -s /bin/bash -c "/usr/local/bin/wp core is-installed"; then
  runuser - www-data -s /bin/bash -c "
    /usr/local/bin/wp \
      core install \
      --url=\"$WP_HOME\" \
      --title=\"Vidéos à la con de l'internet\" \
      --admin_user=\"madrabbit\" \
      --admin_password=\"${TMTK_PASSWORD}\" \
      --admin_email=\"nierdz@example.com\" \
      --skip-email"
fi

if ! runuser - www-data -s /bin/bash -c "/usr/local/bin/wp theme is-active madrabbit"; then
  runuser - www-data -s /bin/bash -c "
    /usr/local/bin/wp \
    theme \
    activate madrabbit"
fi

runuser - www-data -s /bin/bash -c "
  /usr/local/bin/wp \
    language core \
    install fr_FR"
runuser - www-data -s /bin/bash -c "
  /usr/local/bin/wp \
    language core \
    activate fr_FR"
runuser - www-data -s /bin/bash -c "
  /usr/local/bin/wp \
    language core \
    update"
