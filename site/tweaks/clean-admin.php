<?php
namespace nf;

// https://github.com/soberwp/
use function \Sober\Intervention\intervention;
if (function_exists('\Sober\Intervention\intervention')) {
  add_action('init', function() {

    intervention('remove-dashboard-items', [
      // 'activity',
      'drafts',
      'incoming-links', 
      'news',
      'notices',
      'plugins', 
      'quick-draft', 
      'recent-comments', 
      'right-now',
      'welcome', 
    ], 'all');

    // intervention('add-dashboard-item', [
    //   'title',
    //   'body',
    // ]);
  
    // This causes AJAX error
    intervention('add-dashboard-redirect', 'admin.php?page=wp_stream', 'all');
    intervention('remove-menu-items', [
      'dashboard', 
      'media', 
      'themes', 
      'comments',
    ], 'all');

    intervention('add-svg-support');
    intervention('remove-emoji');
    intervention('remove-help-tabs');
    intervention('remove-howdy');

    // intervention('remove-menu-items', ['themes', 'plugins'], ['editor', 'author']);
    
    intervention('remove-page-components', ['author', 'custom-fields', 'comments']);
    intervention('remove-post-components', ['custom-fields', 'comments', 'trackbacks']);

    // intervention('remove-taxonomies', ['tag', 'category']);
    intervention('remove-toolbar-frontend', ['all-not-admin']);
    intervention('remove-toolbar-items', ['logo', 'updates', 'comments', 'new-media', 'new-user', 'themes'], ['all']);
    intervention('remove-user-fields', ['options', 'names', 'contact'], ['editor', 'author']);
    intervention('remove-user-roles', ['subscriber', 'contributor']);
    intervention('remove-update-notices', ['editor', 'author']);
    intervention('remove-widgets', ['calendar', 'rss']);
    intervention('update-dashboard-columns', 1);

    // intervention('update-label-page', ['Content', 'Content', 'smiley']);
    // intervention('update-label-post', ['Books', 'Book', 'book']);
    
    intervention('update-pagination', 100);

    intervention('update-label-footer', '<span id="footer-thankyou">handcrafted by <a href="https://www.nonfiction.ca" target="_blank">nonfiction studios</a></span>');

    // This causes AJAX error

    intervention('add-menu-page', [
      'page_title'    => 'Media',
      'menu_title'    => 'Media',
      'menu_slug'     => 'upload.php',
      'function'      => '',
      'icon_url'      => 'admin-media',
      'position'      => 60
    ], ['all']);

    intervention('add-menu-page', [
      'page_title'    => 'Menus',
      'menu_title'    => 'Menus',
      'menu_slug'     => 'nav-menus.php',
      'function'      => '',
      'icon_url'      => 'menu',
      'position'      => 62
    ], ['all']);

  });
}
