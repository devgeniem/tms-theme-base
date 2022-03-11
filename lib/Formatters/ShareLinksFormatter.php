<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Traits;

/**
 * Class ShareLinksFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class ShareLinksFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    use Traits\Sharing;

    /**
     * Define formatter name
     */
    const NAME = 'ShareLinks';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/share_links/data',
            [ $this, 'format' ]
        );

        add_filter(
            'tms/acf/block/share_links/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout data
     *
     * @param array $layout ACF Layout data.
     *
     * @return array
     */
    public function format( array $layout ) : array {
        $layout['share_links']        = $this->get_share_links();
        $layout['share_link_classes'] = $this->share_link_classes();

        return $layout;
    }
}
