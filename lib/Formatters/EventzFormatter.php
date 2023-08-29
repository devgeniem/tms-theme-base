<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\EventzClient;
use TMS\Theme\Base\Eventz;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Localization;

/**
 * Class EventzFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class EventzFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

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
        $layout['category_id'] = $layout[ 'category'] ? array_values( $layout[ 'category'] ) : [];
        $layout['areas']       = $layout[ 'area']     ? array_values( $layout[ 'area'] ) : [];
        $layout['targets']     = $layout[ 'target']   ? array_values( $layout[ 'target'] ) : [];
        $layout['tags']        = $layout[ 'tag']      ? array_values( $layout[ 'tag'] ) : [];

        $query_params                = $this->format_query_params( $layout );
        $query_params['language']    = function_exists( 'pll_current_language' )
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
            'q'           => null,
            'start'       => null,
            'end'         => null,
            'category_id' => null,
            'areas'       => null,
            'tags'        => null,
            'targets'     => null,
            'sort'        => null,
            'size'        => null,
            'skip'        => null,
            'page_size'   => null,
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
            $query_params['start'] = date( 'Y-m-d' );
        }

        // Force sort param
        $query_params['sort'] = 'startDate';

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
        // Force sort param
        $query_params['sort'] = 'startDate';

        if( ! empty ( $query_params['page_size'] ) ) {
            $query_params['size'] = $query_params['page_size'];
        }

        $client  = new EventzClient( PIRKANMAA_EVENTZ_API_URL, PIRKANMAA_EVENTZ_API_KEY );

        try {
            $lang_key = Localization::get_current_language();
            $response = $client->search_events( $query_params, $lang_key );

            if ( empty( $response ) ) {
                return null;
            }

            return array_map(
                fn( $item ) => Eventz::normalize_event( $item ),
                $response->items
            );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }
}
