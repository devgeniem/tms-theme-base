<?php

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class EventsSearchGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class PageEventsSearchGroup {

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
            $group_title = _x( 'Asetukset', 'theme ACF', 'tms-theme-base' );

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_page_events_search' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'page_template', '==', \PageEventsSearch::TEMPLATE );

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
            'tab'     => 'Tapahtumat',
            'keyword' => [
                'label'        => 'Avainsana',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $keyword_field = ( new Field\Select( $strings['keyword']['label'] ) )
            ->set_key( "${key}_keyword" )
            ->set_name( 'keyword' )
            ->use_ui()
            ->use_ajax()
            ->allow_null()
            ->allow_multiple()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['keyword']['instructions'] );

        $tab->add_field( $keyword_field );

        return $tab;
    }
}

( new PageEventsSearchGroup() );
