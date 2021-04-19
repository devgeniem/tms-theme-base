<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field\Flexible\Layout;
use TMS\Theme\Base\ACF\Fields\ImageBannerFields;
use TMS\Theme\Base\Logger;

/**
 * Class ImageBannerLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class ImageBannerLayout extends Layout {
    
    /**
     * Layout key
     */
    const KEY = '_image_banner';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Kuvabanneri',
            $key . self::KEY,
            'image_banner'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $fields = new ImageBannerFields(
            $this->get_label(),
            $this->get_key(),
            $this->get_name()
        );

        try {
            $this->add_fields( $fields->get_fields() );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
