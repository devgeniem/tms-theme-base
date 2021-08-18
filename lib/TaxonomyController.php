<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use TMS\Theme\Base\Interfaces\Taxonomy;

/**
 * Class TaxonomyController
 *
 * @package TMS\Theme\Base
 */
class TaxonomyController implements Interfaces\Controller {

    /**
     * The taxonomy class instances
     *
     * @var Taxonomy[]
     */
    private $classes = [];

    /**
     * Get a single class instance from Theme Controller
     *
     * @param string|null $class Class name to get.
     *
     * @return Taxonomy|null
     */
    public function get_class( ?string $class ) : ?Interfaces\Taxonomy {
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
            \Closure::fromCallable( [ $this, 'register_taxonomies' ] )
        );
    }

    /**
     * This registers all custom taxonomies.
     *
     * @return void
     */
    protected function register_taxonomies() {
        $instances = array_map( function ( $field_class ) {
            $field_class = basename( $field_class, '.' . pathinfo( $field_class )['extension'] );
            $class_name  = $this->get_namespace() . '\Taxonomy\\' . $field_class;

            if ( ! \class_exists( $class_name ) ) {
                return null;
            }

            return new $class_name();
        }, $this->get_files() );

        foreach ( $instances as $instance ) {
            if ( $instance instanceof Interfaces\Taxonomy ) {
                $instance->hooks();

                $this->classes[ $instance::SLUG ] = $instance;
            }
        }
    }

    /**
     * Get namespace for taxonomy instances
     *
     * @return string
     */
    protected function get_namespace() : string {
        return __NAMESPACE__;
    }

    /**
     * Get custom post type files
     *
     * @return array
     */
    protected function get_files() : array {
        return array_diff( scandir( __DIR__ . '/Taxonomy' ), [ '.', '..' ] );
    }
}
