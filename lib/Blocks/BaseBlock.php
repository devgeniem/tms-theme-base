<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Exception;
use \Geniem\ACF\Block;
use \Geniem\ACF\Renderer\Dust;
use \Geniem\ACF\Renderer\CallableRenderer;

/**
 * Class BaseBlock.
 *
 * @property string title Block title.
 */
class BaseBlock {

    /**
     * The block name, or actually the slug that is used to
     * register the block.
     *
     * @var string
     */
    const NAME = '';

    /**
     * The block description. Used in WP block navigation.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The block category. Used in WP block navigation.
     *
     * @var string
     */
    protected $category = 'common';

    /**
     * The block icon. Used in WP block navigation.
     *
     * @var string
     */
    protected $icon = 'menu';

    /**
     * The block mode. ACF has a few different options.
     * Edit opens the block always in edit mode for example.
     *
     * @var string
     */
    protected $mode = 'edit';

    /**
     * The block supports. You can add all ACF support attributes here.
     *
     * @var array
     */
    protected $supports = [
        'align'  => false,
        'anchor' => true,
    ];

    /**
     * Class constructor.
     */
    public function __construct() {
        $block = new Block( $this->title, static::NAME );
        $block->set_category( $this->category );
        $block->set_icon( $this->icon );
        $block->set_description( $this->description );
        $block->set_mode( $this->mode );
        $block->set_supports( $this->supports );
        $block->set_renderer( $this->get_renderer() );

        if ( method_exists( static::class, 'fields' ) ) {
            $block->add_fields( $this->fields() );
        }

        $block->add_data_filter( [ $this, 'base_filter_data' ], 5 );

        if ( method_exists( static::class, 'filter_data' ) ) {
            $block->add_data_filter( [ $this, 'filter_data' ] );
        }

        $block->register();
    }

    /**
     * Getter for block name.
     *
     * @return string
     */
    public function get_name() {
        return static::NAME;
    }

    /**
     * This filters the block ACF data.
     *
     * @param array  $data       Block's ACF data.
     * @param Block  $instance   The block instance.
     * @param array  $block      The original ACF block array.
     * @param string $content    The HTML content.
     * @param bool   $is_preview A flag that shows if we're in preview.
     * @param int    $post_id    The parent post's ID.
     *
     * @return array The block data.
     */
    public function base_filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array {
        if ( isset( $this->supports['anchor'] ) && $this->supports['anchor'] ) {
            $data['anchor'] = $block['anchor'] ?? '';
        }

        if ( isset( $this->supports['align'] ) && $this->supports['align'] ) {
            $data['align'] = $block['align'] ?? '';
        }

        // phpcs warns if we aren't using these, so let's bend the rules a bit.
        unset( $instance, $content, $is_preview, $post_id );

        return $data;
    }

    /**
     * Get the renderer.
     * If dust partial is not found in child theme, we will use the parent theme partial.
     *
     * @param string $name Dust partial name, defaults to block name.
     *
     * @return Dust|CallableRenderer
     * @throws Exception Thrown if template is not found.
     */
    protected function get_renderer( string $name = '' ) {
        $name = $name ?: $this->get_name();
        $file = get_theme_file_path( '/partials/blocks/block-' . $name . '.dust' );

        if ( file_exists( $file ) ) {
            return new Dust( $file );
        }

        return new CallableRenderer( function ( $data ) use ( $file ) {
            $file = str_replace( '/var/www/project/web/app/', '', $file );

            return print_r( [ 'file' => $file, 'data' => $data ], true ); // phpcs:ignore
        } );
    }

    /**
     * Adds filter attributes to data array so they are available in WP filters.
     *
     * @param array  $data       Block's ACF data.
     * @param Block  $instance   The block instance.
     * @param array  $block      The original ACF block array.
     * @param string $content    The HTML content.
     * @param bool   $is_preview A flag that shows if we're in preview.
     * @param int    $post_id    The parent post's ID.
     *
     * @return array The block data.
     */
    public static function add_filter_attributes( $data, $instance, $block, $content, $is_preview, $post_id ) : array {
        $data['__filter_attributes'] = [
            'block'      => $block,
            'is_preview' => $is_preview,
            'post_id'    => $post_id,
            'instance'   => $instance,
            'content'    => $content,
        ];

        return $data;
    }
}
