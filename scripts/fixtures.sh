#!/usr/bin/env bash

# To use this script you need to have an export name fixtures.xml
# To create fixtures.xml file use this command:
# wp export --dir=/tmp/ --post_type=post --start_id=19519 --post_type=post

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
docker exec -u www-data wordpress \
  wp plugin install wordpress-importer --activate
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
  image_id=$(echo "$html" | sed -nE 's#.*vthumbs/([0-9]{10})\.jpg.*#\1#p')
  docker exec -u www-data wordpress \
    wp media import \
    "https://media.videoalacon.fr/vthumbs/$image_id.jpg" \
    --post_id="$post_id" \
    --title="$post_title" \
    --featured_image
  docker exec -u www-data wordpress \
    wp media import \
    "https://media.videoalacon.fr/videos/$image_id.mp4" \
    --post_id="$post_id" \
    --title="$post_title"
done
