<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Admin
 *
 * @package TMS\Theme\Base
 */
class Admin implements Interfaces\Controller {

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \add_filter(
            'tiny_mce_before_init',
            \Closure::fromCallable( [ $this, 'modify_tinymce_headings' ] )
        );

        \add_filter(
            'acf/fields/wysiwyg/toolbars',
            \Closure::fromCallable( [ $this, 'modify_tinymce_toolbars' ] )
        );

        \add_action(
            'admin_body_class',
            \Closure::fromCallable( [ $this, 'add_template_slug_to_body_class' ] )
        );

        \add_action(
            'enqueue_block_editor_assets',
            \Closure::fromCallable( [ $this, 'disable_editor_fullscreen_by_default' ] )
        );
    }

    /**
     * Modify TinyMCE
     * Based on https://gist.github.com/psorensen/ab45d9408be658b6f90dfeabf1c9f4e6
     *
     * @param array $tags TinyMCE tags.
     *
     * @return array $tags
     */
    private function modify_tinymce_headings( $tags = [] ) : array {
        $strings = [
            'paragraph' => __( 'Paragraph' ),
            'header'    => __( 'Header' ),
        ];
        $formats = [
            'p'  => $strings['paragraph'],
            'h2' => $strings['header'] . ' 2',
            'h3' => $strings['header'] . ' 3',
            'h4' => $strings['header'] . ' 4',
        ];

        \array_walk( $formats, function ( $key, $val ) use ( &$block_formats ) {
            $block_formats .= esc_attr( $key ) . '=' . esc_attr( $val ) . ';';
        }, $block_formats = '' );
        $tags['block_formats'] = $block_formats;

        return $tags;
    }

    /**
     * Modify ACF Wysiwyg toolbars.
     *
     * @param array $toolbars ACF Registered toolbars.
     *
     * @return array
     */
    private function modify_tinymce_toolbars( array $toolbars = [] ) : array {
        $toolbars['tms'] = apply_filters(
            'tms/theme/toolbars/tms',
            [
                1 => [ // Must start with 1
                    'formatselect',
                    'bold',
                    'italic',
                    'bullist',
                    'numlist',
                    'alignleft',
                    'aligncenter',
                    'alignright',
                    'link',
                    'pastetext',
                    'removeformat',
                ],
            ]
        );

        $toolbars['tms-minimal'] = apply_filters(
            'tms/theme/toolbars/tms-minimal',
            [
                1 => [ // Must start with 1
                    'bold',
                    'italic',
                    'link',
                    'pastetext',
                    'removeformat',
                ],
            ]
        );

        return $toolbars;
    }

    /**
     * Disable editor full screen mode by default.
     */
    private function disable_editor_fullscreen_by_default() {
        $script = "
window.onload = function() {
    if ( wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ) ) {
        wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' );
    }
}";

        \wp_add_inline_script( 'wp-blocks', $script );
    }

    /**
     * This adds a class to the body class list. The class is determined
     * by the template of the edited page. Only for pages.
     *
     * @param string $classes The original body class string.
     *
     * @return string $classes The possibly modified body class string.
     */
    private function add_template_slug_to_body_class( $classes = '' ) {
        global $pagenow;

        // We should check against nonce, but we wont, so ignore recommendation.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( 'post.php' === $pagenow && ! empty( $_GET['post'] ) ) {
            $id   = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
            $type = \get_post_type( $id );

            if ( $type === 'page' ) {
                $template  = \get_page_template_slug( $id );
                $file_name = 'page-default';

                if ( $template !== '' ) {
                    $file_name_with_suffix = substr( $template, ( strpos( $template, '/' ) + 1 ) );
                    $file_name             = substr( $file_name_with_suffix, 0, strpos( $file_name_with_suffix, '.' ) );
                }

                $classes .= " geniem-${file_name}";
            }
        }

        return $classes;
    }
}
