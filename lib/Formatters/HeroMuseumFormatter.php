<?php
namespace TMS\Theme\Base\Formatters;

/**
 * Class HeroMuseumFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class HeroMuseumFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'HeroMuseum';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/hero_museum/data',
            [ $this, 'format' ],
            30
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
        $opening_times = [
            'title' => $layout['opening_times']['opening_times_title'] ?? false,
            'text'  => $layout['opening_times']['opening_times_text'] ?? false,
            'link'  => $layout['opening_times']['opening_times_button'] ?? false,
        ];

        if ( ! empty( $opening_times['title'] ) || ! empty( $opening_times['text'] ) ) {
            $layout['columns'][] = $opening_times;
        }

        $tickets = [
            'title' => $layout['tickets']['tickets_title'] ?? false,
            'text'  => $layout['tickets']['tickets_text'] ?? false,
            'logo'  => $layout['tickets']['tickets_image'] ?? false,
            'link'  => $layout['tickets']['tickets_button'] ?? false,
        ];

        if ( ! empty( $tickets['title'] ) || ! empty( $tickets['text'] ) ) {
            $layout['columns'][] = $tickets;
        }

        $find_us = [
            'title' => $layout['find_us']['find_us_title'] ?? false,
            'text'  => $layout['find_us']['find_us_text'] ?? false,
            'link'  => $layout['find_us']['find_us_button'] ?? false,
        ];

        if ( ! empty( $find_us['title'] ) || ! empty( $find_us['text'] ) ) {
            $layout['columns'][] = $find_us;
        }

        $layout['button_classes'] = 'is-primary';

        return $layout;
    }
}
