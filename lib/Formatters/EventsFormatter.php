<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use Geniem\LinkedEvents\LinkedEventsClient;
use Geniem\LinkedEvents\LinkedEventsException;
use TMS\Theme\Base\LinkedEvents;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Settings;

/**
 * Class EventsFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class EventsFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Events';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/events/data',
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
        $query_params             = $this->format_query_params( $layout );
        $query_params['include']  = 'organization,location,keywords';
        $query_params['page']     = 1;
        $query_params['language'] = function_exists( 'pll_current_language' )
            ? pll_current_language()
            : get_locale();

        $events = $this->get_events( $query_params );

        if ( empty( $events ) ) {
            return $layout;
        }

        $layout['events']  = $this->format_events( $events, $layout['show_images'] );
        $layout['classes'] = [
            'event_item_bg'   => apply_filters( 'tms/theme/layout_events/item_bg_class', 'has-background-secondary' ),
            'event_item_text' => apply_filters( 'tms/theme/layout_events/item_text_class', '' ),
            'event_item_icon' => apply_filters( 'tms/theme/layout_events/item_icon_class', '' ),
            'all_events_link' => apply_filters( 'tms/theme/layout_events/all_events_link', 'is-size-7' ),
            'event_item_pill' => apply_filters( 'tms/theme/layout_events/event_item', 'is-primary-invert' ),
        ];

        return $layout;
    }

    /**
     * Format events
     *
     * @param array $events      Array of events.
     * @param bool  $show_images Show images flag.
     *
     * @return array
     */
    public function format_events( array $events, bool $show_images = true ) : array {
        if ( ! $show_images ) {
            return array_map( function ( $item ) {
                $item['image'] = false;

                return $item;
            }, $events );
        }

        $default_image     = Settings::get_setting( 'events_default_image' );
        $default_image_url = wp_get_attachment_image_url( $default_image, 'large' );

        if ( ! empty( $default_image ) ) {
            $events = array_map( function ( $item ) use ( $default_image_url ) {
                if ( empty( $item['image'] ) ) {
                    $item['image'] = $default_image_url;
                }

                return $item;
            }, $events );
        }

        return $events;
    }

    /**
     * Format query params
     *
     * @param array $layout ACF Layout data.
     *
     * @return array
     */
    public function format_query_params( array $layout ) : array {
        $query_params = [
            'start'     => null,
            'end'       => null,
            'keyword'   => null,
            'location'  => null,
            'publisher' => null,
            'sort'      => null,
            'page_size' => null,
            'text'      => null,
            'page'      => null,
        ];

        foreach ( $layout as $key => $value ) {
            if ( ! array_key_exists( $key, $query_params ) ) {
                continue;
            }

            if ( empty( $value ) ) {
                unset( $query_params[ $key ] );
            }
            else {
                $query_params[ $key ] = is_array( $value )
                    ? implode( ',', $value )
                    : $value;
            }
        }

        if ( ! empty( $layout['starts_today'] ) && true === $layout['starts_today'] ) {
            $query_params['start'] = 'today';
        }

        $query_params['language'] = DPT_PLL_ACTIVE
            ? pll_current_language()
            : get_locale();

        return $query_params;
    }

    /**
     * Get events
     *
     * @param array $query_params API query params.
     *
     * @return array|null
     */
    private function get_events( array $query_params ) : ?array {
        $client = new LinkedEventsClient( PIRKANMAA_EVENTS_API_URL );

        try {
            $response = $client->get( 'event', $query_params );

            if ( empty( $response ) ) {
                return null;
            }

            return array_map(
                fn( $item ) => LinkedEvents::normalize_event( $item ),
                $response
            );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }
}
