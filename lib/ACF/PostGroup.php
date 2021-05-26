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
 * Class PostGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class PostGroup {

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
            $field_group = ( new Group( 'Artikkelin lisätiedot' ) )
                ->set_key( 'fg_post_fields' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'post_type', '==', PostType\Post::SLUG );

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'normal' );

            $field_group->add_fields(
                apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_credits_tab( $field_group->get_key() ),
                        $this->get_related_posts_tab( $field_group->get_key() ),
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
     * Get writer tab
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_credits_tab( string $key ) : Field\Tab {
        $strings = [
            'tab'   => 'Tiedot',
            'writing_credits' => [
                'title'         => 'Kirjoittajan nimi',
                'instructions'  => '',
            ],
            'image_credits'  => [
                'title'        => 'Kuvaajan nimi',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $writing_credits_field = ( new Field\Text( $strings['writing_credits']['title'] ) )
            ->set_key( "${key}_writing_credits" )
            ->set_name( 'writing_credits' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['writing_credits']['instructions'] );

        $image_credits_field = ( new Field\Text( $strings['image_credits']['title'] ) )
            ->set_key( "${key}_image_credits" )
            ->set_name( 'image_credits' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['image_credits']['instructions'] );

        $tab->add_fields( [
            $writing_credits_field,
            $image_credits_field,
        ] );

        return $tab;
    }

    /**
     * Get related posts tab
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_related_posts_tab( string $key ) : Field\Tab {
        $strings = [
            'tab'   => 'Suositellut sisällöt',
            'title' => [
                'title'         => 'Otsikko',
                'instructions'  => 'Suositellut sisällöt noston otsikko',
                'default_value' => __( 'Related posts', 'tms-theme-base' ),
            ],
            'link'  => [
                'title'        => 'Arkistolinkki',
                'instructions' => 'Suositellut sisällöt noston linkki',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $title_field = ( new Field\Text( $strings['title']['title'] ) )
            ->set_key( "${key}_related_title" )
            ->set_name( 'related_title' )
            ->set_default_value( $strings['title']['default_value'] )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['title'] ) )
            ->set_key( "${key}_related_link" )
            ->set_name( 'related_link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $tab->add_fields( [
            $title_field,
            $link_field,
        ] );

        return $tab;
    }
}

( new PostGroup() );
