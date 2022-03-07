<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use TMS\Theme\Base\PostType\Page;
use TMS\Theme\Base\PostType\Post;

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

        add_filter(
            'gutenberg_can_edit_post_type',
            \Closure::fromCallable( [ $this, 'disable_gutenberg' ] ),
            10,
            2
        );

        add_filter(
            'use_block_editor_for_post_type',
            \Closure::fromCallable( [ $this, 'disable_gutenberg' ] ),
            10,
            2
        );

        add_action(
            'admin_head',
            \Closure::fromCallable( [ $this, 'disable_classic_editor' ] )
        );

        add_action(
            'init',
            \Closure::fromCallable( [ $this, 'remove_comments' ] )
        );

        \add_filter(
            'gform_add_field_buttons',
            \Closure::fromCallable( [ $this, 'remove_gf_fields' ] ),
        );
    }

    /**
     * Remove comments
     */
    public function remove_comments() : void {
        remove_post_type_support( Post::SLUG, 'trackbacks' );
        remove_post_type_support( Post::SLUG, 'comments' );
        remove_post_type_support( Page::SLUG, 'comments' );
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

    /**
     * Disable Gutenberg
     *
     * @param bool   $can_edit  Whether the post type can be edited or not.
     * @param string $post_type The post type being checked.
     *
     * @return bool
     */
    public function disable_gutenberg( $can_edit, $post_type ) {
        if ( Page::SLUG !== $post_type ) {
            return $can_edit;
        }

        if ( ! ( is_admin() && ! empty( $_GET['post'] ) ) ) { // phpcs:ignore
            return $can_edit;
        }

        if ( $this->disable_gutenberg_from_templates( $_GET['post'] ) ) { // phpcs:ignore
            $can_edit = false;
        }

        return $can_edit;

    }

    /**
     * Disable Gutenberg from defined templates
     *
     * @param ?int $post_id \WP_Post ID.
     *
     * @return bool
     */
    public function disable_gutenberg_from_templates( $post_id ) : bool {
        if ( empty( $post_id ) ) {
            return false;
        }

        if ( get_option( 'page_for_posts' ) === $post_id ) {
            return true;
        }

        $page_template      = get_page_template_slug( $post_id );
        $excludes_templates = apply_filters(
            'tms/theme/gutenberg/excluded_templates',
            [
                \PageOnepager::TEMPLATE,
                \PageFrontPage::TEMPLATE,
                \PageEventsCalendar::TEMPLATE,
                \PageEventsSearch::TEMPLATE,
            ]
        );

        return in_array( $page_template, $excludes_templates, true );
    }

    /**
     * Disable classic editor from defined templates
     */
    public function disable_classic_editor() {
        $screen = get_current_screen();

        if ( Page::SLUG !== $screen->id || ! isset( $_GET['post'] ) ) { // phpcs:ignore
            return;
        }

        if ( $this->disable_gutenberg_from_templates( $_GET['post'] ) ) { // phpcs:ignore
            remove_post_type_support( Page::SLUG, 'editor' );
        }
    }


    /**
     * This function removes all unnecessary fields from GF form admin screens.
     *
     * @param array $field_groups Original field groups.
     * @return array Modified field groups.
     */
    protected function remove_gf_fields( $field_groups ) {

        $standard_fields_idx = -1;
        $advanced_fields_idx = -1;
        $post_fields_idx     = -1;
        $pricing_fields_idx  = -1;

        foreach ( $field_groups as $idx => $group ) {
            if ( $group['name'] === 'standard_fields' ) {
                $standard_fields_idx = $idx;
            }
            elseif ( $group['name'] === 'post_fields' ) {
                $post_fields_idx = $idx;
            }
            elseif ( $group['name'] === 'advanced_fields' ) {
                $advanced_fields_idx = $idx;
            }
            elseif ( $group['name'] === 'pricing_fields' ) {
                $pricing_fields_idx = $idx;
            }
        }

        /**
         * Remove unnecessary fields from standard fields.
         */
        if ( $standard_fields_idx >= 0 && ! empty( $field_groups[ $standard_fields_idx ]['fields'] ) ) {

            foreach ( $field_groups[ $standard_fields_idx ]['fields'] as $field_idx => $field ) {

                if (
                    $field['data-type'] === 'page' ||
                    $field['data-type'] === 'html' ||
                    $field['data-type'] === 'section'
                ) {
                    unset( $field_groups[ $standard_fields_idx ]['fields'][ $field_idx ] );
                }
            }
        }

        /**
         * Remove unnecessary fields from advanced fields.
         */
        if ( $advanced_fields_idx >= 0 && ! empty( $field_groups[ $advanced_fields_idx ]['fields'] ) ) {

            foreach ( $field_groups[ $advanced_fields_idx ]['fields'] as $field_idx => $field ) {

                if (
                    $field['data-type'] === 'date' ||
                    $field['data-type'] === 'time' ||
                    $field['data-type'] === 'captcha' ||
                    $field['data-type'] === 'multiselect' ||
                    $field['data-type'] === 'list'
                ) {
                    unset( $field_groups[ $advanced_fields_idx ]['fields'][ $field_idx ] );
                }
            }
        }

        /**
         * Remove all post fields.
         */
        if ( $post_fields_idx >= 0 ) {
            unset( $field_groups[ $post_fields_idx ] );
        }

        /**
         * Remove all pricing fields.
         */
        if ( $pricing_fields_idx >= 0 ) {
            unset( $field_groups[ $pricing_fields_idx ] );
        }

        return $field_groups;
    }

}
