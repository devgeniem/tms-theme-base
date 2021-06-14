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
class SingleDynamicEventCpt extends PageEvent {

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
     * Get event id
     *
     * @return string|null
     */
    protected function get_event_id() : ?string {
        return get_field( 'event' );
    }
}
