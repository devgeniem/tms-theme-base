<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field\Flexible\Layout;
use TMS\Theme\Base\ACF\Field\TextEditor;
use TMS\Theme\Base\Logger;

/**
 * Class AccordionWysiwygLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class AccordionWysiwygLayout extends Layout {

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
                ->set_instructions( $strings['text']['instructions'] );

            $this->add_fields(
                apply_filters(
                    'tms/acf/layout/' . $this->get_key() . '/fields',
                    [ $text_field ]
                )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
