<?php

namespace nf;

add_action( 'login_enqueue_scripts', function() {
  echo "<style type='text/css'>\n";
  echo "  #login h1 a, .login h1 a {\n";
  echo "    background-image: url(" . get_stylesheet_directory_uri() . "/logo.jpg);\n";
  echo "    background-size: contain;\n";
  echo "    width: 150px;\n";
  echo "    height: 150px;\n";
  echo "    border-radius: 50%;\n";
  echo "  }\n";
  echo "</style>";
});

add_filter( 'login_headerurl', function() {
  return 'https://www.nonfiction.ca/';
});

add_filter( 'login_headertitle', function() {
  return "nonfiction studios";
});
