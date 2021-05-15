#!/usr/bin/env bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

ROOT_URL="https://mad-rabbit.com"

# Create menu
check_top_menu=$(docker exec \
  -u www-data \
  wordpress \
  wp menu list \
  --fields=name \
  --format=json |
  jq -r '.[] | select(.name=="Top Menu") | .name')
if [[ "$check_top_menu" != "Top Menu" ]]; then
  docker exec \
    -u www-data \
    wordpress \
    wp menu create \
    "Top Menu"
  docker exec \
    -u www-data \
    wordpress \
    wp menu location assign \
    top-menu primary
fi

# Create categories and add them in menu
for category in Animaux Étonnant Fail Marrant Rage Techno WTF; do
  if ! docker exec \
    -u www-data \
    wordpress \
    wp term get category \
    --by=slug \
    --fields=id \
    "$category"; then
    category_id=$(docker exec \
      -u www-data \
      wordpress \
      wp term create category \
      "$category" \
      --porcelain)
    docker exec \
      -u www-data \
      wordpress \
      wp menu item add-term \
      top-menu category \
      "$category_id"
  fi
done

# Loop through posts from API v2
posts_json=$(curl --silent --location --request GET "$ROOT_URL/wp-json/wp/v2/posts" | jq -c '.[]')
while read -r post_json; do
  date=$(echo "$post_json" | jq --raw-output '.date')
  link=$(echo "$post_json" | jq --raw-output '.link')
  content=$(echo "$post_json" | jq --raw-output '.content.rendered')
  title=$(echo "$post_json" | jq --raw-output '.title.rendered')
  category_json_url=$(echo "$post_json" | jq --raw-output '._links."wp:term"[0].href')
  category_json=$(curl --silent --location --request GET "$category_json_url" | jq '.[]')
  category=$(echo "$category_json" | jq --raw-output '.name')
  tags_json_url=$(echo "$post_json" | jq --raw-output '._links."wp:term"[1].href')
  tags_json=$(curl --silent --location --request GET "$tags_json_url" | jq -c '.[]')
  tags_array=()
  while read -r tag_json; do
    tag_slug=$(echo "$tag_json" | jq --raw-output '.slug')
    tags_array+=("$tag_slug")
  done <<<"$tags_json"
  html=$(curl -s "$link")
  #video_url=$(echo "$html" | sed -nE '/og:video/s/.*content=\"(.*\.mp4)\" \/>/\1/p')
  image_id=$(echo "$html" | sed -nE '/vthumbs/s/.*vthumbs\/([0-9]{10})\.jpg.*/\1/p')
  tags=$(printf ",%s" "${tags_array[@]}")
  tags=${tags:1}
  post_id=$(docker exec \
    -u www-data \
    wordpress \
    wp post create \
    --post_date="$date" \
    --tags_input="$tags" \
    --post_author=1 \
    --post_content="$content" \
    --post_title="$title" \
    --post_status="publish" \
    --post_category="$category" \
    --porcelain)
  docker exec \
    -u www-data \
    wordpress \
    wp media import \
    "https://media.mad-rabbit.com/vthumbs/$image_id.jpg" \
    --post_id="$post_id" \
    --title="$title" \
    --featured_image
done <<<"$posts_json"
