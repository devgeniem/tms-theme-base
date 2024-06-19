<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use TMS\Theme\Base\ACF\Layouts;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class PageGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class PageGroup {

    /**
     * PageGroup constructor.
     */
    public function __construct() {
        \add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_fields' ] ),
            100
        );

        \add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_page_settings' ] ),
            100
        );

        $this->add_anchor_fields();
    }

    /**
     * Register fields
     */
    protected function register_fields() : void {
        try {
            $group_title = _x( 'Page Components', 'theme ACF', 'tms-theme-base' );
            $key         = 'fg_page_components';

            $field_group = ( new Group( $group_title ) )
                ->set_key( $key );

            $rules = \apply_filters(
                'tms/acf/group/' . $key . '/rules',
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => PostType\Page::SLUG,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageFrontPage::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageEventsCalendar::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageOnepager::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageEventsCalendar::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageEventsSearch::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_type',
                        'operator' => '!=',
                        'value'    => 'posts_page',
                    ],
                ]
            );

            $rule_group = new RuleGroup();

            foreach ( $rules as $rule ) {
                $rule_group->add_rule(
                    $rule['param'],
                    $rule['operator'],
                    $rule['value'],
                );
            }

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'normal' )
                ->set_hidden_elements(
                    [
                        'discussion',
                        'comments',
                        'format',
                        'send-trackbacks',
                    ]
                );

            $field_group->add_fields(
                \apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_components_field( $field_group->get_key() ),
                    ]
                )
            );

            $field_group = \apply_filters(
                'tms/acf/group/' . $field_group->get_key(),
                $field_group
            );

            $field_group->register();
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTraceAsString() );
        }
    }

    /**
     * Register page settings fields
     */
    protected function register_page_settings() : void {
        try {
            $group_title = \_x( 'Page settings', 'theme ACF', 'tms-theme-base' );
            $key = 'fg_page_settings';

            $field_group = ( new Group( $group_title ) )
                ->set_key( $key );

            $rules = \apply_filters(
                'tms/acf/group/' . $key . '/rules',
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => PostType\Page::SLUG,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageFrontPage::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageEventsCalendar::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageOnepager::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageEventsCalendar::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_template',
                        'operator' => '!=',
                        'value'    => \PageEventsSearch::TEMPLATE,
                    ],
                    [
                        'param'    => 'page_type',
                        'operator' => '!=',
                        'value'    => 'posts_page',
                    ],
                ]
            );

            $rule_group = new RuleGroup();

            foreach ( $rules as $rule ) {
                $rule_group->add_rule(
                    $rule['param'],
                    $rule['operator'],
                    $rule['value'],
                );
            }

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'side' );

            $field_group->add_fields(
                \apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_overlay_field( $field_group->get_key() ),
                    ]
                )
            );

            $field_group = \apply_filters(
                'tms/acf/group/' . $field_group->get_key(),
                $field_group
            );

            $field_group->register();
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTraceAsString() );
        }
    }

    /**
     * Get overlay field
     *
     * @param string $key Field group key.
     *
     * @return Field\FlexibleContent
     * @throws Exception In case of invalid option.
     */
    protected function get_overlay_field( string $key ) : Field\TrueFalse {
        $strings = [
            'remove_overlay' => [
                'title'        => \_x( 'Remove hero-image overlay', 'theme ACF', 'tms-theme-base' ),
                'instructions' => \_x( 'Remove hero-image overlay and move the heading under the hero-element', 'theme ACF', 'tms-theme-base' ),
            ],
        ];

        $overlay_field = ( new Field\TrueFalse( $strings['remove_overlay']['title'] ) )
            ->set_key( "{$key}_remove_overlay" )
            ->set_name( 'remove_overlay' )
            ->use_ui()
            ->set_instructions( $strings['remove_overlay']['instructions'] );

        return $overlay_field;
    }

    /**
     * Get components fields
     *
     * @param string $key Field group key.
     *
     * @return Field\FlexibleContent
     * @throws Exception In case of invalid option.
     */
    protected function get_components_field( string $key ) : Field\FlexibleContent {
        $strings = [
            'components' => [
                'title'        => \_x( 'Components', 'theme ACF', 'tms-theme-base' ),
                'instructions' => '',
            ],
        ];

        $components_field = ( new Field\FlexibleContent( $strings['components']['title'] ) )
            ->set_key( "{$key}_components" )
            ->set_name( 'components' )
            ->set_instructions( $strings['components']['instructions'] );

        $component_layouts = \apply_filters(
            'tms/acf/field/' . $components_field->get_key() . '/layouts',
            [
                Layouts\ImageBannerLayout::class,
                Layouts\CallToActionLayout::class,
                Layouts\ContentColumnsLayout::class,
                Layouts\LogoWallLayout::class,
                Layouts\MapLayout::class,
                Layouts\IconLinksLayout::class,
                Layouts\SocialMediaLayout::class,
                Layouts\ImageCarouselLayout::class,
                Layouts\SubpageLayout::class,
                Layouts\TextBlockLayout::class,
                Layouts\GridLayout::class,
                Layouts\EventsLayout::class,
                Layouts\ArticlesLayout::class,
                Layouts\BlogArticlesLayout::class,
                Layouts\SitemapLayout::class,
                Layouts\NoticeBannerLayout::class,
                Layouts\GravityFormLayout::class,
                Layouts\ContactsLayout::class,
                Layouts\AccessibilityIconLinksLayout::class,
                Layouts\ShareLinksLayout::class,
                Layouts\CountdownLayout::class,
                Layouts\VideoLayout::class,
                Layouts\SomeLinkListLayout::class,
            ],
            $key
        );

        foreach ( $component_layouts as $component_layout ) {
            $components_field->add_layout( new $component_layout( $key ) );
        }

        return $components_field;
    }

    /**
     * Add HTML-anchor field to layout fields.
     */
    private function add_anchor_fields() : void {
        $keys = [
            'image_banner',
            'call_to_action',
            'content_columns',
            'logo_wall',
            'map',
            'icon_links',
            'social_media',
            'image_carousel',
            'subpages' ,
            'textblock',
            'grid',
            'events',
            'articles',
            'blog_articles',
            'sitemap',
            'notice_banner',
            'gravityform',
            'contacts',
            'acc_icon_links',
            'share_links',
            'countdown',
            'video',
            'some_link_list',
        ];

        foreach ( $keys as $component ) {
            if ( empty( $component ) ) {
                continue;
            }

            \add_filter(
                "tms/acf/layout/fg_page_components_$component/fields",
                function ( $fields ) use ( $component ) {
                    $anchor_field = ( new Field\Text( 'HTML-ankkuri' ) )
                        ->set_key( $component . '_anchor' )
                        ->set_name( 'anchor' )
                        ->set_instructions( 'Kirjoita sana tai pari, ilman välilyöntejä,
                         luodaksesi juuri tälle lohkolle uniikki verkko-osoite, jota kutsutaan "ankkuriksi".
                         Sen avulla voit luoda linkin suoraan tähän osioon sivullasi.' );

                    array_unshift( $fields, $anchor_field );

                    return $fields;
                },
                10,
                1
            );
        }
    }
}

( new PageGroup() );
