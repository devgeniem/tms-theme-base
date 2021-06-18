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
        add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_fields' ] )
        );
    }

    /**
     * Register fields
     */
    protected function register_fields() : void {
        try {
            $group_title = _x( 'Page Components', 'theme ACF', 'tms-theme-base' );

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_page_components' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'post_type', '==', PostType\Page::SLUG )
                ->add_rule( 'page_template', '!=', \PageFrontPage::TEMPLATE )
                ->add_rule( 'page_type', '!=', 'posts_page' );

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
                Layouts\ArticleLiftupLayout::class,
            ]
        );

        foreach ( $component_layouts as $component_layout ) {
            $components_field->add_layout( new $component_layout( $key ) );
        }

        return $components_field;
    }
}

( new PageGroup() );
