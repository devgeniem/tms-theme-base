<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use TMS\Theme\Base\ACF\Fields\ContactsFields;
use TMS\Theme\Base\ACF\Fields\EventsFields;
use TMS\Theme\Base\Logger;

/**
 * Class PageContactsGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class PageContactsGroup {

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
            $group_title = _x( 'Yhteystietojen asetukset', 'theme ACF', 'tms-theme-base' );

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_page_contacts' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'page_template', '==', \PageContacts::TEMPLATE );

            $field_group
                ->add_rule_group( $rule_group )
                ->set_menu_order( - 1 )
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
                        $this->get_contacts_fields( $field_group->get_key() ),
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
     * Get contacts fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_contacts_fields( string $key ) : Field\Tab {
        $strings = [
            'tab' => 'Yhteystiedot',
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $fields = new ContactsFields( 'Yhteystiedot', $key );
        $fields->remove_field( 'title' );
        $fields->remove_field( 'description' );
        $fields->remove_field( 'contacts' );

        $tab->add_fields( $fields->get_fields() );

        return $tab;
    }
}

( new PageContactsGroup() );
