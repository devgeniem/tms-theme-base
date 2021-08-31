<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use TMS\Theme\Base\Logger;

/**
 * Class SitemapLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class SitemapLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_sitemap';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Sivukartta',
            $key . self::KEY,
            'sitemap'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        try {
            $this->add_fields(
                $this->filter_layout_fields( [], $this->get_key() )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
