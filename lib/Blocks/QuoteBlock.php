<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\QuoteFields;
use TMS\Theme\Base\PostType\BlogArticle;
use TMS\Theme\Base\PostType\Page;
use TMS\Theme\Base\PostType\Post;

/**
 * Class QuoteBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class QuoteBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'quote';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'quote';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'format-quote';

    /**
     * The block title.
     *
     * @var string
     */
    protected string $title;

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Sitaatti';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new QuoteFields( $this->title, self::NAME );

        return apply_filters(
            'tms/block/' . self::KEY . '/fields',
            $group->get_fields(),
            self::KEY
        );
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
    public function filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array {
        $classes = [
            'container' => [
                'has-background-secondary',
                'has-text-secondary-invert',
                'mt-9',
                'mb-9',
            ],
            'quote'     => [
                'is-family-secondary',
                'has-text-weight-medium',
                'is-size-5',
                is_singular( [ Page::SLUG, Post::SLUG, BlogArticle::SLUG ] ) ? 'is-size-1' : 'is-size-5',
            ],
            'author'    => [
                'has-text-weight-medium',
                'is-family-secondary ',
            ],
        ];

        if ( ! empty( $data['is_wide'] ) ) {
            $classes['container'][] = 'is-align-wide';
        }

        $data['classes'] = $classes;

        $data = apply_filters( 'tms/acf/block/' . self::KEY . '/data', $data );

        return $data;
    }

}
