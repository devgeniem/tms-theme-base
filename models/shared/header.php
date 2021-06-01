<?php
/**
 * Header model
 */

use TMS\Theme\Base\Traits;

/**
 * Header class
 */
class Header extends \DustPress\Model {

    use Traits\Breadcrumbs;

    /**
     * Breadcrumbs
     *
     * @return array
     */
    public function breadcrumbs() : array {
        $current_object = get_queried_object();

        if ( $current_object === null || empty( $current_object ) ) {
            return [];
        }

        $breadcrumbs  = [];
        $home_url     = trailingslashit( get_home_url() );
        $current_id   = (int) $current_object->ID;
        $current_type = (string) $current_object->post_type;

        $breadcrumbs['home'] = $this->get_home_link();

        $breadcrumbs = $this->get_ancestors( $current_id, $current_type, $breadcrumbs );
        $breadcrumbs = $this->prepare_by_type( $current_type, $current_id, $home_url, $breadcrumbs );

        return (array) apply_filters(
            'tms/theme/breadcrumbs/' . $current_type,
            $this->format_breadcrumbs( $breadcrumbs ),
            $breadcrumbs,
            $current_object
        );
    }
}
