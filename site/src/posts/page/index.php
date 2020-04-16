<?php
namespace nf;

$post_type = new PostType([
  'name' => 'Page',
  'blocks' => true, // allow all blocks
  'template' => [
    [ 'nf/example-static', ],
    [ 'core/paragraph', [ 'placeholder' => 'Add your text here...' ] ],
    [ 'nf/example-dynamic', ],
  ],
  'template_lock' => false,
]);
