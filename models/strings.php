<?php
/**
 * Class Strings
 * UI Strings
 */

/**
 * Class Strings
 */
class Strings extends \DustPress\Model {

    /**
     * Translated strings
     *
     * @return array
     */
    public function s() : array {
        return [
            'header' => [
                'skip_to_content' => _x( 'Skip to content', 'theme-frontend', 'tms-theme-base' ),
                'main_navigation' => _x( 'Main navigation', 'theme-frontend', 'tms-theme-base' ),
                'search'          => _x( 'Search', 'theme-frontend', 'tms-theme-base' ),
            ],
            '404'    => [
                'title'         => _x( 'Page not found', 'theme-frontend', 'tms-theme-base' ),
                'subtitle'      => _x(
                    'The content were looking for was not found',
                    'theme-frontend',
                    'tms-theme-base'
                ),
                'home_link_txt' => _x( 'To home page', 'theme-frontend', 'tms-theme-base' ),
            ],
            'video'  => [
                'skip_embed' => _x( 'Skip video embed', 'theme-frontend', 'tms-theme-base' ),
            ],
        ];
    }
}
