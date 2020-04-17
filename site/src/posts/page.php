<?php
namespace nf;

PostType::register_post_type( 'page', [
  'blocks' => true, // allow all blocks
  'template' => [
    [ 'nf/example-static', ],
    [ 'core/paragraph', [ 'placeholder' => 'Add your text here...' ] ],
    [ 'nf/example-dynamic', ],
  ],
  'template_lock' => false,
]);
