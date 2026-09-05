<?php
/**
 * Template Name: Bejelentkezés
 *
 * @package BMA_Engineering
 */

get_header();

while ( have_posts() ) {
	the_post();
	the_content();
}

get_footer();
