<?php
namespace nf;

class PostType {

  // Used in database for uniqueness
  const prefix = 'nf';

  // Set this name to something else!
  public $name = 'Foo Bar';

  // ..or use an array to specify the plural (by default we append an 's')
  // public $name = ['Foo Bar', 'Foo Bars'];

  // https://developer.wordpress.org/resource/dashicons/
  public $icon = 'dashicons-format-status';


  public function __construct( $args = [] ) {
    $this->name = $args['name'];
    unset($args['name']);

    $this->icon = $args['icon'] ?? 'dashicons-format-status';
    unset($args['icon']);
    $this->icon = 'dashicons-' . str_replace( 'dashicons-', '', $this->icon );

    $this->blocks = $args['blocks'] ?? $this->default_blocks();
    unset($args['blocks']);

    if (is_array( $this->blocks) ) {
      if ($this->blocks[0] === 'default') {
        unset($this->blocks[0]);
        $this->blocks = array_merge($this->default_blocks(), $this->blocks);
      }
      $blocks = [];
      $this->blocks = $blocks;
    }

    $this->template = $args['template'] ?? $this->default_template();
    unset($args['template']);

    $this->template_lock = $args['template_lock'] ?? $this->default_template_lock();
    unset($args['template_lock']);

    // https://developer.wordpress.org/reference/functions/post_type_supports/
    $this->supports = $args['supports'] ?? $this->default_supports();
    unset($args['supports']);

    // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/
    $this->schema = $args['schema'] ?? $this->default_schema();
    unset($args['schema']);

    // https://github.com/CMB2/CMB2/wiki/Field-Types
    $this->fields = $args['fields'] ?? $this->default_fields();
    unset($args['fields']);

    // https://developer.wordpress.org/block-editor/developers/backward-compatibility/meta-box/
    $this->render_metabox_func = $args['render_metabox'] ?? function(){};
    unset($args['render_metabox']);

    $this->capabilities = $args['capabilities'] ?? $this->default_capabilities();
    unset($args['capabilities']);

    $this->roles_capabilities = $args['roles_capabilities'] ?? $this->default_roles_capabilities();
    unset($args['roles_capabilities']);

    $this->labels = $args['labels'] ?? $this->default_labels();
    unset($args['labels']);

    // Finally register or customize the post type
    if ( $this->is_native_post_type() ) {
      $this->register_native($args);
    } else {
      $this->register($args);
    }
  }


  // => Foo Bar
  public function singular_name(): string {
    list( $singular, $plural ) = $this->parse_name();
    return $singular;
  }

  // => Foo Bars
  public function plural_name(): string {
    list( $singular, $plural ) = $this->parse_name();
    return $plural;
  }

  // => foo_bar
  public function singular_type(): string {
    $type = explode( ' ', $this->singular_name() );
    return strtolower( implode( '_', $type ) );
  }

  // => foo_bars
  public function plural_type(): string {
    $type = explode( ' ', $this->plural_name() );
    return strtolower( implode( '_', $type ) );
  }

  // => nf_foo_bar
  // => nf_foo_bar_description
  public function post_type( $field = false ): string {
    $prefix = ($this->is_native_post_type()) ? '' : static::prefix . '_';
    $field = ($field) ? '_' . $field : '';
    return strtolower( $prefix . $this->singular_type() . $field );
  }

  // => foo_bars
  // => edit_foo_bars
  public function cap_type( $cap = false ): string {
    $cap = ($cap) ? $cap . '_' : '';
    return strtolower( $cap . $this->plural_type() );
  }

  // => foo_bar
  // => edit_foo_bar
  public function meta_cap_type( $cap = false ): string {
    $cap = ($cap) ? $cap . '_' : '';
    return strtolower( $cap . $this->singular_type() );
  }

  // => foo-bars
  public function slug(): string {
    $slug = explode( ' ', $this->plural_name() );
    return strtolower( implode( '-', $slug ) );
  }

  // Detect an array or append an 's' to return [singular,plural]
  private function parse_name(): array {

    if ( is_array( $this->name ) ) {
      list( $singular, $plural ) = $this->name;

    } else {
      $singular = $this->name;
      $plural = $this->name . 's';
    }

    if ($singular == $plural) {
      $plural .= 's';
    }

    return [$singular, $plural];
  }


