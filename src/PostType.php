<?php

namespace nf;
use nf\Util;
use \WP_Post;

abstract class PostType {

  // Used in database for uniqueness
  const prefix = 'nf';

  // Set this name to something else!
  const name = 'Foo Bar';

  // ..or use an array to specify the plural (by default we append an 's')
  // const name = ['Foo Bar', 'Foo Bars']; 

  // https://developer.wordpress.org/resource/dashicons/
  const icon = 'dashicons-format-status';

  public $post;

  // => Foo Bar
  public static function singular_name(): string {
    list( $singular, $plural ) = self::parse_name();
    return $singular;
  }

  // => Foo Bars
  public static function plural_name(): string {
    list( $singular, $plural ) = static::parse_name();
    return $plural;
  }

  // => foo_bar
  public static function singular_type(): string {
    $type = explode( ' ', static::singular_name() );
    return strtolower( implode( '_', $type ) );
  }

  // => foo_bars
  public static function plural_type(): string {
    $type = explode( ' ', static::plural_name() );
    return strtolower( implode( '_', $type ) );
  }

  // => nf_foo_bar
  // => nf_foo_bar_description
  public static function post_type( $field = false ): string {
    $prefix = static::prefix . '_';
    $field = ($field) ? '_' . $field : ''; 
    return strtolower( $prefix . static::singular_type() . $field );
  }

  // => foo_bars
  // => edit_foo_bars
  public static function cap_type( $cap = false ): string {
    $cap = ($cap) ? $cap . '_' : ''; 
    return strtolower( $cap . static::plural_type() );
  }
  
  // => foo_bar
  // => edit_foo_bar
  public static function meta_cap_type( $cap = false ): string {
    $cap = ($cap) ? $cap . '_' : ''; 
    return strtolower( $cap . static::singular_type() );
  }

  // => foo-bars
  public static function slug(): string {
    $slug = explode( ' ', static::plural_name() );
    return strtolower( implode( '-', $slug ) );
  }

  // Detect an array or append an 's' to return [singular,plural]
  private static function parse_name(): array {

    if ( is_array( static::name ) ) {
      list( $singular, $plural ) = static::name;

    } else {
      $singular = static::name;
      $plural = static::name . 's';
    }

    if ($singular == $plural) {
      $plural .= 's';
    }

    return [$singular, $plural];
  }


  abstract public static function init();

  public function __get($key) {
    global $post;
    var_dump($key);
    var_dump($post);
  
  }

  public static function find( $id ) {
    WP_Post();
  }

  public static function register($args = []) {

    // Args detailed in this gist:
    // https://gist.github.com/justintadlock/6552000
    $args = array_merge([

      'post_type'       => static::post_type(),
      'labels'          => static::labels(),
      'rewrite'         => ['slug' => static::slug()],
      'menu_icon'       => static::icon,
      'taxonomies'      => [],
      'public'          => true,
      'has_archive'     => false,
      'hierarchical'    => false,
      'supports'        => static::supports(),
      'show_in_rest'    => ['schema' => static::schema()],
      'capability_type' => [static::meta_cap_type(), static::cap_type()],
      'capabilities'    => static::capabilities(),
      'map_meta_cap'    => true,

      'register_meta_box_cb' => function() {
        // foreach( ['normal', 'side'] as $context ) {
        foreach( ['side'] as $context ) {

          $id = static::post_type($context);
          $screen = static::post_type();
          $title = static::singular_name();
          $priority = 'high'; // low [default] high
          // $context // normal side [advanced]

          add_meta_box( $id, $title, function($post){
            static::render_metabox($post);
          }, $screen, $context, $priority ); 

        }
      },

    ], $args);


    add_action( 'init', function() use($args) {
      register_post_type( $args['post_type'], $args );
    });


    add_action( 'cmb2_admin_init', function() {
      $cmb = new_cmb2_box([
        'id'            => static::post_type(),
        'title'         => static::singular_name() . ' Details',
        'object_types'  => [static::post_type()],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
      ]);
      foreach(static::fields() as $field) {
        $field['id'] = $field['id'] ?? strtolower(str_replace(' ', '_', $field['name']));
        $cmb->add_field($field);
      }
    });

    // Map meta-capabilities for this custom post type
    add_filter( 'map_meta_cap', [static::class, 'map_meta_cap'], 10, 4 );

    // Set default permissions and flush
    if ( is_blog_installed() ) {
      if ( '1' !== get_option( static::post_type('activated') ) ) {
        update_option( static::post_type('activated'), '1' );
        static::deactivate();
        static::activate();
      }
    }

	}

