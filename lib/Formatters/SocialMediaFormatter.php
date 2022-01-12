<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Traits\Components;

/**
 * Class SocialMediaFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class SocialMediaFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    use Components;

    /**
     * Define formatter name
     */
    const NAME = 'SocialMedia';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/social_media/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout data
     *
     * @param array $data ACF Layout data.
     *
     * @return array
     */
    public function format( array $data ) : array {
        $data['id']        = wp_unique_id( 'social-media-' );
        $data['skip_text'] = ( new \Strings() )->s()['social_media']['skip_embed'];
        return $data;
    }
}
