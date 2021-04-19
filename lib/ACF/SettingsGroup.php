<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use \Geniem\ACF\Exception;
use \Geniem\ACF\Field;
use \Geniem\ACF\Group;
use \Geniem\ACF\RuleGroup;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class SettingsGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class SettingsGroup {

    /**
     * SettingsGroup constructor.
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
            $group_title = _x( 'Site settings', 'theme ACF', 'tms-theme-base' );

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_site_settings' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'post_type', '==', PostType\Settings::SLUG );

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

            $fields = [
                $this->get_header_fields( $field_group->get_key() ),
            ];

            $field_group
                ->set_fields( [
                    apply_filters(
                        'tms_theme_base_acf_' . $field_group->get_key(),
                        $fields,
                        $field_group->get_key()
                    ),
                ] )
                ->register();
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
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_header_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'  => _x( 'Header', 'theme ACF', 'tms-theme-base' ),
            'logo' => [
                'title'        => _x( 'Site logo', 'theme ACF', 'tms-theme-base' ),
                'instructions' => _x( 'Add site logo here.', 'theme ACF', 'tms-theme-base' ),
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $logo = ( new Field\Image( $strings['logo']['title'] ) )
            ->set_key( "${key}_logo" )
            ->set_name( 'logo' )
            ->set_instructions( $strings['logo']['instructions'] );

        $tab->add_field( $logo );

        return $tab;
    }
}
