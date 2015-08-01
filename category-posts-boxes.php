<?php
/*
Plugin Name: Category Post Boxes
Description: Enables a shortcode which, when inserted in a page or post, creates a series of boxes corresponding to the posts in a certain category
Version: 0.1
Author: xdxd.vs.xdxd
Author URI: http://www.artisopensource.net
Plugin URI: http://www.artisopensource.net
License: Creative Commons Attrib
License URI: http://www.artisopensource.net
Text Domain: category-posts-boxes
Domain Path: /languages

Category Post Boxes
Copyright (C) 2013-2015, Salvatore Iaconesi - salvatore.iaconesi@artisopensource.net

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action( 'wp_enqueue_scripts', 'category_post_boxes_style' );
add_action( 'wp_enqueue_scripts', 'category_post_boxes_js' );
add_shortcode( 'category-posts-boxes', 'category_posts_boxes_exec' );


// Register Style
function category_post_boxes_style() {

	wp_register_style( 'category_post_boxes_style', plugins_url('category_post_boxes_style.css', __FILE__ ), false, '1.0' );
	wp_enqueue_style( 'category_post_boxes_style' );

}

// Register Script
function category_post_boxes_js() {

	wp_deregister_script( 'category_post_boxes_js' );
	wp_register_script( 'category_post_boxes_js', plugins_url('category_post_boxes.js', __FILE__ ), array( 'jquery' ), '1.0', false );
	wp_enqueue_script( 'category_post_boxes_js' );

}


// Add Shortcode
function category_posts_boxes_exec( $atts ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'category' => '',
			'background_color' => '#EEEEEE',
			'color'			=> '#333333'
		), $atts )
	);

	// Code
	$result = '';

	if(isset($atts["category"]) && $atts["category"]!='' ){

		$posts = null;

		$term = term_exists($atts["category"], 'category');
		if ($term !== 0 && $term !== null) {
			// category exists

			$args = array(
				'posts_per_page'   => -1,
				'post_type'		   => 'post',
				'tax_query'		   => array(
										array(
												'taxonomy'	=> 'category',
												'field'		=> 'slug',
												'terms'		=> array($atts["category"])
										)
				),
				'orderby'          => 'date',
				'order'            => 'DESC'
			);
			$posts = get_posts( $args );

		} else {
			$term = term_exists($atts["category"], 'post_tag');
			if ($term !== 0 && $term !== null) {
				// tag exists

				$args = array(
				'posts_per_page'   => -1,
				'post_type'		   => 'post',
				'tax_query'		   => array(
										array(
												'taxonomy'	=> 'post_tag',
												'field'		=> 'slug',
												'terms'		=> array($atts["category"])
										)
				),
				'orderby'          => 'date',
				'order'            => 'DESC'
			);
			$posts = get_posts( $args );

			}
		}

		if(isset($posts)){

			$result = $result . "<div class='category-post-boxes-container'>";

			foreach ($posts as $post) {

				$cleanposttitle = str_replace("'", " ", $post->post_title);

				$result = $result . "<a class='category-post-boxes-a-box-link' href='" . get_post_permalink($post->ID) . "' title='" . $cleanposttitle . "' alt='" . $cleanposttitle . "'>";

				$result = $result . "<div class='category-post-boxes-a-box-wrapper'>";

					$result = $result . "<div class='category-post-boxes-a-box-content'>";

					$result = $result . $post->post_title;					

					$result = $result . "</div>";

				$result = $result . "</div>";

				$result = $result . "</a>";
				
			}


			$result = $result . "</div>";

		}

	}

	return $result;
}



?>