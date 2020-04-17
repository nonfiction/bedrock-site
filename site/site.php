<?php
/*
Plugin Name: Website Plugin & Theme
Description: Nonfiction's configuration for this website
Author: nonfiction studios
Version: 1.0
*/

$site = new nf\Site();

// Load block/post types
$site->load( __DIR__ . '/src/blocks/*/index.php'); 
$site->load( __DIR__ . '/src/posts/*.php'); 

// Load the manifest.json to register/enqueue webpack assets
$site->assets( __DIR__ . '/dist/manifest.json' );

// Configuration
$site->config( 'branded-login' );
$site->config( 'branded-footer' );
$site->config( 'nice-search' );
$site->config( 'relative-urls' );
$site->config( 's3-uploads' );
$site->config( 'add-svg-support' );
$site->config( 'remove-help-tabs' );
$site->config( 'remove-update-notices', 'all-not-admin' );
$site->config( 'remove-widgets' );
$site->config( 'update-pagination', 100 );
// $site->config( 'remove-taxonomies', ['tag', 'category'] );
$site->config( 'remove-toolbar-frontend', ['all-not-admin'] );
$site->config( 'remove-toolbar-items', ['logo', 'updates', 'comments', 'new-media', 'new-user', 'themes'], 'all' );
$site->config( 'remove-user-fields', ['options', 'names', 'contact'], ['editor', 'author'] );
$site->config( 'remove-user-roles', ['subscriber', 'contributor'] );
$site->config( 'remove-page-components', [ 'author', 'custom-fields', 'comments', 'trackbacks' ]);
$site->config( 'remove-post-components', [ 'custom-fields', 'comments', 'trackbacks' ]);
// $site->config( 'update-dashboard-columns', 1 );
// $site->config( 'remove-dashboard-items', [ 'activity', 'drafts', 'incoming-links', 'news', 'notices', 'plugins', 'quick-draft', 'recent-comments', 'right-now', 'welcome', ], 'all' );
// $site->config( 'add-dashboard-item', [ 'title', 'body' ]);
$site->config( 'add-dashboard-redirect', 'admin.php?page=wp_stream', 'all' );
$site->config( 'remove-menu-items', [ 'dashboard', /* 'themes', */ 'media', 'comments', ], 'all' );
$site->config( 'add-menu-page-media' );
$site->config( 'add-menu-page-menus' );
