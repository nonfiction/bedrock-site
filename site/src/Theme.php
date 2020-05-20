<?php

namespace nf;
use \Timber;

class Theme {

  public function __construct() {

    // Initalize Timber and set the directory
    $timber = new Timber\Timber();
    Timber::$dirname = ['../src/views','../src/blocks'];

    // Support HTML by default
    $this->support( 'html5', ['comment-form','comment-list','search-form','gallery','caption'] );

    // Default context settings
    $this->context( 'site', new Timber\Site() );

    // Path to assets image directory
    $this->context( 'img', home_url() . '/app/site/src/img' );

  }

  // Method config keys:
  // 'clean-head'
  // 'clean-body'
  // 'clean-tags'
  // 'disable-asset-versioning'
  // 'disable-emoji'
  // 'disable-feeds'


  public function config( $key, ...$args ) {

    // Convert key to class method
    $method = "config_" . str_replace( '-', '_', $key );

    // Attempt to call a class method: $this->config_$key()
    if ( method_exists($this, $method) and is_callable([$this, $method]) ) {
      $this->$method( ...$args );
    }

  }


  // Add to theme support
  public function support( $feature, ...$args ) {
    add_action( 'after_setup_theme', function() use($feature, $args) {
      add_theme_support( $feature, ...$args );
    });
  }

  // Add to timber context
  public function context( $key, $value ) {
    add_filter( 'timber/context', function($context) use($key, $value) {

      // If the value is a function, evaluate it and set the value
      if ( is_callable($value) ) {
        $value = ($value)();

      // If the value has a menu name, build a menu and set the value
      } elseif ( (is_array($value)) and (isset($value['menu'])) )  {

        // Build menu and clean classnames
        $menu_name = $value['menu'] ?? '';
        $menu = new Timber\Menu( $menu_name, $value );
        $this->clean_menu_items( $menu->items );
        $value = $menu;
      }

      // Set the value to the context
      $context[$key] = $value;
      return $context;
    });
  }


  // Recursive function for tweaking menu classnames
  private function clean_menu_items( $items ) {
    foreach( $items as $item ) {

      $classes = [ 'menu-' . $item->slug ];
      if ( $item->current ) $classes[] = 'current';
      if ( $item->current_item_parent ) $classes[] = 'parent';
      if ( $item->current_item_ancestor ) $classes[] = 'ancestor';
      if ( $item->current or $item->current_item_parent or $item->current_item_ancestor ) {
        $classes[] = 'open';
      }
      $item->classes = $classes;
      $item->class = implode( ' ', $classes );

      if ( $item->children ) {
        $this->clean_menu_items( $item->children );
      }
    }
  }

  public function twig( $callback ) {
    add_filter( 'timber/twig', function( $twig ) use( $callback ) {
      if ( is_callable($callback) ) {
        return ($callback)($twig);
      }
    });
  }

  public function extension( $value ) {
    add_filter( 'timber/twig', function( $twig ) use( $value ) {
      $twig->addExtension($value);
      return $twig;
    });
  }

  public function filter( $value ) {
    add_filter( 'timber/twig', function( $twig ) use( $value ) {
      $twig->addFilter($value);
      return $twig;
    });
  }


