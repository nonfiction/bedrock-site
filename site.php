<?php
/*
Plugin Name: site
Description: Nonfiction's configuration for this website
Author: nonfiction studios
Version: 1.0
*/

// Apply all Wordpress tweaks in this plugin
foreach (glob(__DIR__ . '/tweaks/*.php') as $file) {
  require_once $file;
}

// Load theme directory inside this plugin
// $t = __DIR__ . '/theme/';

// echo $t;
if (file_exists(__DIR__)) {
  echo "YES!!";
}
echo "\n";
echo __DIR__ . ' <------';
echo "\n";
echo WP_CONTENT_DIR . ' <------';
echo "\n";

add_action('init', function() {
register_theme_directory( __DIR__ );
});
// register_theme_directory( 'theme' );

// Register Custom Post Types
nf\CaseStudy::register();
nf\Service::register();
