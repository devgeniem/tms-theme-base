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
            'header'             => [
                'skip_to_content' => _x( 'Skip to content', 'theme-frontend', 'tms-theme-base' ),
                'main_navigation' => _x( 'Main navigation', 'theme-frontend', 'tms-theme-base' ),
                'search'          => _x( 'Search', 'theme-frontend', 'tms-theme-base' ),
            ],
            '404'                => [
                'title'         => _x( 'Page not found', 'theme-frontend', 'tms-theme-base' ),
                'subtitle'      => _x(
                    'The content were looking for was not found',
                    'theme-frontend',
                    'tms-theme-base'
                ),
                'home_link_txt' => _x( 'To home page', 'theme-frontend', 'tms-theme-base' ),
            ],
            'video'              => [
                'skip_embed' => _x( 'Skip video embed', 'theme-frontend', 'tms-theme-base' ),
            ],
            'share'              => [
                'share_article' => _x( 'Share Article', 'theme-frontend', 'tms-theme-base' ),
            ],
            'gallery'            => [
                'close'    => _x( 'Close', 'theme-frontend', 'tms-theme-base' ),
                'next'     => _x( 'Next', 'theme-frontend', 'tms-theme-base' ),
                'open'     => _x( 'Open', 'theme-frontend', 'tms-theme-base' ),
                'previous' => _x( 'Previous', 'theme-frontend', 'tms-theme-base' ),
            ],
            'footer'             => [
                'to_main_site' => _x( 'Move to tampere.fi', 'theme-frontend', 'tms-theme-base' ),
                'back_to_top'  => _x( 'Back to top', 'theme-frontend', 'tms-theme-base' ),
            ],
            'common'             => [
                'target_blank' => _x( 'Opens in a new window', 'theme-frontend', 'tms-theme-base' ),
            ],
            'single'             => [
                'image_credits'   => _x( 'Image:', 'theme-frontend', 'tms-theme-base' ),
                'writing_credits' => _x( 'Text:', 'theme-frontend', 'tms-theme-base' ),
            ],
            'password_protected' => [
                'input_label' => _x( 'Password:', 'theme-frontend', 'tms-theme-base' ),
                'button_text' => _x( 'Enter', 'theme-frontend', 'tms-theme-base' ),
                'message'     => _x(
                    'This content is password protected. To view it please enter your password below:',
                    'theme-frontend',
                    'tms-theme-base'
                ),
            ],
        ];
    }
}
