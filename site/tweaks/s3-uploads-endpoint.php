<?php

// Use Digital Ocean Spaces with Human Made S3 Uploads
// https://github.com/humanmade/S3-Uploads#custom-endpoints
add_filter( 's3_uploads_s3_client_params', function ( $params ) {

  if ( defined( 'S3_UPLOADS_ENDPOINT' ) ) {
    $params['endpoint'] = S3_UPLOADS_ENDPOINT;
    $params['use_path_style_endpoint'] = true;
    $params['debug'] = false; // Set to true if uploads are failing.
  }
  return $params;

}, 5, 1 );
