<?php
// 
// nf-wordpress
// https://nonfiction.ca
// 

$timber = new Timber\Timber();

// Sets the directories (inside your theme) to find .twig files
Timber::$dirname = ['templates', 'views'];

add_action('wp_enqueue_scripts', function () {

  // Where do compiled assets live
  $json = get_template_directory() . '/dist/assets.json';
  $manifest = json_decode(file_get_contents($json, true));

  // Load the compiled assets.json to get the vendor and assets objects:
  // {
  //   "assets": {
  //     "css": "/app/site/theme/dist/assets.css",
  //     "js": "/app/site/theme/dist/assets.js"
  //   },
  //   "vendor": {
  //     "css": "/app/site/theme/dist/vendor.css",
  //     "js": "/app/site/theme/dist/vendor.js"
  //   }
  // }

  // First enqueue vendor assets
  $vendor = $manifest->vendor;
  if (isset($vendor->css)) wp_enqueue_style( 'vendor', home_url() . $vendor->css, false, null, 'all' );
  if (isset($vendor->js)) wp_enqueue_script( 'vendor', home_url() . $vendor->js, false, null,  true );

  // Last enqueue assets we've written for this site
  $assets = $manifest->assets;
  if (isset($assets->css)) wp_enqueue_style( 'assets', home_url() . $assets->css, ['vendor'], null, 'all' );
  if (isset($assets->js)) wp_enqueue_script( 'assets', home_url() . $assets->js, ['vendor'], null,  true );

}, 100);



/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class nfSite extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'theme_supports' ] );
		add_filter( 'timber/context', [ $this, 'add_to_context' ] );
		add_filter( 'timber/twig', [ $this, 'add_to_twig' ] );
		parent::__construct();
	}



	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {

		$context['menu'] = new Timber\Menu('primary', [
			'depth' => 2
		]);

		$context['menu_images'] = [];
		foreach( $context['menu']->items as $item ) {
				$page_id = $item->master_object()->ID;
				$img_url = get_the_post_thumbnail_url( $page_id, 'post-full' );
				if ($img_url) {
						$context['menu_images'][$item->id] = $img_url;
				}
		}

		$context['site'] = $this;
		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5', array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats', array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );
	}





	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter( new Twig_SimpleFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		return $twig;
	}

}

new nfSite();
