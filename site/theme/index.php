<?php

use Timber\Post;
use Timber\PostQuery;
// use Timber\Term;
use Timber\Timber;
use Timber\User;

$context = Timber::get_context();
// $context['terms'] = Timber::get_terms( ['taxonomy' => 'uncatergorized'] );
$content['all_posts'] = Timber::get_posts( ['post_type' => 'post', 'posts_per_page' => '-1'] );
$context['entrypoint'] = 'index';
$context['post'] = new Post();
$context['posts'] = new PostQuery();


// Populate template array from least specific to most specific
$templates = ['index.twig'];
if ( is_page() ) {
    array_unshift( $templates, 'page.twig' );
    array_unshift( $templates, 'page-' . $post->post_name . '.twig' );
}

if ( is_home() ) {
    array_unshift( $templates, 'home.twig' );
}

if ( is_front_page() ) {
    array_unshift( $templates, 'front.twig' );
}

if ( is_single() ) {
    array_unshift( $templates, 'single.twig' );
    array_unshift( $templates, 'single-' . get_post_type() . '.twig' );
    array_unshift( $templates, 'single-' . $post->post_name . '.twig' );
}

if ( is_archive() ) {
    array_unshift( $templates, 'archive.twig' );
}

if ( is_author() ) {
    array_unshift( $templates, 'author.twig' );

    if ( isset( $wp_query->query_vars['author'] ) ) {
      $author = new User( $wp_query->query_vars['author'] );
      $context['author'] = $author;
      $context['title']  = 'Author Archives: ' . $author->name();
      array_unshift( $templates, 'author-' . $wp_query->query_vars['author'] . '.twig' );
    }
}

if ( is_tax() ) {
    array_unshift( $templates, 'taxonomy.twig' );
    array_unshift( $templates, 'taxonomy-' . get_query_var('taxonomy') . '.twig' );
}

if ( is_search() ) {
  $context['title'] = 'Search results for ' . get_search_query();
  array_unshift( $templates, 'search.twig' );
}

// Set title for archives pages
if ( is_archive() ) {
    $context['title'] = 'Archive';

    if ( is_day() ) {
        $context['title'] = 'Archive: ' . get_the_date( 'F D, Y' );

    } elseif ( is_month() ) {
        $context['title'] = 'Archive: ' . get_the_date( 'F Y' );

    } elseif ( is_year() ) {
        $context['title'] = 'Archive: ' . get_the_date( 'Y' );

    } elseif ( is_tag() ) {
        $context['title'] = single_tag_title( '', false );

    } elseif ( is_category() ) {
        $context['title'] = single_cat_title( '', false );
        array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );

    } elseif ( is_post_type_archive() ) {
        $context['title'] = post_type_archive_title( '', false );
        array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );

    } elseif ( is_tax() ) {
        $term = new Term();
        $context['title'] = $term->taxonomy . ' - ' . $term->name;
    }
}

Timber::render( $templates, $context );
