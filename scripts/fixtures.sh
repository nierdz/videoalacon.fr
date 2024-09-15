#!/usr/bin/env bash

# To use this script you need to have an export name fixtures.xml
# To create fixtures.xml file use this command:
# wp --allow-root export --stdout --post__in="$(wp --allow-root post list --post_type=post --orderby=rand --posts_per_page=10 --format=ids)" > fixtures.xml

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

FIXTURES_FILE=${FIXTURES_FILE:-./fixtures.xml}

if [[ ! -f "$FIXTURES_FILE" ]]; then
  echo "You need to have fixtures.xml in root directory"
fi

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

# Remove first post
if docker exec -u www-data wordpress \
  wp post exists 1; then
  docker exec -u www-data wordpress \
    wp post delete 1 --force
fi

# Install wordpress-importer and import posts from fixtures.xml
docker exec wordpress \
  wp --allow-root plugin install wordpress-importer --activate
docker cp fixtures.xml wordpress:/tmp/
docker exec -u www-data wordpress \
  wp import /tmp/fixtures.xml --authors=skip

# List all imported posts
POST_LIST=$(sed -nE 's#  <link>(.*)</link>#\1#p' fixtures.xml | tail -n +1)

# Loop through posts to import medias
for post_url in ${POST_LIST}; do
  html=$(curl -s "$post_url")
  post_id=$(echo "$html" | sed -nE 's#.*wp-json/wp/v2/posts/([0-9]*)".*#\1#p')
  post_title=$(echo "$html" | sed -nE 's#.*>(.*)</h1>.*#\1#p')
  image_url=$(echo "$html" | sed -nE 's#.*property="og:image" content="(.*)".*#\1#p')
  video_url=$(echo "$html" | sed -nE 's#.*<source src="(.*)" type="video/mp4.*#\1#p')
  docker exec wordpress \
    wp --allow-root media import \
    "${image_url}" \
    --post_id="$post_id" \
    --title="$post_title" \
    --featured_image
  docker exec wordpress \
    wp --allow-root media import \
    "${video_url}" \
    --post_id="$post_id" \
    --title="$post_title"
done
