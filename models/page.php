<?php
/**
 * Define the generic Page class.
 */

use TMS\Theme\Base\PostType;
use TMS\Theme\Base\Traits;

/**
 * The Page class.
 */
class Page extends BaseModel {

    use Traits\Components;
    use Traits\Breadcrumbs;

    /**
     * Page breadcrumbs
     *
     * @return array
     */
    public function breadcrumbs() : array {
        $breadcrumbs = [];
        $home_url    = trailingslashit( get_home_url() );
        $current_id  = get_queried_object_id();

        $breadcrumbs['home'] = $this->get_home_link();

        $breadcrumbs = $this->get_ancestors(
            $current_id,
            PostType\Page::SLUG,
            $breadcrumbs
        );

        /**
         * Add current page to breadcrumbs and set its
         * link status to false, unless it's the front page, then remove it.
         */
        if ( trailingslashit( get_the_permalink( $current_id ) ) !== $home_url ) {
            $breadcrumbs[] = [
                'title'     => get_the_title( $current_id ),
                'permalink' => false,
                'icon'      => false,
                'is_active' => true,
            ];
        }
        else {
            unset( $breadcrumbs['home'] ); // Not showing frontpage on frontpage.
        }

        return $this->format_breadcrumbs( $breadcrumbs );
    }
}
