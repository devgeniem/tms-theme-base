<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use TMS\Theme\Base\ACF\Field\TextEditor;
use TMS\Theme\Base\Logger;

/**
 * Class AccordionWysiwygLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class AccordionWysiwygLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_accordion_wysiwyg';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Teksti',
            $key . self::KEY,
            'accordion_wysiwyg'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $strings = [
            'text' => [
                'label'        => 'Teksti',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        try {
            $text_field = ( new TextEditor( $strings['text']['label'] ) )
                ->set_key( "${key}_rows" )
                ->set_name( 'text' )
                ->set_instructions( $strings['text']['instructions'] )
                ->set_height( 200 );

            $fields = [ $text_field ];
            $this->add_fields(
                $this->filter_layout_fields( $fields, $this->get_key(), self::KEY )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
