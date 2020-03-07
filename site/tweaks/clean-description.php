<?php

namespace nf;

// Skip in admin but allow in ajax
if ((is_admin() && !wp_doing_ajax())) return;

// Don't return default description
add_action('init', function() {

  add_filter('get_bloginfo_rss', function($bloginfo){
    $default_tagline = 'Just another WordPress site';
    return ($bloginfo === $default_tagline) ? '' : $bloginfo;
  });

});
