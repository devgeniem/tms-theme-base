<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use Geniem\LinkedEvents\LinkedEventsClient;
use TMS\Theme\Base\LinkedEvents;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Traits\Components;

/**
 * The SingleDynamicEventCpt class.
 */
class SingleDynamicEventCpt extends BaseModel {

    use Components;

    public function hero() {
        return [
            'title'       => get_the_title(),
            'image_id'    => has_post_thumbnail()
                ? get_post_thumbnail_id()
                : false,
            'description' => get_field( 'description' ),
            'link'        => get_field( 'hero_link' ),
        ];
    }

    private function get_event() {
        $event_id = get_field( 'event' );

        if ( empty( $event_id ) ) {
            return null;
        }

        $client = new LinkedEventsClient( PIRKANMAA_EVENTS_API_URL );

        try {
            return $client->get( 'event/' . $event_id );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }

    public function event() {
        $event = $this->get_event();

        return [
            'normalized' => LinkedEvents::normalize_event( $event ),
            'orig'       => $event,
        ];
    }
}