  public $render_metabox_func = false;
  public function render_metabox($post=null) {
    ($this->render_metabox_func)($post);
  }

  private function register($args = []) {

    // Args detailed in this gist:
    // https://gist.github.com/justintadlock/6552000
    $args = array_merge([

      'post_type'       => $this->post_type(),
      'labels'          => $this->labels,
      'rewrite'         => ['slug' => $this->slug()],
      'menu_icon'       => $this->icon,
      'taxonomies'      => [],
      'public'          => true,
      'has_archive'     => false,
      'hierarchical'    => false,
      'supports'        => $this->supports,
      'template'        => $this->template,
      'template_lock'   => $this->template_lock,
      'show_in_rest'    => ['schema' => $this->schema],
      'capability_type' => [$this->meta_cap_type(), $this->cap_type()],
      'capabilities'    => $this->capabilities,
      'map_meta_cap'    => true,

      /*
      'register_meta_box_cb' => function() {
        foreach( ['side'] as $context ) { // ['normal', 'side']

          $id = $this->post_type($context);
          $screen = $this->post_type();
          $title = $this->singular_name();
          $priority = 'high'; // low [default] high
          // $context // normal side [advanced]

          add_meta_box( $id, $title, function($post){
            $this->render_metabox($post);
          }, $screen, $context, $priority );

        }
      },
      */

    ], $args);


    add_action( 'init', function() use($args) {
      register_post_type( $args['post_type'], $args );
    });

    $this->set_allowed_block_types();

    add_action( 'cmb2_admin_init', function() {
      $cmb = new_cmb2_box([
        'id'            => $this->post_type(),
        'title'         => $this->singular_name() . ' Details',
        'object_types'  => [$this->post_type()],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
      ]);
      foreach($this->fields as $field) {
        $field['id'] = $field['id'] ?? strtolower(str_replace(' ', '_', $field['name']));
        $cmb->add_field($field);
      }
    });

    // Map meta-capabilities for this custom post type
    add_filter( 'map_meta_cap', [$this, 'map_meta_cap'], 10, 4 );

    // Set default permissions and flush
    if ( is_blog_installed() ) {
      if ( '1' !== get_option( $this->post_type('activated') ) ) {
        update_option( $this->post_type('activated'), '1' );
        $this->deactivate();
        $this->activate();
      }
    }

  }


  private function set_allowed_block_types() {
    add_filter( 'allowed_block_types', function( $allowed_block_types, $post ) {
      if ( $post->post_type === $this->post_type() ) {
        
        if (is_array($this->blocks)) {
          $types = [];
          foreach($this->blocks as $type) {
            $types[] = $type;
          };
          return $types;

        } else {
          return true;
        }

      }
      return $allowed_block_types;
    }, 10, 2 );
  }


  public function register_native($args) {
    add_action( 'init', function() use($args) {

      $post_type = get_post_type_object( $this->post_type() );
      $post_type->template = $this->template;
      $post_type->template_lock = $this->template_lock;

      add_post_type_support( $this->post_type(), $this->supports );

      $this->set_allowed_block_types();
    });
  }



  private function is_native_post_type() {
    if ( ( $this->singular_type() == 'post' ) or ( $this->singular_type() == 'page' ) ) {
      return true;
    } else {
      return false;
    }
  }


  // CMB2
  public $fields = [];
  private function default_fields() {
    return [];
  }

  public $template = [];
  private function default_template() {
    return [];
  }

  public $template_lock = false;
  private function default_template_lock() {
    return false;
  }

