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
     * Event
     *
     * @var ?object
     */
    protected ?object $event;

    /**
     * Template
     */
    const TEMPLATE = 'models/page-event.php';

    /**
     * Hooks
     *
     * @return void
     */
    public function hooks() : void {
        add_filter(
            'the_seo_framework_title_from_generation',
            Closure::fromCallable( [ $this, 'alter_title' ] )
        );

        add_action(
            'wp_head',
            Closure::fromCallable( [ $this, 'add_json_ld_data' ] )
        );
    }

    /**
     * Init
     */
    public function init() {
        $this->set_event();
    }

    /**
     * Add json+ld data to head
     */
    protected function add_json_ld_data() : void {
        $event = $this->get_event();

        if ( empty( $event ) ) {
            return;
        }

        printf(
            '<script type="application/ld+json">%s</script>',
            LinkedEvents::get_json_ld_data( $event ) // phpcs:ignore
        );
    }

    /**
     * Alter page title
     *
     * @param string $title Page title.
     *
     * @return string
     */
    protected function alter_title( string $title ) : string {
        if ( ! is_page_template( static::TEMPLATE ) ) {
            return $title;
        }

        $event = $this->get_event();

        if ( $event ) {
            $event = LinkedEvents::normalize_event( $event );
            $title = $event['name'];
        }

        return $title;
    }

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
     * Set view event
     */
    protected function set_event() : void {
        $event_id = $this->get_event_id();

        if ( empty( $event_id ) ) {
            $this->event = null;

            return;
        }

        try {
            $client = new LinkedEventsClient( PIRKANMAA_EVENTS_API_URL );
            $event  = $client->get(
                'event/' . $event_id,
                [ 'include' => 'organization,location' ]
            );

            if ( ! empty( $event ) ) {
                $this->event = $event;
            }
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );

            $this->event = null;
        }
    }

    /**
     * Get event
     *
     * @return object|null
     */
    protected function get_event() {
        return $this->event;
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