  // Override this method in the custom post type
  public static function fields() { 
    return [];
  }


  // http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
  public static function map_meta_cap( $caps, $cap, $user_id, $args ): array {

    $edit_post   = static::meta_cap_type('edit');
    $delete_post = static::meta_cap_type('delete');
    $read_post   = static::meta_cap_type('read');


    // If editing, deleting, or reading this cpt, get the post and post type object.
    if ( $edit_post == $cap || $delete_post == $cap || $read_post == $cap ) {
      $post = get_post( $args[0] );
      $post_type = get_post_type_object( $post->post_type );

      // Set an empty array for the caps. 
      $caps = [];
    }

    // If editing this cpt, assign the required capability.
    if ( $edit_post == $cap ) {
      if ( $user_id == $post->post_author )
        $caps[] = $post_type->cap->edit_posts;
      else
        $caps[] = $post_type->cap->edit_others_posts;
    }

    // If deleting a post, assign the required capability.
    elseif ( $delete_post == $cap ) {
      if ( $user_id == $post->post_author )
        $caps[] = $post_type->cap->delete_posts;
      else
        $caps[] = $post_type->cap->delete_others_posts;
    }
    
    // If reading a private post, assign the required capability.
    elseif ( $read_post == $cap ) {

      if ( 'private' != $post->post_status )
        $caps[] = 'read';
      elseif ( $user_id == $post->post_author )
        $caps[] = 'read';
      else
        $caps[] = $post_type->cap->read_private_posts;
    }

    // Return the capabilities required by the user.
    return $caps;
  }
  

  // Default labels for this custom post type
  public static function labels(): array {
    return [
      'name'                  => __( ''                  . static::plural_name()   . ''                , static::prefix ),
      'singular_name'         => __( ''                  . static::singular_name() . ''                , static::prefix ),
      'menu_name'             => __( ''                  . static::plural_name()   . ''                , static::prefix ),
      'name_admin_bar'        => __( ''                  . static::plural_name()   . ''                , static::prefix ),
      'add_new'               => __( 'New '              . static::singular_name() . ''                , static::prefix ),
      'add_new_item'          => __( 'Add New '          . static::singular_name() . ''                , static::prefix ),
      'edit_item'             => __( 'Edit '             . static::singular_name() . ''                , static::prefix ),
      'new_item'              => __( 'New '              . static::singular_name() . ''                , static::prefix ),
      'view_item'             => __( 'View '             . static::plural_name()   . ''                , static::prefix ),
      'search_items'          => __( 'Search '           . static::plural_name()   . ''                , static::prefix ),
      'not_found'             => __( 'No '               . static::plural_name()   . ' Found'          , static::prefix ),
      'not_found_in_trash'    => __( 'No '               . static::plural_name()   . ' found in Trash' , static::prefix ),
      'all_items'             => __( 'All '              . static::plural_name()   . ''                , static::prefix ),
      'insert_into_item'      => __( 'Insert into '      . static::singular_name() . ''                , static::prefix ),
      'uploaded_to_this_item' => __( 'Uploaded to this ' . static::singular_name() . ''                , static::prefix ),
      'views'                 => __( 'Filter '           . static::plural_name()   . ' list'           , static::prefix ),
      'pagination'            => __( ''                  . static::plural_name()   . ' list navigation', static::prefix ),
      'list'                  => __( ''                  . static::plural_name()   . ' list'           , static::prefix ),
      'featured_image'        => __( 'Featured Image'                                                  , static::prefix ),
      'set_featured_image'    => __( 'Set featured image'                                              , static::prefix ),
      'remove_featured_image' => __( 'Remove featured image'                                           , static::prefix ),
      'use_featured_image'    => __( 'Use as featured image'                                           , static::prefix ),
    ];
  }


  // Default permissions for the default roles for this custom post type
  public static function roles_capabilities(): array {

    $all_caps = [
      'create', 
      'edit', 
      'edit_others', 
      'publish', 
      'read_private', 
      'delete', 
      'delete_private', 
      'delete_published', 
      'delete_others', 
      'edit_private', 
      'edit_published',
    ];

    $most_caps = [
      'edit', 
      'publish', 
      'delete', 
      'delete_published', 
      'edit_published',
    ];

    $few_caps = [
      'edit', 
      'delete', 
    ];

    return [
      'administrator' => $all_caps,
      'editor'        => $all_caps,
      'author'        => $most_caps,
      'contributor'   => $few_caps,
    ];

  }


