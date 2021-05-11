<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

/**
 * Class HeroFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class HeroFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Hero';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/hero/data',
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
        $button_classes = [ 'mt-4' ];
        $box_classes    = [
            'is-' . $layout['align'],
        ];

        if ( $layout['use_box'] ) {
            $layout['container_class'] = 'hero--box';
            $box_classes[]             = 'hero__box--background';
            $box_classes[]             = 'has-background-primary';
            $box_classes[]             = 'has-text-primary-invert';
            $button_classes[]          = 'is-primary-invert';
            $button_classes[]          = 'is-outlined';
        }
        else {
            $layout['use_overlay'] = true;
            $box_classes[]         = 'has-text-white';
            $button_classes[]      = 'is-primary';
        }

        $layout['button_classes'] = implode( ' ', $button_classes );
        $layout['box_classes']    = implode( ' ', $box_classes );
        $layout['play_video']     = __( 'Play video', 'tms-theme-base' );
        $layout['pause_video']    = __( 'Pause video', 'tms-theme-base' );

        return $layout;
    }
}
