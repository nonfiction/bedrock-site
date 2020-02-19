<?php

namespace nf;
use nf\PostType;

class CaseStudy extends PostType {

  // Singular, Plural
  const name = ['Case Study', 'Case Studies'];
  
  // https://developer.wordpress.org/resource/dashicons/#editor-outdent
  const icon = 'dashicons-book-alt';

  public static function init() {
    self::register([]);
  }

  public static function fields() { 
    return [
      [
        'id'   => 'text',
        'name' => 'Test Text',
        'desc' => 'field description',
        'type' => 'text',
      ],
      [
        'id'   => 'text_two',
        'name' => 'Test Text Two',
        'desc' => 'field description again',
        'type' => 'text',
      ],
      [
        'name' => 'Date',
        'type' => 'text_date',
      ]
    ];
  }


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

  // https://make.wordpress.org/core/2019/10/03/wp-5-3-supports-object-and-array-meta-types-in-the-rest-api/
  public static function schema(): array {
    return [
      'type'=> 'object',
      'properties' => [
        'company_name' => [
          'type' => 'string',
        ],
        'years_active'  => [
          'type' => 'string',
        ],
      ],
    ];
  }

  public function __construct($ID) {
    parent::__construct($ID);
  }

}


/*
  public static function fields($fields) {
    $fields
      ->addText('title')
      ->addText('subtitle')
      ->addFile('the_big_file')
      ->addLink('link')
      ->addImage('background_image');
  }  
*/
