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
  if [[ ${URL} == *koreus.com* ]]; then
    URL=$(curl --silent -L "${URL}" | grep -m 1 -oE "https.*embed.koreus.*\.mp4")
    curl \
      --user-agent "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" \
      -o "${WORKING_DIR}/${TIMESTAMP}.mp4" \
      "${URL}"
  else
    ${YT_BIN} \
      --format "best[ext=mp4]" \
      --user-agent "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" \
      --no-embed-metadata \
      --cache-dir "${WORKING_DIR}" \
      --output "${WORKING_DIR}/${TIMESTAMP}.mp4" \
      "${URL}"
  fi

  ${WP_BIN} \
    media import \
    "${WORKING_DIR}/${TIMESTAMP}.mp4" \
    --post_id="${POST_ID}" \
    --title="${TITLE}"
fi

if [[ ${TYPE} == "image" ]]; then
  if [[ -z ${POSTER} ]]; then
    if [[ ${URL} == *koreus.com* ]]; then
      POSTER_URL=$(curl --silent -L "${URL}" | grep -m 1 -oE "https.*cdn.koreus.com/thumbshigh/.*\.jpg")
    else
      POSTER_URL=$(
        ${YT_BIN} \
          --user-agent "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" \
          --skip-download \
          --cache-dir "/dev/shm" \
          --print thumbnail \
          "${URL}"
      )
    fi
    curl \
      --user-agent "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" \
      -o "${WORKING_DIR}/${TIMESTAMP}.jpg" \
      "${POSTER_URL}"
  else
    date=$(
      ${WP_BIN} \
        post get \
        "${POST_ID}" \
        --field="post_date"
    )
    ffmpeg \
      -ss "00:${POSTER}" \
      -i "/var/www/bedrock/web/app/uploads/$(date -d "${date}" '+%Y')/$(date -d "${date}" '+%m')/${TIMESTAMP}.mp4" \
      -frames:v 1 \
      "${WORKING_DIR}/${TIMESTAMP}.jpg"
  fi
  ${WP_BIN} \
    media import \
    "${WORKING_DIR}/${TIMESTAMP}.jpg" \
    --post_id="${POST_ID}" \
    --title="${TITLE}" \
    --featured_image
fi
