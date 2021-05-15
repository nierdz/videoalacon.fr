#!/bin/bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

if [[ -n "$(ls -A /docker-entrypoint.d/)" ]]; then
  echo "$0: /docker-entrypoint.d/ is not empty, will attempt to perform configuration"

  echo "$0: Looking for shell scripts in /docker-entrypoint.d/"
  find "/docker-entrypoint.d/" -follow -type f -print | sort -V | while read -r f; do
    case "$f" in
      *.sh)
        if [[ -x "$f" ]]; then
          echo "$0: Launching $f"
          "$f"
        else
          echo "$0: Ignoring $f, not executable"
        fi
        ;;
      *) echo "$0: Ignoring $f" ;;
    esac
  done

  echo "$0: Configuration complete; ready for start up"
else
  echo "$0: No files found in /docker-entrypoint.d/, skipping configuration"
fi

exec "$@"
