<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Tapahtuma
 */

use Geniem\LinkedEvents\LinkedEventsClient;
use TMS\Theme\Base\LinkedEvents;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Traits\Components;

/**
 * The PageEvent class.
 */
class PageEvent extends BaseModel {

    use Components;

    /**
     * Get home url
     *
     * @return mixed|string
     */
    protected function get_home_url() {
        return DPT_PLL_ACTIVE
            ? pll_home_url()
            : home_url();
    }


    /**
     * Hero image
     *
     * @return false|int
     */
    public function hero_image() {
        $event = $this->get_event();

        if ( empty( $event ) || empty( $event->images ) ) {
            return false;
        }

        return ! empty( $event->images[0]->url )
            ? $event->images[0]->url
            : false;
    }

    /**
     * Get event id
     *
     * @return string|null
     */
    protected function get_event_id() : ?string {
        $event_id = get_query_var( 'event-id', null );

        if ( empty( $event_id ) ) {
            wp_safe_redirect( $this->get_home_url() );

            return null;
        }
        else {
            return $event_id;
        }
    }

    /**
     * Get event from API
     *
     * @return false|stdClass|null
     */
    private function get_event() {
        $event_id = $this->get_event_id();

        if ( empty( $event_id ) ) {
            return null;
        }

        $cache_key = 'event-' . $event_id;
        $event     = wp_cache_get( $cache_key );

        if ( $event ) {
            return $event;
        }

        try {
            $client = new LinkedEventsClient( PIRKANMAA_EVENTS_API_URL );
            $event  = $client->get(
                'event/' . $event_id,
                [ 'include' => 'organization,location' ]
            );

            if ( ! empty( $event ) ) {
                wp_cache_set( $cache_key, $event, '', MINUTE_IN_SECONDS );

                return $event;
            }
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }

    /**
     * Get event info
     *
     * @return array|null
     */
    public function event() {
        $event = $this->get_event();

        if ( empty( $event ) ) {
            return null;
        }

        return [
            'normalized' => LinkedEvents::normalize_event( $event ),
            'orig'       => $event,
        ];
    }
}
