<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use PageOnepager;
use TMS\Theme\Base\ACF\Layouts;
use TMS\Theme\Base\Logger;

/**
 * Class OnepagerGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class OnepagerGroup {

    /**
     * PageGroup constructor.
     */
    public function __construct() {
        add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_fields' ] )
        );

        $this->add_layout_filters();
    }

    /**
     * Register fields
     */
    protected function register_fields() : void {
        try {
            $group_title = _x( 'Page Components', 'theme ACF', 'tms-theme-base' );

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_onepager_components' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'page_template', '==', PageOnepager::TEMPLATE );

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'normal' )
                ->set_hidden_elements(
                    [
                        'discussion',
                        'comments',
                        'format',
                        'send-trackbacks',
                        'editor',
                    ]
                );

            $field_group->add_fields(
                apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_components_field( $field_group->get_key() ),
                    ]
                )
            );

            $field_group = apply_filters(
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
     * Get header fields
     *
     * @param string $key Field group key.
     *
     * @return Field\FlexibleContent
     * @throws Exception In case of invalid option.
     */
    protected function get_components_field( string $key ) : Field\FlexibleContent {
        $strings = [
            'components' => [
                'title'        => _x( 'Components', 'theme ACF', 'tms-theme-base' ),
                'instructions' => '',
            ],
        ];

        $components_field = ( new Field\FlexibleContent( $strings['components']['title'] ) )
            ->set_key( "${key}_components" )
            ->set_name( 'components' )
            ->set_instructions( $strings['components']['instructions'] );

        $component_layouts = apply_filters(
            'tms/acf/field/' . $components_field->get_key() . '/layouts',
            [
                Layouts\HeroLayout::class,
                Layouts\ImageBannerLayout::class,
                Layouts\CallToActionLayout::class,
                Layouts\ContentColumnsLayout::class,
                Layouts\LogoWallLayout::class,
                Layouts\MapLayout::class,
                Layouts\IconLinksLayout::class,
                Layouts\SocialMediaLayout::class,
                Layouts\ImageCarouselLayout::class,
                Layouts\TextBlockLayout::class,
                Layouts\GridLayout::class,
                Layouts\EventsLayout::class,
                Layouts\ArticlesLayout::class,
            ]
        );

        foreach ( $component_layouts as $component_layout ) {
            $components_field->add_layout( new $component_layout( $key ) );
        }

        return $components_field;
    }

    /**
     * Add menu_text field to layout fields.
     */
    private function add_layout_filters() {
        $components = [
            'hero',
            'image_banner',
            'call_to_action',
            'content_columns',
            'logo_wall',
            'map',
            'icon_links',
            'social_media',
            'image_carousel',
            'text_block',
            'grid',
            'events',
            'articles',
            'textblock',
        ];

        foreach ( $components as $component ) {
            add_filter(
                "tms/acf/layout/fg_onepager_components_$component/fields",
                function ( $fields ) use ( $component ) {
                    $menu_text_field = ( new Field\Text( 'Valikkoteksti' ) )
                        ->set_key( $component . '_menu_text' )
                        ->set_name( 'menu_text' )
                        ->set_instructions( '' );

                    array_unshift( $fields, $menu_text_field );

                    return $fields;
                },
                10,
                1
            );

            add_filter(
                "tms/acf/layout/$component/data",
                function ( $data ) {
                    if ( isset( $data['menu_text'] ) && ! empty( $data['menu_text'] ) ) {
                        $data['anchor'] = sanitize_title( $data['menu_text'] );
                    }

                    return $data;
                },
                10,
                1
            );
        }
    }
}

( new OnepagerGroup() );