  // Clean up the extra stuff WP adds to the <body>
  private function config_clean_body() {

    // Skip in admin but allow in ajax
    if ((is_admin() && !wp_doing_ajax())) return;

    // Add and remove body_class() classes
    add_filter('body_class', function($classes){

      // Add post/page slug if not present
      if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
          $classes[] = basename(get_permalink());
        }
      }

      // Remove unnecessary classes
      $classes = array_diff($classes, [
        'post-template-default',
        'page-template-default',
        'page-id-' . get_option('page_on_front')
      ]);

      return $classes;

    });
  }


  // Clean up the extra stuff WP adds to the <head>
  private function config_clean_head() {

    // Skip in admin but allow in ajax
    if ((is_admin() && !wp_doing_ajax())) return;

    add_action('init', function() {

      remove_action('wp_head', 'rsd_link'); // remove really simple discovery link
      remove_action('wp_head', 'wlwmanifest_link'); // remove wlwmanifest.xml (needed to support windows live writer)
      remove_action('wp_head', 'index_rel_link'); // remove link to index page
      remove_action('wp_head', 'start_post_rel_link', 10, 0); // remove random post link
      remove_action('wp_head', 'parent_post_rel_link', 10, 0); // remove parent post link
      remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // remove the next and previous post links
      remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
      remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
      remove_action('wp_head', 'wp_oembed_add_discovery_links');
      remove_action('wp_head', 'wp_oembed_add_host_js');
      remove_action('wp_head', 'rest_output_link_wp_head', 10); // remove api link

      remove_action('wp_head', 'wp_generator'); // remove wordpress version
      add_filter('the_generator', '__return_false'); // remove wordpress version from rss feeds
      add_filter('use_default_gallery_style', '__return_false');
      add_filter('show_recent_comments_widget_style', '__return_false');

    });
  }


  // Clean up the extra stuff WP adds to certain tags
  private function config_clean_tags() {

    // Skip in admin but allow in ajax
    if ((is_admin() && !wp_doing_ajax())) return;

    // Clean up output of stylesheet <link> tags
    add_filter('style_loader_tag', function($input){
      preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
      if (empty($matches[2])) {
        return $input;
      }
      // Only display media if it is meaningful
      $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
      return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
    });

    // Clean up output of <script> tags
    add_filter('script_loader_tag', function($input){
      $input = str_replace("type='text/javascript' ", '', $input);
      $input = \preg_replace_callback(
        '/document.write\(\s*\'(.+)\'\s*\)/is',
        function ($m) {
          return str_replace($m[1], addcslashes($m[1], '"'), $m[0]);
        },
        $input
      );
      return str_replace("'", '"', $input);
    });

    // <img /> <input />
    foreach(['get_avatar', 'comment_id_fields', 'post_thumbnail_html'] as $filter) {
      add_filter($filter, function($input) {
        return str_replace(' />', '>', $input);
      });
    }
  }


  // Remove the query string from scripts and styles
  private function config_disable_asset_versioning() {

    // Skip in admin but allow in ajax
    if ((is_admin() && !wp_doing_ajax())) return;

    foreach( ['script_loader_src','style_loader_src'] as $tag ) {
      add_filter( $tag, function($src) {
        return $src ? esc_url( remove_query_arg( 'ver', $src ) ) : false;
      }, 15, 1 );
    }
  }


  // Remove WP emoji
  private function config_disable_emoji() {

    // Skip in admin but allow in ajax
    if ((is_admin() && !wp_doing_ajax())) return;

    add_action('init', function() {

      remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); 
      remove_action( 'admin_print_scripts', 'print_emoji_detection_script' ); 
      remove_action( 'wp_print_styles', 'print_emoji_styles' ); 
      remove_action( 'admin_print_styles', 'print_emoji_styles' );
      remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
      remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
      remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
      add_filter( 'emoji_svg_url', '__return_false' );

    });
  }


  // Remove feeds from the <head>
  private function config_disable_feeds() {

    // Skip in admin but allow in ajax
    if ((is_admin() && !wp_doing_ajax())) return;

    add_action('init', function() {

      remove_action('wp_head', 'feed_links', 2); // remove rss feed links
      remove_action('wp_head', 'feed_links_extra', 3); // removes all extra rss feed links

      // remove comments feed
      add_action('wp_head', 'ob_start', 1, 0);
      add_action('wp_head', function () {
        $pattern = '/.*' . preg_quote(esc_url(get_feed_link('comments_' . get_default_feed())), '/') . '.*[\r\n]+/';
        echo preg_replace($pattern, '', ob_get_clean());
      }, 3, 0);

    });
  }


}
