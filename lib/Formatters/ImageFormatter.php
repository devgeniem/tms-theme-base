<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

/**
 * Class ImageFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class ImageFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Image';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/image/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format block data
     *
     * @param array $data ACF Block data.
     *
     * @return array
     */
    public static function format( array $data ) : array {
        $block = $data['__filter_attributes']['block'] ?? [];
        unset( $data['__filter_attributes'] );

        $image                  = (array) ( $data['image'] ?? [] );
        $image_id               = (int) ( $image['id'] ?? 0 );
        $is_clickable           = (bool) ( $data['is_clickable'] ?? false );
        $data['author_name']    = null;
        $data['image_url_orig'] = null;

        if ( $image_id > 0 ) {
            $data['author_name']    = trim( get_field( 'author_name', $image_id ) );
            $data['image_url_orig'] = $is_clickable ? ( $image['url'] ?? null ) : null;
        }

        if ( $block['supports']['align'] ?? false ) {
            $data['align'] = $block['align'] ?? '';
        }

        $data['image_caption'] = wp_strip_all_tags( $data['caption'] ?? '', true );

        if ( ! empty( $data['author_name'] ) ) {
            $data['image_caption'] .= ' (' . $data['author_name'] . ')';
        }

        $data['wrapper_class'] = 'is-align-wide mb-6';

        return $data;
    }
}
