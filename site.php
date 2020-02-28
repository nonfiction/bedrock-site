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
register_theme_directory( __DIR__ );

// Register Custom Post Types
nf\CaseStudy::register();
nf\Service::register();

// var_dump($_ENV);
