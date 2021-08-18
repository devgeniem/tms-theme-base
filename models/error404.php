<?php
/**
 * Error page
 */

use TMS\Theme\Base\Settings;

/**
 * Class for handling 404 errors
 */
class Error404 extends BaseModel {

    /**
     * Return 404 page title.
     */
    public function title() {
        return Settings::get_setting( '404_title' );
    }

    /**
     * Return 404 page description.
     */
    public function description() {
        return Settings::get_setting( '404_description' );
    }

    /**
     * Return 404 page image.
     */
    public function image() {
        return Settings::get_setting( '404_image' );
    }

    /**
     * Return page links.
     *
     * @return array[]
     */
    public function links() : array {
        $links = [
            $this->get_home_link(),
        ];

        $search_link = $this->get_search_link();

        if ( ! empty( $search_link ) ) {
            $links[] = $search_link;
        }

        return $links;
    }

    /**
     * Get search button content.
     *
     * @return array
     */
    private function get_search_link() : ?array {
        $home_url = $this->get_home_url();

        if ( Settings::get_setting( 'hide_search' ) ) {
            return null;
        }

        return apply_filters(
            'tms/theme/error404/search_link',
            [
                'title'   => _x( 'Go to search', 'theme-frontend', 'tms-theme-base' ),
                'url'     => "$home_url?s",
                'classes' => 'is-inverted',
                'icon'    => 'search',
            ]
        );
    }

    /**
     * Get home button content.
     *
     * @return array
     */
    private function get_home_link() : array {
        return apply_filters(
            'tms/theme/error404/home_link',
            [
                'title' => _x( 'Return to home', 'theme-frontend', 'tms-theme-base' ),
                'url'   => $this->get_home_url(),
                'icon'  => 'chevron-right',
            ]
        );
    }

    /**
     * Get home page url.
     *
     * @return string
     */
    private function get_home_url() : string {
        return defined( 'DPT_PLL_ACTIVE' ) && DPT_PLL_ACTIVE
            ? \pll_home_url()
            : \home_url();
    }
}
