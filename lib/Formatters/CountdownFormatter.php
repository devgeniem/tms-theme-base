<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use DateTime;
use TMS\Theme\Base\Interfaces\Formatter;

/**
 * Class CountdownFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class CountdownFormatter implements Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Countdown';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/countdown/data',
            [ $this, 'format' ]
        );

        add_filter(
            'tms/acf/block/countdown/data',
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
        if ( $data['type'] === 'countdown' ) {
            $data['show_minutes'] = true;
        }

        $target_date = new DateTime();
        $target_date->setTimestamp( $data['target_datetime'] );

        $format = 'j.n.Y';

        if ( $data['type'] === 'countdown' ) {
            $format               = 'j.n.Y H:i';
            $data['show_minutes'] = true;
        }

        $data['date_formatted'] = $target_date->format( $format );

        return $data;
    }
}
