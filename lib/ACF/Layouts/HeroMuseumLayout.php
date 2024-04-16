<?php
namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use TMS\Theme\Base\ACF\Fields\HeroMuseumFields;
use TMS\Theme\Base\Logger;

/**
 * Class HeroMuseumLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class HeroMuseumLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_hero_museum';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Hero - museosivustot',
            $key . self::KEY,
            'hero_museum'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $fields = new HeroMuseumFields(
            $this->get_label(),
            $this->get_key(),
            $this->get_name()
        );

        try {
            $this->add_fields(
                $this->filter_layout_fields( $fields->get_fields(), $this->get_key(), self::KEY )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
