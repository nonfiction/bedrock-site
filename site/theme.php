<?php
/*
 * Theme Name: Website Theme
 * Text Domain: theme
 * Description: Nonfiction template website theme
 * Author: nonfiction studios
 * Version: 1.0
*/

$theme = new nf\Theme();
$theme->config( 'clean-head' );
$theme->config( 'clean-body' );
$theme->config( 'clean-tags' );
$theme->config( 'disable-asset-versioning' );
$theme->config( 'disable-feeds' );

// Add theme support for...
$theme->support( 'menus' );
$theme->support( 'automatic-feed-links' );
$theme->support( 'title-tag' );
$theme->support( 'post-thumbnails' );
$theme->support( 'align-wide' );

// Make variables available in view...
$theme->context( 'something', 'value' );
$theme->context( 'menu', [ 'menu' => 'Primary', 'depth' => 2 ] );

// Extend twig...
$theme->extension( new \Twig_Extension_StringLoader() );
$theme->filter( new \Twig_SimpleFilter( 'myfoo', function( $text ) {
	$text .= ' bar!';
	return $text;
}) );