  // http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
  public function map_meta_cap( $caps, $cap, $user_id, $args ): array {

    $edit_post   = $this->meta_cap_type('edit');
    $delete_post = $this->meta_cap_type('delete');
    $read_post   = $this->meta_cap_type('read');


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

  public $blocks = true;
  private function default_blocks() {
    return true;
  }

  // Default labels for this custom post type
  public $labels = [];
  private function default_labels(): array {
    return [
      'name'                  => __( ''                  . $this->plural_name()   . ''                , static::prefix ),
      'singular_name'         => __( ''                  . $this->singular_name() . ''                , static::prefix ),
      'menu_name'             => __( ''                  . $this->plural_name()   . ''                , static::prefix ),
      'name_admin_bar'        => __( ''                  . $this->plural_name()   . ''                , static::prefix ),
      'add_new'               => __( 'New '              . $this->singular_name() . ''                , static::prefix ),
      'add_new_item'          => __( 'Add New '          . $this->singular_name() . ''                , static::prefix ),
      'edit_item'             => __( 'Edit '             . $this->singular_name() . ''                , static::prefix ),
      'new_item'              => __( 'New '              . $this->singular_name() . ''                , static::prefix ),
      'view_item'             => __( 'View '             . $this->plural_name()   . ''                , static::prefix ),
      'search_items'          => __( 'Search '           . $this->plural_name()   . ''                , static::prefix ),
      'not_found'             => __( 'No '               . $this->plural_name()   . ' Found'          , static::prefix ),
      'not_found_in_trash'    => __( 'No '               . $this->plural_name()   . ' found in Trash' , static::prefix ),
      'all_items'             => __( 'All '              . $this->plural_name()   . ''                , static::prefix ),
      'insert_into_item'      => __( 'Insert into '      . $this->singular_name() . ''                , static::prefix ),
      'uploaded_to_this_item' => __( 'Uploaded to this ' . $this->singular_name() . ''                , static::prefix ),
      'views'                 => __( 'Filter '           . $this->plural_name()   . ' list'           , static::prefix ),
      'pagination'            => __( ''                  . $this->plural_name()   . ' list navigation', static::prefix ),
      'list'                  => __( ''                  . $this->plural_name()   . ' list'           , static::prefix ),
      'featured_image'        => __( 'Featured Image'                                                 , static::prefix ),
      'set_featured_image'    => __( 'Set featured image'                                             , static::prefix ),
      'remove_featured_image' => __( 'Remove featured image'                                          , static::prefix ),
      'use_featured_image'    => __( 'Use as featured image'                                          , static::prefix ),
    ];
  }


  // Default permissions for the default roles for this custom post type
  public $roles_capabilities = [];
  private function default_roles_capabilities(): array {

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
  public $capabilities = [];
  private function default_capabilities(): array {
    return [

      // meta caps (don't assign these to roles)
      'edit_post'              => $this->meta_cap_type('edit'),
      'read_post'              => $this->meta_cap_type('read'),
      'delete_post'            => $this->meta_cap_type('delete'),

      // primitive/meta caps
      'create_posts'           => $this->cap_type('create'),

      // primitive caps used outside of map_meta_cap()
      'edit_posts'             => $this->cap_type('edit'),
      'edit_others_posts'      => $this->cap_type('edit_others'),
      'publish_posts'          => $this->cap_type('publish'),
      'read_private_posts'     => $this->cap_type('read_private'),

      // primitive caps used inside of map_meta_cap()
      'read'                   => 'read',
      'delete_posts'           => $this->cap_type('delete'),
      'delete_private_posts'   => $this->cap_type('delete_private'),
      'delete_published_posts' => $this->cap_type('delete_published'),
      'delete_others_posts'    => $this->cap_type('delete_others'),
      'edit_private_posts'     => $this->cap_type('edit_private'),
      'edit_published_posts'   => $this->cap_type('edit_published'),

    ];
  }


  // https://make.wordpress.org/core/2019/10/03/wp-5-3-supports-object-and-array-meta-types-in-the-rest-api/
  public $schema = [];
  private function default_schema(): array {
    return [];
  }


  // Default list of features this custom post type suports
  public $supports = [];
  private function default_supports(): array {
    return ['title', 'editor', 'revisions', 'page-attributes', 'thumbnail', 'custom-fields'];
  }


  // https://make.wordpress.org/core/2019/10/03/wp-5-3-supports-object-and-array-meta-types-in-the-rest-api/
  public $scheme = [];


  // Call when plugin is activated
  public function activate() {

    // Add default caps to default roles
    foreach($this->roles_capabilities as $role_type => $cap_types) {
      $role = get_role($role_type);

      foreach($cap_types as $cap_type) {
        $role->add_cap( $this->cap_type($cap_type) );
      }
    }

    // Make new URLs work
    flush_rewrite_rules(false);
  }


  // Call when plugin is deactivated
  public function deactivate() {

    // Remove default caps from default roles
    foreach($this->roles_capabilities as $role_type => $cap_types) {
      $role = get_role($role_type);

      foreach($cap_types as $cap_type) {
        $role->remove_cap( $this->cap_type($cap_type) );
      }
    }

    // Remove old URLs
    flush_rewrite_rules(false);
  }

}
