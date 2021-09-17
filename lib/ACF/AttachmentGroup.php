<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class AttachmentGroup
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class AttachmentGroup {
    /**
     * UI Strings
     *
     * @var array
     */
    private array $strings;

    /**
     * Attachment constructor.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_fields' ] );

        $this->strings = [
            'group'  => [
                'title' => 'Lisäkentät',
            ],
            'author' => [
                'title' => 'Kuvaajan nimi',
                'help'  => 'Muista kuvaoikeudet!',
            ],
            'artist' => [
                'title' => 'Taiteilijan nimi',
                'help'  => 'Käytetään teoksen otsikon yhteydessä useassa komponentissa ja lohkossa',
            ],
        ];
    }

    /**
     * Register fields
     */
    public function register_fields() : void {
        $field_group = ( new Group( $this->strings['group']['title'] ) )
            ->set_key( 'fg_attachment_fields' );

        try {
            $rule_group = ( new RuleGroup() )
                ->add_rule( PostType\Attachment::SLUG, '==', 'all' );

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'normal' );

            $field_group->add_fields(
                apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_author_field( $field_group->get_key() ),
                        $this->get_artist_field( $field_group->get_key() ),
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
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * Author name.
     *
     * @param string $key Group Key.
     *
     * @return \Geniem\ACF\Field\Text
     * @throws \Geniem\ACF\Exception Throw error if mandatory property (field label) is not set.
     */
    private function get_author_field( string $key = '' ) : Field\Text {
        return ( new Field\Text( $this->strings['author']['title'] ) )
            ->set_key( $key . '_author_name' )
            ->set_name( 'author_name' )
            ->set_instructions( $this->strings['author']['help'] )
            ->set_default_value( '' );
    }

    /**
     * Artist name.
     *
     * @param string $key Group Key.
     *
     * @return \Geniem\ACF\Field\Text
     * @throws \Geniem\ACF\Exception Throw error if mandatory property (field label) is not set.
     */
    private function get_artist_field( string $key = '' ) : Field\Text {
        return ( new Field\Text( $this->strings['artist']['title'] ) )
            ->set_key( $key . '_artist_name' )
            ->set_name( 'artist_name' )
            ->set_instructions( $this->strings['artist']['help'] )
            ->set_default_value( '' );
    }
}

( new AttachmentGroup() );
