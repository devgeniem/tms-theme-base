<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use Geniem\LinkedEvents\LinkedEventsClient;
use TMS\Theme\Base\LinkedEvents;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Traits\Components;
use TMS\Theme\Base\Traits;

/**
 * The SingleDynamicEventCpt class.
 */
class SingleDynamicEventCpt extends PageEvent {

    use Traits\Sharing;

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter( 'tms/theme/breadcrumbs/show_breadcrumbs_in_header', fn() => false );

        add_filter(
            'tms/base/breadcrumbs/after_prepare',
            Closure::fromCallable( [ $this, 'alter_breadcrumbs' ] )
        );
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
     * Get event id
     *
     * @return string
     */
    protected function get_event_id() : string {
        return get_field( 'event' ) ?? '';
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

        $parent = get_page_by_path(
            str_replace( $home_url, '', $referer )
        );

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
