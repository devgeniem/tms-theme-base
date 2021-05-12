/*
 *  Copyright (c) 2021. Geniem Oy
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Export the class reference.
 */
export default class Header {

    toggleSearchForm( event ) {
        const toggle = $( event.currentTarget );
        const toggleTarget = $( '#' + toggle.attr( 'aria-controls' ) );
        const ariaExpandedState = toggle.attr( 'aria-expanded' ) === 'false';

        if ( toggleTarget.hasClass( 'is-hidden' ) ) {
            toggleTarget.css( 'display', 'none' );
            toggleTarget.removeClass( 'is-hidden' );
        }

        toggle.attr( 'aria-expanded', ariaExpandedState );
        toggle.toggleClass( 'is-active' );

        toggleTarget.slideToggle();
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.js-toggle' ).on( 'click', this.toggleSearchForm.bind( this ) );
    }
}
