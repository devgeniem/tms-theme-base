<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use TMS\Theme\Base\ACF\Field\TextEditor;
use TMS\Theme\Base\ACF\Fields\EventsFields;
use TMS\Theme\Base\Logger;

/**
 * Class EventsCalendarGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class PageEventsCalendarGroup {

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
            $group_title = _x( 'Kalenterin asetukset', 'theme ACF', 'tms-theme-base' );

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_page_events_calendar' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'page_template', '==', \PageEventsCalendar::TEMPLATE );

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
                        $this->get_page_fields( $field_group->get_key() ),
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
     * Get page fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_page_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'         => 'Tapahtumat',
            'description' => [
                'title'        => 'Kuvausteksti',
                'instructions' => '',
            ],
            'layout'      => [
                'title'        => 'Asettelu',
                'instructions' => '',
                'choices'      => [
                    'grid' => 'Ruudukko',
                    'list' => 'Lista',
                ],
            ],
            'disable_pagination' => [
                'title'        => 'Poista sivutus käytöstä',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $description_field = ( new TextEditor( $strings['description']['title'] ) )
            ->set_key( "${key}_description" )
            ->set_name( 'description' )
            ->redipress_include_search()
            ->set_instructions( $strings['description']['instructions'] );

        $search_fields = new EventsFields( 'Tapahtumahaku', $key );
        $search_fields->remove_field( 'title' );
        $search_fields->remove_field( 'page_size' );

        $layout_field = ( new Field\Radio( $strings['layout']['title'] ) )
            ->set_key( "${key}_layout" )
            ->set_name( 'layout' )
            ->set_choices( $strings['layout']['choices'] )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['layout']['instructions'] );

        $disable_pagination_field = ( new Field\TrueFalse( $strings['disable_pagination']['title'] ) )
            ->set_key( "{$key}_disable_pagination" )
            ->set_name( 'disable_pagination' )
            ->use_ui()
            ->set_instructions( $strings['disable_pagination']['instructions'] );

        $fields   = $search_fields->get_fields();
        $fields[] = $layout_field;
        $fields[] = $disable_pagination_field;
        array_unshift( $fields, $description_field );

        $tab->add_fields( $fields );

        return $tab;
    }
}

( new PageEventsCalendarGroup() );
