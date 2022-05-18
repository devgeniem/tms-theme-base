/*
 *  Copyright (c) 2021. Geniem Oy
 */

// Use jQuery as $ within this file scope.

const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Class MapLayout
 */
export default class Toggle {

    /**
     * Toggle target element
     *
     * @param {Object} event Click event object.
     *
     * @return {void}
     */
    toggle( event ) {
        const toggleTrigger = $( event.currentTarget );
        const toggleTarget = $( '#' + toggleTrigger.attr( 'aria-controls' ) );
        const ariaExpandedState = toggleTrigger.attr( 'aria-expanded' ) === 'false';
        const duration = typeof toggleTrigger.data( 'duration' ) !== undefined ? toggleTrigger.data( 'duration' ) : 400;

        if ( toggleTarget.hasClass( 'is-hidden' ) ) {
            toggleTarget.css( 'display', 'none' );
            toggleTarget.removeClass( 'is-hidden' );
        }

        toggleTrigger.attr( 'aria-expanded', ariaExpandedState );
        toggleTrigger.toggleClass( 'is-active' );

        toggleTarget.slideToggle( duration );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.js-toggle' ).on( 'click', this.toggle.bind( this ) );
    }
}
