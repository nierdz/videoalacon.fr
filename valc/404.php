<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package valc
 */

wp_safe_redirect( esc_url( home_url( '/404-page-introuvable/' ) ), 301 );
exit();
