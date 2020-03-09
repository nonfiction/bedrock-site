<?php

namespace nf;
use nf\PostType;

class Service extends PostType {

  const name = 'Service';
  
  // https://developer.wordpress.org/resource/dashicons/#editor-outdent
  const icon = 'dashicons-flag';

  public static function init() {
    self::register([
      //
    ]);
  }


/*
  public static function fields($fields) {
    $fields
      ->addText('title')
      ->addText('subtitle')
      ->addFile('my_file')
      ->addLink('link')
      // ->addPostObject('post_objection')
      // ->addRelationship('relationship_thing')
      ->addImage('background_image')
      ->addTrueFalse('truefalse_field', [
        'label' => 'True / False Field',
      ]);
  }
*/

  public static function render($template, $context = []) {
    ob_start();
    extract($context);
    include __DIR__ . "/templates/$template.php";
    return ob_get_clean();
  }

  public static function render_normal_metabox($post) {
    $meta = get_post_custom($post->ID);
    // wp_nonce_field(basename(__FILE__), self::$post_type);
    // add_thickbox();
    // echo App::render("testimonial-metaboxes", [
    //     'fields' => self::customFields($meta),
    //     'prefix' => self::$post_type,
    // ]);
    echo '<p>My nice Metabox!!</p>';
    var_dump($meta);
  }

  public function __construct($ID) {
    parent::__construct($ID);
  }

}
