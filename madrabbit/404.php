<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package madrabbit
 */

header( 'HTTP/1.1 301 Moved Permanently' );
header( 'Location: ' . get_bloginfo( 'url' ) . '/404-page-introuvable/' );
exit();
