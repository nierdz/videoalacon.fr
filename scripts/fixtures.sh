#!/usr/bin/env bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

# Create menu
check_top_menu=$(docker exec \
  -u www-data \
  madrabbit-php-fpm \
  wp menu list \
  --fields=name \
  --format=json \
  | jq -r '.[] | select(.name=="Top Menu") | .name')
if [[ "$check_top_menu" != "Top Menu" ]]; then
  docker exec \
    -u www-data \
    madrabbit-php-fpm \
    wp menu create \
    "Top Menu"
  docker exec \
    -u www-data \
    madrabbit-php-fpm \
    wp menu location assign \
    top-menu primary
fi

# Create categories and add them in menu
for category in Animaux Étonnant Fail Marrant Rage Techno WTF; do
  if ! docker exec \
    -u www-data \
    madrabbit-php-fpm \
      wp term get category \
        --by=slug \
        --fields=id \
        "$category"; then
    category_id=$(docker exec \
      -u www-data \
      madrabbit-php-fpm \
        wp term create category \
          "$category" \
          --porcelain)
    docker exec \
      -u www-data \
      madrabbit-php-fpm \
      wp menu item add-term \
      top-menu category \
      "$category_id"
  fi
done

# Loop around items in feed to populate database
feed=$(curl -s https://mad-rabbit.com/feed/)
echo "$feed" | xmlstarlet sel -t -v "//item/link" | while read -r link; do
  title=$(echo "$feed" | xmlstarlet sel -t -m "//item[link=\"$link\"]" -v title)
  category=$(echo "$feed" | xmlstarlet sel -t -m "//item[link=\"$link\"]" -v "category[1]")
  tags=$(echo "$feed" | xmlstarlet sel -t -m "//item[link=\"$link\"]" -v "category[position()>1]" | tr '\n' ', ')
  content=$(echo "$feed" | xmlstarlet sel -t -m "//item[link=\"$link\"]" -v description)
  html=$(curl -s "$link")
  #video_url=$(echo "$html" | sed -nE '/og:video/s/.*content=\"(.*\.mp4)\" \/>/\1/p')
  image_id=$(echo "$html" | sed -nE '/vthumbs/s/.*vthumbs\/([0-9]{10})\.jpg.*/\1/p')
  post_id=$(docker exec \
    -u www-data \
    madrabbit-php-fpm \
      wp post create \
        --tags_input="$tags" \
        --post_author=1 \
        --post_content="$content" \
        --post_title="$title" \
        --post_status="publish" \
        --post_category="$category" \
        --porcelain)
  echo "post_id: $post_id"
  docker exec \
    -u www-data \
    madrabbit-php-fpm \
      wp media import \
        "https://media.mad-rabbit.com/vthumbs/$image_id.jpg" \
        --post_id="$post_id" \
        --title="$title" \
        --featured_image
done
