<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field\Flexible\Layout;
use TMS\Theme\Base\ACF\Fields\ArticlesFields;
use TMS\Theme\Base\Logger;

/**
 * Class ArticlesLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class ArticlesLayout extends Layout {

    /**
     * Layout key
     */
    const KEY = '_articles';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Ajankohtaiset nostot',
            $key . self::KEY,
            'articles'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $fields = new ArticlesFields(
            $this->get_label(),
            $this->get_key(),
            $this->get_name()
        );

        try {
            $layout_fields = apply_filters(
                'tms/acf/layout/' . self::KEY . '/fields',
                $fields->get_fields(),
                $this->get_key()
            );

            $layout_fields = apply_filters(
                'tms/acf/layout/' . $this->get_key() . '/fields',
                $layout_fields,
                $this->get_key()
            );

            $this->add_fields( $layout_fields );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
