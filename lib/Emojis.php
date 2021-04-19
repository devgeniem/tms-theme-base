<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Emojis
 *
 * @package TMS\Theme\Base
 */
class Emojis implements Interfaces\Controller {

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        \remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        \remove_action( 'wp_print_styles', 'print_emoji_styles' );
        \remove_action( 'admin_print_styles', 'print_emoji_styles' );
        \remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        \remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        \remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

        \add_filter( 'tiny_mce_plugins', \Closure::fromCallable( [ $this, 'disable_emojis_tinymce' ] ) );
    }

    /**
     * Disable emojis from TinyMCE editors
     *
     * @param mixed $plugins The original value.
     *
     * @return array
     */
    private function disable_emojis_tinymce( $plugins ) : array {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, [ 'wpemoji' ] );
        }

        return [];
    }
}
