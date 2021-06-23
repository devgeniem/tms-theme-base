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

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter( 'tms/theme/breadcrumbs/show_breadcrumbs_in_header', fn() => false );
    }

    /**
     * Hero image
     *
     * @return false|int
     */
    public function hero_image() {
        return has_post_thumbnail()
            ? get_post_thumbnail_id()
            : false;
    }

    /**
     * Get event from API
     *
     * @return false|stdClass|null
     */
    private function get_event() {
        $event_id = get_field( 'event' );

        if ( empty( $event_id ) ) {
            return null;
        }

        $client = new LinkedEventsClient( PIRKANMAA_EVENTS_API_URL );

        try {
            return $client->get(
                'event/' . $event_id,
                [ 'include' => 'organization,location' ]
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }

    /**
     * Get event info
     *
     * @return array
     */
    public function event() {
        $event = $this->get_event();

        return [
            'normalized' => LinkedEvents::normalize_event( $event ),
            'orig'       => $event,
        ];
    }
}
