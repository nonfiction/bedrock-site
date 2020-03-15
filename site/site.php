<?php
/*
Plugin Name: Website Plugin & Theme
Description: Nonfiction's configuration for this website
Author: nonfiction studios
Version: 1.0
*/

// Apply all Wordpress tweaks in this plugin
foreach (glob( __DIR__ . '/tweaks/*.php' ) as $file) {
  require_once $file;
}

// Load theme directory inside this plugin
register_theme_directory( __DIR__ );

// Include script and styles bundled by webpack
add_action('wp_enqueue_scripts', function () {

  // Load the compiled manifest.json to get the vendor and site objects
  $manifest = json_decode(file_get_contents( __DIR__ . '/dist/manifest.json', true ));

  // First enqueue vendor assets
  if (isset($manifest->vendor->css)) wp_enqueue_style( 'vendor', home_url() . $manifest->vendor->css, false, null, 'all' );
  if (isset($manifest->vendor->js)) wp_enqueue_script( 'vendor', home_url() . $manifest->vendor->js, false, null,  true );

  // Last enqueue site assets
  if (isset($manifest->site->css)) wp_enqueue_style( 'site', home_url() . $manifest->site->css, ['vendor'], null, 'all' );
  if (isset($manifest->site->js)) wp_enqueue_script( 'site', home_url() . $manifest->site->js, ['vendor'], null,  true );

}, 100);

// Register Custom Post Types
nf\CaseStudy::register();
nf\Service::register();
