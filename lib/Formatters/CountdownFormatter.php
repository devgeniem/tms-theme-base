<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use DateTime;
use DateTimeZone;
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
        $data['have_footer']  = ! empty( $data['expired_text'] );
        $data['is_countdown'] = true;

        if ( $data['type'] === 'date' ) {
            $data['is_countdown'] = false;
        }

        if ( $data['type'] === 'countdown' ) {
            $data['show_hours']   = true;
            $data['show_minutes'] = true;
        }

        if ( $data['type'] === 'countdown_seconds' ) {
            $data['show_hours']   = true;
            $data['show_minutes'] = true;
            $data['show_seconds'] = true;
        }

        $target_date = new DateTime();

        if ( $data['type'] === 'countdown' ) {
            $target_date->setTimestamp( $data['target_datetime'] );
        }
        else {
            $target_date->setTimestamp( strtotime( 'today', $data['target_datetime'] ) );
            $data['target_datetime'] = $target_date->getTimestamp();
        }

        $format = 'j.n.Y';

        if ( $data['type'] === 'countdown' ) {
            $format               = 'j.n.Y H:i';
            $data['show_minutes'] = true;
        }

        if ( $data['type'] === 'countdown_seconds' ) {
            $format               = 'j.n.Y H:i';
            $data['show_minutes'] = true;
            $data['show_seconds'] = true;
        }

        $timezone_offset = ( ( new DateTimeZone( 'Europe/Helsinki' ) )->getOffset( $target_date ) / 60 ) / 60;

        $data['timezone_offset']    = $timezone_offset;
        $data['date_formatted']     = $target_date->format( $format );
        $data['strings']['days']    = _x( 'Days', 'theme-frontend', 'tms-theme-base' );
        $data['strings']['hours']   = _x( 'Hours', 'theme-frontend', 'tms-theme-base' );
        $data['strings']['minutes'] = _x( 'Minutes', 'theme-frontend', 'tms-theme-base' );
        $data['strings']['seconds'] = _x( 'Seconds', 'theme-frontend', 'tms-theme-base' );

        return $data;
    }
}
