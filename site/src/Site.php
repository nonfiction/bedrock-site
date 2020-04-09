<?php

namespace nf;
use function \Sober\Intervention\intervention;

class Site {

	public $assets = [];
	public $post_types = [];
	public $block_types = [];

	public function register_assets( $manifest_path ) {
		$this->assets = new Assets( $manifest_path );
	}

	public function register_post_type( $args ) {
		$this->post_types[] = new PostType( $args );
	}

	public function register_block_type( $args ) {
		$this->block_types[] = new BlockType( $args );
	}


  public function __construct() {

		// Load theme directory inside this plugin
		register_theme_directory( dirname( __DIR__, 1 ) );

		// Always disable crawlers while in development mode, and don't say howdy
		$this->config( 'disable-dev-crawlers' );
		$this->config( 'remove-howdy' );

  }

	private $agency_name = 'nonfiction studios';
	private $agency_url = 'https://www.nonfiction.ca/';
	
	// Method config keys:
	// 'add-menu-page-media' 
	// 'add-menu-page-menus' 
	// 'branded-login'
	// 'branded-footer'
  // 'disable-dev-crawlers'
  // 'nice-search'
  // 'relative-urls'
  // 's3-uploads'

	// Extended config keys:
	private $interventions = [
		'add-acf-page', 
		'add-dashboard-item', 
		'add-dashboard-redirect', 
		'add-menu-page', 
		'add-svg-support', 
		'remove-customizer-items', 
		'remove-dashboard-items', 
		'remove-emoji', 
		'remove-help-tabs', 
		'remove-howdy', 
		'remove-menu-items', 
		'remove-page-components', 
		'remove-post-components', 
		'remove-taxonomies', 
		'remove-toolbar-frontend', 
		'remove-toolbar-items', 
		'remove-update-notices', 
		'remove-user-fields', 
		'remove-user-roles', 
		'remove-widgets', 
		'update-dashboard-columns', 
		'update-label-footer', 
		'update-label-page', 
		'update-label-post', 
		'update-pagination', 
	];


	public function config( $key, ...$args ) {

		// Convert key to class method
		$method = "config_" . str_replace( '-', '_', $key );

		// First, attempt to call an Intervention module
		// https://github.com/soberwp/intervention
		if ( in_array( $key, $this->interventions ) and function_exists('\Sober\Intervention\intervention') ) {
			add_action('init', function() use( $key, $args ) {
				intervention( $key, ...$args );
			});

		// Last, attempt to call a class method: $this->config_$key()
		} elseif ( method_exists($this, $method) and is_callable([$this, $method]) ) {
			$this->$method( ...$args );
		}

	}


  private function config_branded_login() {
    add_action( 'login_enqueue_scripts', function() {
      echo "<style type='text/css'>\n";
      echo "  #login h1 a, .login h1 a {\n";
      echo "    background-image: url(" . get_stylesheet_directory_uri() . "/logo.jpg);\n";
      echo "    background-size: contain;\n";
      echo "    width: 150px;\n";
      echo "    height: 150px;\n";
      echo "    border-radius: 50%;\n";
      echo "  }\n";
      echo "</style>";
    });
    add_filter( 'login_headerurl', function() { return $this->agency_url; });
    add_filter( 'login_headertext', function() { return $this->agency_name; });
  }


	private function config_branded_footer() {
		$footer  = "<span id='footer-thankyou'>handcrafted by ";
		$footer .=   "<a href='$this->agency_url' target='_blank'>";
		$footer .=     $this->agency_name;
		$footer .=   "</a>";
		$footer .= "</span>";
		$this->config( 'update-label-footer', $footer);
	}


	private function config_add_menu_page_media() {
		$this->config( 'add-menu-page', [
			'page_title'    => 'Media',
			'menu_title'    => 'Media',
			'menu_slug'     => 'upload.php',
			'function'      => '',
			'icon_url'      => 'admin-media',
			'position'      => 60
		], 'all' );
	}


