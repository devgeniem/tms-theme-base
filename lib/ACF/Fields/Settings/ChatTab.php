<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields\Settings;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use Geniem\ACF\Field\Tab;
use Geniem\ACF\Field\Repeater;
use Geniem\ACF\Field\PostObject;
use Geniem\ACF\Field\TrueFalse;
use TMS\Theme\Base\PostType;
use TMS\Theme\Base\Logger;

/**
 * Class ChatTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class ChatTab extends Tab {

    /**
     * Where should the tab switcher be located
     *
     * @var string
     */
    protected $placement = 'left';

    /**
     * Tab strings.
     *
     * @var array
     */
    protected $strings = [
        'tab'         => 'Chat',
        'chat_script' => [
            'title'        => 'Chatin scripti',
            'instructions' => '',
        ],
        'pages'   => [
            'title'        => 'Valitse sivut, joilla chat näytetään',
            'instructions' => '',
        ],
        'page'   => [
            'title'        => 'Sivu',
            'instructions' => '',
        ],
        'show_on_child'   => [
            'title'        => 'Näytä tämän alasivuilla',
            'instructions' => '',
        ],
    ];

    /**
     * The constructor for tab.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) { // phpcs:ignore
        $label = $this->strings['tab'];

        parent::__construct( $label );

        $this->sub_fields( $key );
    }

    /**
     * Register sub fields.
     *
     * @param string $key Field tab key.
     */
    public function sub_fields( $key ) {
        $strings = $this->strings;

        try {
            if ( user_can( get_current_user_id(), 'unfiltered_html' ) ) {
                $chat_script_field = ( new Field\Textarea( $strings['chat_script']['title'] ) )
                    ->set_key( "${key}_chat_script" )
                    ->set_name( 'chat_script' )
                    ->set_instructions( $strings['chat_script']['instructions'] );
      
                $this->add_field( $chat_script_field );
            }

            $pages_field = ( new Field\Repeater( $strings['pages']['title'] ) )
                ->set_key( "${key}_pages" )
                ->set_name( 'pages' )
                ->set_instructions( $strings['pages']['instructions'] );

            $this->add_field( $pages_field );

            $page_field = ( new Field\PostObject( $strings['page']['title'] ) )
                ->set_key( "${key}_page" )
                ->set_name( 'page' )
                ->set_post_types( [ PostType\Page::SLUG ] )
                ->set_instructions( $strings['page']['instructions'] );

            $pages_field->add_field( $page_field );

            $show_on_child_field = ( new Field\TrueFalse( $strings['show_on_child']['title'] ) )
                ->set_key( "${key}_show_on_child" )
                ->set_name( 'show_on_child' )
                ->use_ui()
                ->set_instructions( $strings['show_on_child']['instructions'] );

            $pages_field->add_field( $show_on_child_field );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
