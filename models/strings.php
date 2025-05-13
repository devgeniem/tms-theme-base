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
     * Constructor
     *
     * @param array $args   Model arguments.
     * @param mixed $parent Set model parent.
     */
    public function __construct( $args = [], $parent = null ) {
        parent::__construct( $args, $parent );

        $this->hooks();
    }

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'dustpress/pagination/data',
            \Closure::fromCallable( [ $this, 'add_pagination_translations' ] )
        );
    }

    /**
     * Translated strings
     *
     * @return array
     */
    public function s() : array {
        return [
            'header'             => [
                'skip_to_content'           => _x( 'Skip to content', 'theme-frontend', 'tms-theme-base' ),
                'main_navigation'           => _x( 'Main navigation', 'theme-frontend', 'tms-theme-base' ),
                'frequently_searched_pages' => _x( 'Frequently searched pages', 'theme-frontend', 'tms-theme-base' ),
                'open_menu'                 => _x( 'Open menu', 'theme-frontend', 'tms-theme-base' ),
                'main_menu'                 => _x( 'Main menu', 'theme-frontend', 'tms-theme-base' ),
                'close_menu'                => _x( 'Close menu', 'theme-frontend', 'tms-theme-base' ),
                'language_navigation'       => _x( 'Language navigation', 'theme-frontend', 'tms-theme-base' ),
                'open_search'               => _x( 'Open search form', 'theme-frontend', 'tms-theme-base' ),
                'open_lang_nav'             => _x( 'Open language navigation', 'theme-frontend', 'tms-theme-base' ),
                'current_lang'              => _x( 'Current language: ', 'theme-frontend', 'tms-theme-base' ),
                'search'                    => _x( 'Search', 'theme-frontend', 'tms-theme-base' ),
                'search_title'              => _x( 'Search', 'theme-frontend', 'tms-theme-base' ),
                'search_input_label'        => _x( 'Search from site', 'theme-frontend', 'tms-theme-base' ),
                'search_input_placeholder'  => _x( 'Search from site', 'theme-frontend', 'tms-theme-base' ),
                'exception_close_button'    => _x( 'Close', 'theme-frontend', 'tms-theme-base' ),
                'home'                      => _x( 'To home page', 'theme-frontend', 'tms-theme-base' ),
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
                'play'       => _x( 'Play video', 'theme-frontend', 'tms-theme-base' ),
                'pause'      => _x( 'Pause video', 'theme-frontend', 'tms-theme-base' ),
            ],
            'social_media'       => [
                'skip_embed' => _x( 'Skip social media embed', 'theme-frontend', 'tms-theme-base' ),
            ],
            'share'              => [
                'share_article'         => _x( 'Share Article', 'theme-frontend', 'tms-theme-base' ),
                'share_event'           => _x( 'Share Event', 'theme-frontend', 'tms-theme-base' ),
                'share_to_social_media' => _x( 'Share to social media', 'theme-frontend', 'tms-theme-base' ),
            ],
            'gallery'            => [
                'close'           => _x( 'Close', 'theme-frontend', 'tms-theme-base' ),
                'next'            => _x( 'Next', 'theme-frontend', 'tms-theme-base' ),
                'open'            => _x( 'Open', 'theme-frontend', 'tms-theme-base' ),
                'previous'        => _x( 'Previous', 'theme-frontend', 'tms-theme-base' ),
                'goto'            => _x( 'Go to slide', 'theme-frontend', 'tms-theme-base' ),
                'centered'        => _x( 'Centered', 'theme-frontend', 'tms-theme-base' ),
                'slide'           => _x( 'Slide', 'theme-frontend', 'tms-theme-base' ),
                'image_carousel'  => _x( 'Image carousel', 'theme-frontend', 'tms-theme-base' ),
                'modal_carousel'  => _x( 'Modal image carousel', 'theme-frontend', 'tms-theme-base' ),
                'browsing_images' => _x( 'Browsing images', 'theme-frontend', 'tms-theme-base' ),
                'main_carousel'   => _x( 'Main image carousel', 'theme-frontend', 'tms-theme-base' ),
            ],
            'footer'             => [
                'to_main_site' => _x( 'Move to tampere.fi', 'theme-frontend', 'tms-theme-base' ),
                'back_to_top'  => _x( 'Back to top', 'theme-frontend', 'tms-theme-base' ),
            ],
            'common'             => [
                'target_blank'  => _x( 'Opens in a new window', 'theme-frontend', 'tms-theme-base' ),
                'external_link' => _x( 'The link takes you to an external website', 'theme-frontend', 'tms-theme-base' ),
                'all'           => _x( 'All', 'theme-frontend', 'tms-theme-base' ),
                'read_more'     => _x( 'Read more', 'theme-frontend', 'tms-theme-base' ),
            ],
            'single'             => [
                'image_credits'   => _x( 'Image:', 'theme-frontend', 'tms-theme-base' ),
                'writing_credits' => _x( 'Text:', 'theme-frontend', 'tms-theme-base' ),
                'article_type'    => _x( 'Articletype:', 'theme-frontend', 'tms-theme-base' ),
            ],
            'home'               => [
                'month'              => _x( 'Month', 'theme-frontend', 'tms-theme-base' ),
                'year'               => _x( 'Year', 'theme-frontend', 'tms-theme-base' ),
                'no_results'         => _x( 'No results', 'theme-frontend', 'tms-theme-base' ),
                'filter_by_category' => _x( 'Filter by Category', 'theme-frontend', 'tms-theme-base' ),
                'description'        => _x( 'The page reloads after the selection.', 'theme-frontend', 'tms-theme-base' ),
            ],
            'months'             => [
                'january'   => _x( 'January', 'theme-frontend', 'tms-theme-base' ),
                'february'  => _x( 'February', 'theme-frontend', 'tms-theme-base' ),
                'march'     => _x( 'March', 'theme-frontend', 'tms-theme-base' ),
                'april'     => _x( 'April', 'theme-frontend', 'tms-theme-base' ),
                'may'       => _x( 'May', 'theme-frontend', 'tms-theme-base' ),
                'june'      => _x( 'June', 'theme-frontend', 'tms-theme-base' ),
                'july'      => _x( 'July', 'theme-frontend', 'tms-theme-base' ),
                'august'    => _x( 'August', 'theme-frontend', 'tms-theme-base' ),
                'september' => _x( 'September', 'theme-frontend', 'tms-theme-base' ),
                'october'   => _x( 'October', 'theme-frontend', 'tms-theme-base' ),
                'november'  => _x( 'November', 'theme-frontend', 'tms-theme-base' ),
                'december'  => _x( 'December', 'theme-frontend', 'tms-theme-base' ),
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
            'sibling_navigation' => [
                'sibling_navigation' => _x( 'Sibling pages', 'theme-frontend', 'tms-theme-base' ),
            ],
            'comments'           => [
                'comments_title' => _x( 'Comments', 'theme-frontend', 'tms-theme-base' ),
                'close_notice'   => _x( 'Close', 'theme-frontend', 'tms-theme-base' ),
            ],
            'blog_article'       => [
                'toggle_details'    => _x( 'Show description', 'theme-frontend', 'tms-theme-base' ),
                'archive_link_text' => _x( 'All articles', 'theme-frontend', 'tms-theme-base' ),
            ],
            'event'              => [
                'date'     => _x( 'Event date', 'theme-frontend', 'tms-theme-base' ),
                'time'     => _x( 'Event time', 'theme-frontend', 'tms-theme-base' ),
                'location' => _x( 'Event location', 'theme-frontend', 'tms-theme-base' ),
                'price'    => _x( 'Event price', 'theme-frontend', 'tms-theme-base' ),
            ],
            'sitemap'            => [
                'open'  => _x( 'Open', 'theme-frontend', 'tms-theme-base' ),
                'close' => _x( 'Close', 'theme-frontend', 'tms-theme-base' ),
            ],
            'contact_search'     => [
                'label'             => _x( 'Search contacts', 'theme-frontend', 'tms-theme-base' ),
                'input_placeholder' => _x( 'Search', 'theme-frontend', 'tms-theme-base' ),
                'submit_value'      => _x( 'Search', 'theme-frontend', 'tms-theme-base' ),
            ],
            'artist'             => [
                'open'            => _x( 'Open', 'theme-frontend', 'tms-theme-base' ),
                'close'           => _x( 'Close', 'theme-frontend', 'tms-theme-base' ),
                'related_artwork' => _x( 'Related artwork', 'theme-frontend', 'tms-theme-base' ),
            ],
            'artwork'            => [
                'artist_link'     => _x( 'Show artist', 'theme-frontend', 'tms-theme-base' ),
                'related_art'     => _x( 'Artwork by the same artist', 'theme-frontend', 'tms-theme-base' ),
                'related_artwork' => _x( 'Related artwork', 'theme-frontend', 'tms-theme-base' ),
            ],
            'search'             => [
                'filter_by_post_type' => _x( 'Filter by type', 'theme-frontend', 'tms-theme-base' ),
                'filter_by_date'      => _x( 'Publish date', 'theme-frontend', 'tms-theme-base' ),
                'breadcrumbs'         => _x( 'Page location:', 'theme-frontend', 'tms-theme-base' ),
                'clear'               => _x( 'Clear the form', 'theme-frontend', 'tms-theme-base' )
            ],
            // Use the Duet Date Picker keys for strings
            'datepicker'         => [
                'buttonLabel'         => _x( 'Pick a date', 'theme-frontend', 'tms-theme-base' ),
                'placeholder'         => _x( 'dd.mm.yyyy', 'theme-frontend', 'tms-theme-base' ),
                'selectedDateMessage' => _x( 'The chosen date is', 'theme-frontend', 'tms-theme-base' ),
                'prevMonthLabel'      => _x( 'Previous month', 'theme-frontend', 'tms-theme-base' ),
                'nextMonthLabel'      => _x( 'Next month', 'theme-frontend', 'tms-theme-base' ),
                'monthSelectLabel'    => _x( 'Month', 'theme-frontend', 'tms-theme-base' ),
                'yearSelectLabel'     => _x( 'Year', 'theme-frontend', 'tms-theme-base' ),
                'closeLabel'          => _x( 'Close window', 'theme-frontend', 'tms-theme-base' ),
                'calendarHeading'     => _x( 'Pick a date', 'theme-frontend', 'tms-theme-base' ),
                'dayNames'            => [
                    _x( 'Sunday', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Monday', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Tuesday', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Wednesday', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Thursday', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Friday', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Saturday', 'theme-frontend', 'tms-theme-base' ),
                ],
                'monthNames'          => [
                    _x( 'January', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'February', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'March', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'April', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'May', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'June', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'July', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'August', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'September', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'October', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'November', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'December', 'theme-frontend', 'tms-theme-base' ),
                ],
                'monthNamesShort'     => [
                    _x( 'Jan', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Feb', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Mar', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Apr', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'May', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Jun', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Jul', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Aug', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Sept', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Oct', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Nov', 'theme-frontend', 'tms-theme-base' ),
                    _x( 'Dec', 'theme-frontend', 'tms-theme-base' ),
                ],
            ],
            'countdown'          => [
                'days'    => _x( 'Days', 'theme-frontend', 'tms-theme-base' ),
                'hours'   => _x( 'Hours', 'theme-frontend', 'tms-theme-base' ),
                'minutes' => _x( 'Minutes', 'theme-frontend', 'tms-theme-base' ),
                'seconds' => _x( 'Seconds', 'theme-frontend', 'tms-theme-base' ),
            ],
            'modaal'             => [
                'accessible_title' => _x( 'Enlarged image', 'theme-frontend', 'tms-theme-base' ),
                'close'            => _x( 'Close', 'theme-frontend', 'tms-theme-base' ),
            ],
        ];
    }

    /**
     * Add translations to pagination
     *
     * @param object $data Pagination data.
     *
     * @return object
     */
    public function add_pagination_translations( $data ) {
        $data->S->aria_label = __( 'Pagination', 'tms-theme-base' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

        return $data;
    }
}
