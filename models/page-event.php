<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Tapahtuma
 */

use Geniem\LinkedEvents\LinkedEventsClient;
use TMS\Theme\Base\LinkedEvents;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Traits\Components;
use TMS\Theme\Base\Traits\Sharing;

/**
 * The PageEvent class.
 */
class PageEvent extends BaseModel {

    use Sharing;
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

        add_filter(
            'tms/base/breadcrumbs/after_prepare',
            Closure::fromCallable( [ $this, 'alter_breadcrumbs' ] )
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

        if ( empty( $event ) ) {
            return false;
        }

        $default_image = empty( Settings::get_setting( 'events_default_image' ) )
            ? null
            : wp_get_attachment_image_url( Settings::get_setting( 'events_default_image' ), 'large' );

        return ! empty( $event->images[0]->url )
            ? $event->images[0]->url
            : $default_image;
    }

    /**
     * Get event id or redirect to home.
     *
     * @return string
     */
    protected function get_event_id() : string {
        $event_id = get_query_var( 'event-id', null );

        if ( ! empty( $event_id ) ) {
            return $event_id;
        }

        wp_safe_redirect( $this->get_home_url() );

        exit();
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
     * Template classes.
     *
     * @return array
     */
    public function template_classes() {
        $hero_info_classes = apply_filters(
            'tms/theme/event/hero_info_classes',
            'has-background-secondary has-text-secondary-invert'
        );

        $hero_icon_classes = apply_filters(
            'tms/theme/event/hero_icon_classes',
            'is-accent'
        );

        $info_group_title = apply_filters(
            'tms/theme/event/group_title',
            [
                'title' => 'has-background-secondary',
                'icon'  => 'is-accent',
            ]
        );

        $info_group_classes = apply_filters(
            'tms/theme/event/info_group_classes',
            'has-background-secondary--opaque has-text-secondary-invert'
        );

        $info_button_classes = apply_filters(
            'tms/theme/event/info_button_classes',
            ''
        );

        return [
            'hero_info'        => $hero_info_classes,
            'hero_icon'        => $hero_icon_classes,
            'info_group_title' => $info_group_title,
            'info_group'       => $info_group_classes,
            'info_button'      => $info_button_classes,
        ];
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

    /**
     * Alter breadcrumbs
     *
     * @param array $breadcrumbs Array of breadcrumbs.
     *
     * @return array
     */
    public function alter_breadcrumbs( array $breadcrumbs ) : array {
        $referer  = wp_get_referer();
        $home_url = DPT_PLL_ACTIVE && function_exists( 'pll_current_language' )
            ? pll_home_url()
            : home_url();

        if ( false === strpos( $referer, $home_url ) ) {
            return $breadcrumbs;
        }

        // Resolve the parent page ignoring f.ex. paging parameters in the URL: /page/2/
        $parent = str_replace( $home_url, '', $referer );
        $parent = strpos( $parent, '/' ) !== false ? explode( '/', $parent )[0] : $parent;
        $parent = get_page_by_path( $parent );

        if ( empty( $parent ) ) {
            return $breadcrumbs;
        }

        $last = array_pop( $breadcrumbs );

        $breadcrumbs[] = [
            'title'     => $parent->post_title,
            'permalink' => get_the_permalink( $parent->ID ),
        ];

        $breadcrumbs[] = $last;

        return $breadcrumbs;
    }
}
