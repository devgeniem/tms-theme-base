<?php
/**
 * Isset helper
 */

namespace TMS\Theme\Base\Helpers;

/**
 * Isset helper
 * Paremeters
 * - key1, key2, key#...: the keys to check.
 * - method: and|or
 *
 * Usage example:
 * {@isset key1=foo key2=foo.bar key3=something method="and"}
 *    All fields are set.
 * {:else}
 *   All fields are not set.
 * {/isset}
 */
class IssetHelper extends \DustPress\Helper {

    /**
     * Initialize the helper
     *
     * @return object
     */
    public function init() {
        $keys = [];
        $key  = 1;

        if ( ! isset( $this->params->key1 ) ) {
            return 'DustPress isset helper error: No keys specified.';
        }

        while ( isset( $this->params->{ 'key' . $key } ) ) {
            $keys[] = $this->params->{ 'key' . $key };
            $key++;
        }

        $clause = false;
        $values = array_filter( $keys, function( $key ) {
            return ! empty( $key );
        });

        if ( isset( $this->params->method ) ) {
            switch ( $this->params->method ) {
                case 'and':
                    if ( count( $values ) === count( $keys ) ) {
                        $clause = true;
                    }
                    break;
                case 'or':
                    if ( count( $values ) > 0 ) {
                        $clause = true;
                    }
                    break;
                default:
                    return 'DustPress isset helper error: Method not known.';
            }
        }
        else {
            if ( count( $values ) > 0 ) {
                $clause = true;
            }
        }

        if ( $clause ) {
            return $this->chunk->render( $this->bodies->block, $this->context );
        }
        elseif ( isset( $this->bodies['else'] ) ) {
            return $this->chunk->render( $this->bodies['else'], $this->context );
        }
        else {
            return $this->chunk;
        }
    }
}
dustpress()->add_helper( 'isset', new IssetHelper() );
