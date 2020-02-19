<?php

namespace nf;
use function Sober\Intervention\intervention;
return;

// https://github.com/soberwp/
add_action('init', function() {

  // welcome, notices, activity, right-now, recent-comments, incoming-links, plugins, quick-draft, drafts, news
  // intervention('remove-dashboard-items', ['right-now', 'activity'], ['admin', 'editor']);
  intervention('remove-dashboard-items', ['welcome', 'recent-comments', 'incoming-links', 'plugins', 'quick-draft', 'news']);
  intervention('add-dashboard-redirect', 'pages', ['editor', 'author']);
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
  intervention('remove-update-notices', ['editor', 'author']);
  intervention('remove-user-fields', ['options', 'names', 'contact'], ['editor', 'author']);
  intervention('remove-user-roles', ['subscriber', 'contributor']);
  intervention('remove-widgets', ['calendar', 'rss']);
  intervention('update-dashboard-columns', 2);
  // intervention('update-label-page', ['Content', 'Content', 'smiley']);
  // intervention('update-label-post', ['Books', 'Book', 'book']);
  intervention('update-pagination', 100);

  intervention('update-label-footer', '<span id="footer-thankyou">handcrafted by <a href="https://www.nonfiction.ca" target="_blank">nonfiction studios</a></span>');
  intervention('remove-menu-items', ['media', 'themes', 'comments'], ['all']);

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
