#!/bin/bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

POSTER=${POSTER:=}
POST_ID=${POST_ID:=}
URL=${URL:=}
TIMESTAMP=${TIMESTAMP:=}
TITLE=${TITLE:=}
TYPE=${TYPE:=}
WORKING_DIR=$(mktemp -d /dev/shm/media_importer.XXXXXX)
WP_BIN=${WP_BIN:="/usr/bin/wp"}
YT_BIN=${YT_BIN:="/usr/bin/yt-dlp"}

clean() {
  if [[ -d "${WORKING_DIR}" ]]; then
    rm -rf "${WORKING_DIR}"
  fi
}

trap clean INT QUIT TERM EXIT

if [[ -z ${POST_ID} || -z ${URL} || -z ${TIMESTAMP} || -z ${TITLE} ]]; then
  exit 1
fi

if [[ ${TYPE} != @(video|image) ]]; then
  exit 1
fi

# Needed to read wp-cli.yml
pushd /var/www/bedrock

if [[ ${TYPE} == "video" ]]; then
  ${YT_BIN} \
    --format "best[ext=mp4]" \
    --no-embed-metadata \
    --cache-dir "${WORKING_DIR}" \
    --output "${WORKING_DIR}/${TIMESTAMP}.mp4" \
    "${URL}"

  ${WP_BIN} \
    media import \
    "${WORKING_DIR}/${TIMESTAMP}.mp4" \
    --post_id="${POST_ID}" \
    --title="${TITLE}"
fi

if [[ ${TYPE} == "image" ]]; then
  POSTER_URL=$(
    ${YT_BIN} \
      --skip-download \
      --cache-dir "/dev/shm" \
      --print thumbnail \
      "${URL}"
  )
  curl -o "${WORKING_DIR}/${TIMESTAMP}.jpg" "${POSTER_URL}"
  ${WP_BIN} \
    media import \
    "${WORKING_DIR}/${TIMESTAMP}.jpg" \
    --post_id="${POST_ID}" \
    --title="${TITLE}" \
    --featured_image
fi
