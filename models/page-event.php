<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Tapahtuma
 */

use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Traits\Components;
use TMS\Theme\Base\Traits\Sharing;
use TMS\Theme\Base\EventzClient;
use TMS\Theme\Base\Eventz;
use TMS\Theme\Base\Localization;

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

        // og:title.
        add_filter(
            'the_seo_framework_title_from_custom_field',
            Closure::fromCallable( [ $this, 'alter_title' ] )
        );

        // og:image.
        add_filter(
            'the_seo_framework_image_generation_params',
            Closure::fromCallable( [ $this, 'alter_image' ] )
        );

        // og:description.
        add_filter(
            'the_seo_framework_custom_field_description',
            Closure::fromCallable( [ $this, 'alter_desc' ] )
        );

        // og:url.
        add_filter(
            'the_seo_framework_ogurl_output',
            Closure::fromCallable( [ $this, 'alter_url' ] )
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
            Eventz::get_json_ld_data( $event ) // phpcs:ignore
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
            $event = Eventz::normalize_event( $event );
            $title = $event['name'];
        }

        return $title;
    }

    /**
     * Add image for og:image.
     *
     * @param array $params An array of SEO framework image parameters.
     *
     * @return array
     */
    protected function alter_image( $params ) {
        $event = $this->get_event();

        if ( $event ) {
            // Ensure our custom generator is ran first.
            $params['cbs'] = array_merge(
                [ 'tms' => Closure::fromCallable( [ $this, 'seo_image_generator' ] ) ],
                $params['cbs']
            );
        }

        return $params;
    }

    /**
     * Custom generator for The SEO Framework og images.
     *
     * @yield array : {
     *     string url: The image URL,
     *     int     id: The image ID,
     * }
     */
    protected function seo_image_generator() {
        $event = $this->get_event();
        $image = $event->images->imageMobile ?? false;

        if ( $image ) {
            yield [
                'url' => $image->url ?? '',
            ];
        }
    }

    /**
     * This sets the content of og:description.
     *
     * @param string $description The original description.
     *
     * @return string
     */
    protected function alter_desc( $description ) {
        $event = $this->get_event();
        $event = Eventz::normalize_event( $event );

        if ( $event ) {
            $description = $event['short_description'];
        }

        return $description;
    }

    /**
     * This sets the content of og:url.
     *
     * @param string $url The original URL.
     *
     * @return string
     */
    protected function alter_url( $url ) {
        $event = $this->get_event();

        if ( $event ) {
            $event = Eventz::normalize_event( $event );
            $url   = $event['url'];
        }

        return $url;
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

        return ! empty( $event->images->imageDesktop->url )
            ? $event->images->imageDesktop->url
            : $default_image;
    }

    /**
     * Hero image credits
     *
     * @return string|null
     */
    public function hero_image_credits() : ?string {
        $event = $this->get_event();

        if ( empty( $event ) ) {
            return null;
        }

        return Settings::get_setting( 'events_default_image_credits' ) ?? null;
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
            $lang_key = Localization::get_current_language();
            $client   = new EventzClient( PIRKANMAA_EVENTZ_API_URL, PIRKANMAA_EVENTZ_API_KEY );
            $event    = $client->get_item( $event_id, $lang_key );

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
            'normalized' => Eventz::normalize_event( $event ),
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

        if ( empty( $parent ) || \get_post_status( $parent ) !== 'publish' ) {
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