	private function config_add_menu_page_menus() {
		$this->config( 'add-menu-page', [
			'page_title'    => 'Menus',
			'menu_title'    => 'Menus',
			'menu_slug'     => 'nav-menus.php',
			'function'      => '',
			'icon_url'      => 'menu',
			'position'      => 62
		], 'all' );
	}


  // Disallow crawlers unless production
  private function config_disable_dev_crawlers() {
    if (defined('WP_ENV') && WP_ENV !== 'production' && !is_admin()) {
      add_action('pre_option_blog_public', '__return_zero');
    }
  }
  


  // Redirects search results from /?s=query to /search/query/, converts %20 to +
  private function config_nice_search() {

    add_action('template_redirect', function() {

      global $wp_rewrite;
      if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->get_search_permastruct()) {
        return;
      }

      $search_base = $wp_rewrite->search_base;
      if (is_search() && !is_admin() && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false && strpos($_SERVER['REQUEST_URI'], '&') === false) {
        wp_redirect(get_search_link());
        exit();
      }

    });

    add_filter('wpseo_json_ld_search_url', function($url) {
      return str_replace('/?s=', '/search/', $url);
    });
  }


  // Relative URLs
  private function config_relative_urls() {

    // Skip in admin but allow in ajax
    if ((is_admin() && !wp_doing_ajax())) return;

    // Skip in sitemap
    if (isset($_GET['sitemap'])) return;

    // Skip login/register screens
    if (in_array(($GLOBALS['pagenow'] ?? ''), ['wp-login.php', 'wp-register.php'])) return;

    foreach([
      'bloginfo_url',
      'the_permalink',
      'wp_list_pages',
      'wp_list_categories',
      'wp_get_attachment_url',
      'the_content_more_link',
      'the_tags',
      'get_pagenum_link',
      'get_comment_link',
      'month_link',
      'day_link',
      'year_link',
      'term_link',
      'the_author_posts_link',
      'script_loader_src',
      'style_loader_src',
      'theme_file_uri',
      'parent_theme_file_uri',
    ] as $tag) {
      add_filter($tag, [$this,'make_link_relative'], 10, 1); 
    }

    add_filter('wp_calculate_image_srcset', function ($sources) {
      foreach ((array) $sources as $source => $src) {
        $sources[$source]['url'] = $this->make_link_relative($src['url']);
      }
      return $sources;
    });

    // Compatibility with The SEO Framework
    add_action('the_seo_framework_do_before_output', function () {
      remove_filter('wp_get_attachment_url', [$this, 'make_link_relative']);
    });

    add_action('the_seo_framework_do_after_output', function () {
      add_filter('wp_get_attachment_url', [$this, 'make_link_relative']);
    });

  }


  // Used by the method above
  public function make_link_relative($input) {

    // Will be comparing input to home url
    $site_url = parse_url(network_home_url());

    // Get URL from input
    $url = parse_url($input);
    $url['scheme'] = $url['scheme'] ?? $site_url['scheme'];

    // Leave feeds alone
    if (is_feed()) return $input;

    // Ensure it's a valid url
    if (!isset($url['host']) || !isset($url['path'])) return $input;

    // See if input url matches properly with home url
    $hosts_match = $site_url['host'] === $url['host'];
    $schemes_match = $site_url['scheme'] === $url['scheme'];
    $ports_exist = isset($site_url['port']) && isset($url['port']);
    $ports_match = ($ports_exist) ? $site_url['port'] === $url['port'] : true;

    // If so, return the relative version
    if ($hosts_match && $schemes_match && $ports_match) {
      return wp_make_link_relative($input);

      // If not, return as-is
    } else {
      return $input;
    }

  }


  // Use Digital Ocean Spaces with Human Made S3 Uploads
  // https://github.com/humanmade/S3-Uploads#custom-endpoints
  private function config_s3_uploads() {
    add_filter( 's3_uploads_s3_client_params', function ( $params ) {

      if ( defined( 'S3_UPLOADS_ENDPOINT' ) ) {
        $params['endpoint'] = S3_UPLOADS_ENDPOINT;
        $params['use_path_style_endpoint'] = true;
        $params['debug'] = false; // Set to true if uploads are failing.
      }
      return $params;

    }, 5, 1 );
  }


}
