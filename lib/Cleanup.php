<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Cleanup
 *
 * @package TMS\Theme\Base
 */
class Cleanup implements Interfaces\Controller {

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        // EditURI link
        \remove_action( 'wp_head', 'rsd_link' );
        // Category feed links
        \remove_action( 'wp_head', 'feed_links_extra', 3 );
        // Post and comment feed links
        \remove_action( 'wp_head', 'feed_links', 2 );
        // Windows Live Writer
        \remove_action( 'wp_head', 'wlwmanifest_link' );
        // Index link
        \remove_action( 'wp_head', 'index_rel_link' );
        // Previous link
        \remove_action( 'wp_head', 'parent_post_rel_link', 10 );
        // Start link
        \remove_action( 'wp_head', 'start_post_rel_link', 10 );
        // Canonical
        \remove_action( 'wp_head', 'rel_canonical', 10 );
        // Shortlink
        \remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
        // Links for adjacent posts
        \remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
        // WP version
        \remove_action( 'wp_head', 'wp_generator' );
        // rest api link
        \remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
        // embed links
        \remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
        // Remove oEmbed-specific JavaScript from the front-end and back-end.
        \remove_action( 'wp_head', 'wp_oembed_add_host_js' );

        // Remove FileBird review begging nag screen, if it exists.
        if (
            class_exists( \FileBird\Classes\Review::class ) &&
            method_exists( \FileBird\Classes\Review::class, 'getInstance' )
        ) {
            \remove_action(
                'admin_notices',
                [ \FileBird\Classes\Review::getInstance(), 'give_review' ]
            );
        }
    }
}
