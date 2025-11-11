<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class HomeGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class HomeGroup {

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
            $field_group = ( new Group( 'Asetukset' ) )
                ->set_key( 'fg_home_fields' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'page_type', '==', 'posts_page' );

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'normal' );

            $field_group->add_fields(
                apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_highlight_tab( $field_group->get_key() ),
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
     * Get highlight tab
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_highlight_tab( string $key ) : Field\Tab {
        $strings = [
            'tab'       => 'Korostettu artikkeli',
            'highlight' => [
                'title'        => 'Korostettu artikkeli',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $higlight_field = ( new Field\PostObject( $strings['highlight']['title'] ) )
            ->set_key( "{$key}_highlight" )
            ->set_name( 'highlight' )
            ->set_post_types( [ PostType\Post::SLUG ] )
            ->allow_null()
            ->set_instructions( $strings['highlight']['instructions'] );

        // Filter out drafts
        add_filter(
            'acf/fields/post_object/query/name=highlight',
            \Closure::fromCallable( [ $this, 'filter_out_drafts' ] ),
            10,
            1
        );

        $tab->add_fields( [
            $higlight_field,
        ] );

        return $tab;
    }

    /**
     * Show only published posts on the highlight field.
     *
     * @param array $options Field options array.
     *
     * @return array
     */
    protected function filter_out_drafts( array $options ) : array {
        $options['post_status'] = [ 'publish' ];

        return $options;
    }
}

( new HomeGroup() );
