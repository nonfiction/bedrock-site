<?php
namespace nf;

PostType::register_post_type( 'nf/team-member', [

  'title' => 'Team Member',
  'icon' => 'businesswoman',

  // Allow specific list of blocks
  'blocks' => [
    'core/image'        , 'core/paragraph' , 'core/heading'   , 'core/gallery' ,
    'core/list'         , 'core/quote'     , 'core/audio'     , 'core/cover'   ,
    'core/file'         , 'core/video'     , 'core/code'      , 'core/html'    ,
    'core/preformatted' , 'core/pullquote' , 'core/table'     , 'core/verse'   ,
    'core/page-break'   , 'core/buttons'   , 'core/columns'   , 'core/group'   ,
    'core/media-text'   , 'core/more'      , 'core/separator' , 'core/spacer'  ,
    'core/shortcode'    , 'core/classic'   , 
    'nf/example-static' , 'nf/example-dynamic',
  ],

  // New posts should start with these blocks
  'template' => [
    [ 'core/cover', ],
    [ 'core/heading', ],
    [ 'core/paragraph', ],
  ],
  'template_lock' => true,

  // CMB2 custom fields (post meta)
  // https://github.com/CMB2/CMB2/wiki/Field-Types
  'fields' => [
    [
      'id'   => 'occupation',
      'name' => 'Occupation',
      'desc' => 'Team Member Occuptation',
      'type' => 'text',
    ],
    [
      'id'   => 'mood',
      'name' => 'Mood',
      'desc' => 'Team Member Mood',
      'type' => 'text',
    ],
    [
      'id'   => 'start_date',
      'name' => 'Start Date',
      'desc' => 'How long has this team member been with us?',
      'type' => 'text_date',
    ],
  ],

]);
