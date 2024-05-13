<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use Geniem\ACF\Block as GeniemBlock;

/**
 * Class BlocksController
 *
 * This class handles the registration of Gutenberg blocks
 * that have been created with ACF Codifier.
 *
 * @package TMS\Theme\Base
 */
class BlocksController implements Interfaces\Controller {

    /**
     * Holds the block names for all ACF blocks.
     *
     * @var array
     */
    public array $registered_blocks = [];

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \add_filter(
            'allowed_block_types_all',
            \Closure::fromCallable( [ $this, 'allowed_block_types' ] ),
            10,
            2
        );

        \add_action(
            'acf/init',
            \Closure::fromCallable( [ $this, 'require_block_files' ] )
        );

        \add_action(
            'codifier/blocks/data',
            \Closure::fromCallable( [ $this, 'add_block_data_to_debugger' ] ),
            1,
            2
        );
    }

    /**
     * This method loops through all files in the
     * Blocks directory and requires them.
     */
    private function require_block_files() : void {
        $files         = scandir( __DIR__ . '/Blocks' );
        $cleaned_files = array_diff( $files, [ '.', '..', 'BaseBlock.php' ] );

        array_walk( $cleaned_files, function ( $block ) {
            $block_class_name = str_replace( '.php', '', $block );

            if ( $block_class_name !== $block ) {
                $class_name = __NAMESPACE__ . "\\Blocks\\{$block_class_name}";

                if ( class_exists( $class_name ) ) {
                    $block_obj                              = new $class_name();
                    $this->registered_blocks[ $class_name ] = $block_obj;
                }
            }
        } );
    }

    /**
     * Add the ACF block data to the DustPress Debugger
     *
     * @param mixed       $data  The block data.
     * @param GeniemBlock $block The block instance.
     *
     * @return mixed
     */
    private function add_block_data_to_debugger( $data, GeniemBlock $block ) {
        if ( class_exists( '\DustPress\Debugger' ) ) {
            \DustPress\Debugger::set_debugger_data( $block->get_title(), $data );
        }

        return $data;
    }

    /**
     * Set the allowed block types. By default the allowed_blocks array
     * is empty, which means that all block types are allowed. We simply
     * fill the array with the block types that the theme supports.
     *
     * @param bool|array $allowed_blocks An empty array.
     * @param \WP_Post   $context        The current block editor context.
     *
     * @return array An array of allowed block types.
     */
    private function allowed_block_types( $allowed_blocks, $context ) {
        $blocks = [
            'core/block'            => [],
            'core/template'         => [],
            'core/list'             => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/list-item'        => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/heading'          => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/paragraph'        => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'acf/image-banner'      => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/key-figures'       => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/link-list'         => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/some-link-list'    => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/quote'             => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/grid'              => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                ],
            ],
            'acf/accordion'         => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/video'             => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/image'             => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/video'             => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\DynamicEvent::SLUG,
                    PostType\BlogArticle::SLUG,
                ],
            ],
            'acf/image-gallery'     => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/image-carousel'    => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/share-links'       => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/table'             => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/material'          => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/subpages'          => [
                'post_types' => [
                    PostType\Page::SLUG,
                ],
            ],
            'acf/notice-banner'     => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/map'               => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\DynamicEvent::SLUG,
                    PostType\BlogArticle::SLUG,
                ],
            ],
            'acf/contacts'          => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\DynamicEvent::SLUG,
                    PostType\BlogArticle::SLUG,
                ],
            ],
            'acf/place-of-business' => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\DynamicEvent::SLUG,
                    PostType\BlogArticle::SLUG,
                ],
            ],
            'acf/acc-icon-links'    => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'acf/countdown'         => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
            'gravityforms/form'     => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\Contact::SLUG,
                ],
            ],
            'acf/lunch-menu'        => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                ],
            ],
            'acf/anchor-links'        => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                    PostType\BlogArticle::SLUG,
                    PostType\DynamicEvent::SLUG,
                ],
            ],
        ];

        $blocks = apply_filters(
            'tms/gutenberg/blocks',
            $blocks,
            $context
        );

        $allowed_blocks = [];
        $post_type      = \get_post_type( $context->post->ID );
        $page_template  = \get_page_template_slug( $context->post->ID );

        foreach ( $blocks as $block => $rules ) {
            if ( empty( $rules ) ) {
                $allowed_blocks[] = $block;
                continue;
            }

            $allowed_post_type = false;

            if ( isset( $rules['post_types'] ) ) {
                $allowed_post_type = in_array( $post_type, $rules['post_types'], true );
            }

            $allowed_template = false;

            if ( isset( $rules['templates'] ) ) {
                $allowed_template = in_array( $page_template, $rules['templates'], true );
            }

            if ( $allowed_post_type || $allowed_template ) {
                $allowed_blocks[] = $block;
            }
        }

        return $allowed_blocks;
    }
}
