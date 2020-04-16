<?php

namespace nf;

class BlockType {

  // Set this name to something else!
  public $name = 'nf/foo-bar';

  public function __construct( $name_or_args = [] ) {

    if ( is_string($name_or_args) ) {
      $args = [];
      $this->name = $name_or_args;

    } else {
      $args = $name_or_args;
      $this->name = $args['name'];
      unset($args['name']);
    }

    $this->styles = $args['styles'] ?? [];

    if ( !in_array($this->name, self::$core_block_types) ) {
      $this->register($args);
    }

    $default = true;
    foreach($this->styles as $class_name => $class_label ) {
      $this->register_style($class_name, $class_label, $default);
      $default = false;
    }
  }

  private function register($args = []) {
    $args = array_merge([
      'attributes' => $args['attributes'] ?? [],
      'render_callback' => $args['render_callback'] ?? false,
    ], $args);

    add_action( 'init', function() use($args) {
      register_block_type( $this->name, $args );
    });

    // add_action( 'admin_footer', function() {
    //   echo "<script id='nf-load-blocktype'>";
    //   echo "nf.loadBlockType('" . $this->name . "');";
    //   echo "</script>\n";
    // }, 100);

  }

  private function register_style($class_name, $class_label, $default) {
    add_action( 'init', function() use($class_name, $class_label, $default) {
      register_block_style($this->name, [
        'name' => $class_name,
        'label' => $class_label, 
        // 'is_default' => $default,
      ]);
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
