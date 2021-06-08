/*
 *  Copyright (c) 2021. Geniem Oy
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Export the class reference.
 */
export default class Dropdown {
    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.dropdown-trigger > button' ).on( 'click', function() {
            const $btn = $( this );

            const expanded = $btn.attr( 'aria-expanded' );
            $btn.attr( 'aria-expanded', ! expanded );

            const $menu = $btn.parents( '.dropdown' );

            const $dropdown = $menu
                .toggleClass( 'is-active' )
                .first( '.dropdown-menu' );

            $dropdown.attr( 'aria-hidden', ! $dropdown.attr( 'aria-hidden' ) );
        } );
    }
}

