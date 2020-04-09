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

		$this->register($args);
  }

  private function register($args = []) {
    $args = array_merge([
		  // 'editor_script' => 'nf-blocktypes-js',
		  // 'editor_style' => 'nf-blocktypes-css',
		  // 'style' => 'nf-blocks-css',
		  // 'script' => 'nf-blocks-js',
      'attributes' => $args['attributes'] ?? [],
      'render_callback' => $args['render_callback'] ?? false,
    ], $args);

    add_action( 'init', function() use($args) {
      register_block_type( $this->name, $args );
    });

		add_action( 'admin_footer', function() {
			echo "<script>";
			echo "nf.loadBlockType('" . $this->name . "');";
			echo "</script>\n";
		});

  }

}
