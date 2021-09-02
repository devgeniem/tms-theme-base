<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use TMS\Theme\Base\Interfaces\Formatter;

/**
 * Class FormatterController
 *
 * @package TMS\Theme\Base
 */
class FormatterController implements Interfaces\Controller {

    /**
     * The post type class instances
     *
     * @var Formatter[]
     */
    private array $classes = [];

    /**
     * Get a single class instance from Theme Controller
     *
     * @param string|null $class Class name to get.
     *
     * @return Formatter|null
     */
    public function get_class( ?string $class ) : ?Interfaces\Formatter {
        return $this->classes[ $class ] ?? null;
    }

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_formatters' ] )
        );
    }

    /**
     * Get namespace for formatter instances
     *
     * @return string
     */
    protected function get_namespace() : string {
        return __NAMESPACE__;
    }

    /**
     * Get formatter files
     *
     * @return array
     */
    protected function get_formatter_files() : array {
        return array_diff( scandir( __DIR__ . '/Formatters' ), [ '.', '..' ] );
    }

    /**
     * This registers all custom post types.
     *
     * @return void
     */
    private function register_formatters() : void {
        $instances = array_map( function ( $field_class ) {
            $field_class = basename( $field_class, '.' . pathinfo( $field_class )['extension'] );
            $class_name  = $this->get_namespace() . '\Formatters\\' . $field_class;

            if ( ! \class_exists( $class_name ) ) {
                return null;
            }

            return new $class_name();
        }, $this->get_formatter_files() );

        foreach ( $instances as $instance ) {
            if ( $instance instanceof Interfaces\Formatter ) {
                if ( apply_filters( 'tms/acf/formatter/' . $instance::NAME . '/disable', false ) ) {
                    continue;
                }

                $instance->hooks();

                $this->classes[ $instance::NAME ] = $instance;
            }
        }
    }
}
