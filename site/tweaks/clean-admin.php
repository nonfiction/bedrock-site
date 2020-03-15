<?php
namespace nf;

// https://github.com/soberwp/intervention
use function \Sober\Intervention\intervention;
if (function_exists('\Sober\Intervention\intervention')) {
  add_action('init', function() {

    intervention('add-svg-support');
    intervention('remove-emoji');
    intervention('remove-help-tabs');
    intervention('remove-howdy');
    intervention('remove-update-notices', 'all-not-admin');
    intervention('remove-widgets');
    intervention('update-pagination', 100);
    // intervention('remove-taxonomies', ['tag', 'category']);
    intervention('update-label-footer', '<span id="footer-thankyou">handcrafted by <a href="https://www.nonfiction.ca" target="_blank">nonfiction studios</a></span>');

    intervention('remove-toolbar-frontend', ['all-not-admin']);
    intervention('remove-toolbar-items', ['logo', 'updates', 'comments', 'new-media', 'new-user', 'themes'], 'all');

    intervention('remove-user-fields', ['options', 'names', 'contact'], ['editor', 'author']);
    intervention('remove-user-roles', ['subscriber', 'contributor']);

    intervention('remove-page-components', [
      'author', 
      'custom-fields', 
      'comments',
      'trackbacks'
    ]);
    intervention('remove-post-components', [
      'custom-fields', 
      'comments', 
      'trackbacks'
    ]);

    intervention('update-dashboard-columns', 1);
    intervention('remove-dashboard-items', [
      'activity',
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
  
    // These cause AJAX error (in dev mode)
    intervention('add-dashboard-redirect', 'admin.php?page=wp_stream', 'all'); 
    intervention('remove-menu-items', [
      'dashboard', 
      'themes', 
      'media', 
      'comments',
    ], 'all');

    intervention('add-menu-page', [
      'page_title'    => 'Media',
      'menu_title'    => 'Media',
      'menu_slug'     => 'upload.php',
      'function'      => '',
      'icon_url'      => 'admin-media',
      'position'      => 60
    ], 'all');

    intervention('add-menu-page', [
      'page_title'    => 'Menus',
      'menu_title'    => 'Menus',
      'menu_slug'     => 'nav-menus.php',
      'function'      => '',
      'icon_url'      => 'menu',
      'position'      => 62
    ], 'all');

  });
}