  // List all the capability types for this custom post type
  public static function capabilities(): array {
    return [

      // meta caps (don't assign these to roles)
      'edit_post'              => static::meta_cap_type('edit'),
      'read_post'              => static::meta_cap_type('read'),
      'delete_post'            => static::meta_cap_type('delete'),

      // primitive/meta caps
      'create_posts'           => static::cap_type('create'),

      // primitive caps used outside of map_meta_cap()
      'edit_posts'             => static::cap_type('edit'),
      'edit_others_posts'      => static::cap_type('edit_others'),
      'publish_posts'          => static::cap_type('publish'),
      'read_private_posts'     => static::cap_type('read_private'),

      // primitive caps used inside of map_meta_cap()
      'read'                   => 'read',
      'delete_posts'           => static::cap_type('delete'),
      'delete_private_posts'   => static::cap_type('delete_private'),
      'delete_published_posts' => static::cap_type('delete_published'),
      'delete_others_posts'    => static::cap_type('delete_others'),
      'edit_private_posts'     => static::cap_type('edit_private'),
      'edit_published_posts'   => static::cap_type('edit_published'),

    ];
  }


  // Default list of features this custom post type suports
  public static function supports(): array {
    return ['title', 'editor', 'revisions', 'page-attributes', 'thumbnail', 'custom-fields'];
  }


  // https://make.wordpress.org/core/2019/10/03/wp-5-3-supports-object-and-array-meta-types-in-the-rest-api/
  public static function schema(): array {
    return [];
  }


  // Call when plugin is activated
  public static function activate() {

    // Add default caps to default roles
    foreach(self::roles_capabilities() as $role_type => $cap_types) {
      $role = get_role($role_type);

      foreach($cap_types as $cap_type) {
        $role->add_cap( static::cap_type($cap_type) );
      }
    }

    // Make new URLs work
    flush_rewrite_rules(false);
  }


  // Call when plugin is deactivated
  public static function deactivate() {

    // Remove default caps from default roles
    foreach(self::roles_capabilities() as $role_type => $cap_types) {
      $role = get_role($role_type);

      foreach($cap_types as $cap_type) {
        $role->remove_cap( static::cap_type($cap_type) );
      }
    }

    // Remove old URLs
    flush_rewrite_rules(false);
  }


  public static function render_metabox($post) {
    echo "<p>Hello world!</p>";
  }

  public function add_action_save_post_class_meta() {

    /* Save the meta box's post metadata. */
    add_action( 'save_post', function( $post_id, $post ) {

      /* Verify the nonce before proceeding. */
      if ( !isset( $_POST['nf_post_class_nonce'] ) || !wp_verify_nonce( $_POST['nf_post_class_nonce'], basename( __FILE__ ) ) )
        return $post_id;

      /* Get the post type object. */
      $post_type = get_post_type_object( $post->post_type );

      /* Check if the current user has permission to edit the post. */
      if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

      /* Get the posted data and sanitize it for use as an HTML class. */
      $new_meta_value = ( isset( $_POST['nf-post-class'] ) ? sanitize_html_class( $_POST['nf-post-class'] ) : '' );

      /* Get the meta key. */
      $meta_key = 'nf_post_class';

      /* Get the meta value of the custom field key. */
      $meta_value = get_post_meta( $post_id, $meta_key, true );

      /* If a new meta value was added and there was no previous value, add it. */
      if ( $new_meta_value && ’ == $meta_value )
        add_post_meta( $post_id, $meta_key, $new_meta_value, true );

      /* If the new meta value does not match the old value, update it. */
      elseif ( $new_meta_value && $new_meta_value != $meta_value )
        update_post_meta( $post_id, $meta_key, $new_meta_value );

      /* If there is no new meta value but an old value exists, delete it. */
      elseif ( '' == $new_meta_value && $meta_value )
        delete_post_meta( $post_id, $meta_key, $meta_value );

    }, 10, 2 );
  }


  // public static function addFields($name, $callback) {
  //
  //   $builder = new FieldsBuilder($name);
  //   $builder->setLocation('post_type', '==', static::post_type());
  //   $callback($builder);
  //   
  //   add_action('acf/init', function() use ($builder) {
  //     acf_add_local_field_group($builder->build());
  //   });
  //
  // }


  public function __construct($ID) {
    $this->post = WP_Post::get_instance($ID);
  }


}

