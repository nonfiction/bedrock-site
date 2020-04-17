<?php
namespace nf;

class BlockType {

  public static $blocks = [];

  public static function register_block_type( $name, $args = [] ) {
    self::$blocks[$name] = new BlockType( $name, $args );
  }

  public $name = null;
  public $wp_block_type = null;

  public function __construct( $name, $args = [] ) {
    $this->name = $name;
    if ( !in_array( $this->name, self::$core_block_types ) ) {
      $this->register( $args );
    }
  }

  private function register($args = []) {

    $args = array_merge([
      'attributes' => $args['attributes'] ?? [],
      'render_callback' => $args['render_callback'] ?? false,
    ], $args);

    add_action( 'init', function() use($args) {
      $this->wp_block_type = register_block_type( $this->name, $args );
    });
  }


  public static $core_block_types = [

    /* [COMMON] */
    'core/image',
    'core/paragraph',
    'core/heading',
    'core/gallery',
    'core/list',
    'core/quote',
    'core/audio',
    'core/cover',
    'core/file',
    'core/video',

    /* [FORMATTING] */
    'core/code',
    'core/classic',
    'core/html',
    'core/preformatted',
    'core/pullquote',
    'core/table',
    'core/verse',

    /* [LAYOUT] */
    'core/page-break',
    'core/buttons',
    'core/columns',
    'core/group',
    'core/media-text',
    'core/more',
    'core/separator',
    'core/spacer',

    /* [WIDGETS] */
    'core/archives',
    'core/shortcode',
    'core/calendar',
    'core/categories',
    'core/latest-posts',
    'core/rss',
    'core/search',
    'core/social-icons',
    'core/tag-cloud',

    /* [EMBEDS] */
    'core/embed',
    'core-embed/twitter',
    'core-embed/youtube',
    'core-embed/facebook',
    'core-embed/instagram',
    'core-embed/wordpress',
    'core-embed/soundcloud',
    'core-embed/spotify',
    'core-embed/flickr',
    'core-embed/vimeo',
    'core-embed/animoto',
    'core-embed/cloudup',
    'core-embed/crowdsignal',
    'core-embed/dailymotion',
    'core-embed/hulu',
    'core-embed/imgur',
    'core-embed/issuu',
    'core-embed/kickstarter',
    'core-embed/meetup-com',
    'core-embed/mixcloud',
    'core-embed/reddit',
    'core-embed/reverbnation',
    'core-embed/screencast',
    'core-embed/scribd',
    'core-embed/slideshare',
    'core-embed/smugmug',
    'core-embed/speaker-deck',
    'core-embed/tiktok',
    'core-embed/ted',
    'core-embed/tumblr',
    'core-embed/videopress',
    'core-embed/wordpress-tv',
    'core-embed/amazon-kindle',
  ];

}
