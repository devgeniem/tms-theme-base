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
        const ariaExpandedState = toggleTrigger.attr( 'aria-expanded' ) === 'false';
        const duration = typeof toggleTrigger.data( 'duration' ) !== undefined ? toggleTrigger.data( 'duration' ) : 400;

        let toggleTarget = $( '#' + toggleTrigger.attr( 'aria-controls' ) );

        if ( ! toggleTarget.length && toggleTrigger.hasClass( 'accordion-heading' ) ) {
            toggleTarget = toggleTrigger.closest( '.accordion__item' ).find( '.accordion__content' );
        }

        console.log(toggleTarget);

        if ( toggleTarget.hasClass( 'is-hidden' ) ) {
            toggleTarget.css( 'display', 'none' );
            toggleTarget.removeClass( 'is-hidden' );
        }

        toggleTrigger.attr( 'aria-expanded', ariaExpandedState );
        toggleTrigger.toggleClass( 'is-active' );

        if ( toggleTrigger.hasClass( 'process-accordion__heading' ) || toggleTrigger.hasClass( 'accordion-heading' ) ) {
            toggleTarget.toggleClass( 'active' );
            toggleTarget.css( 'display', toggleTarget.hasClass( 'active' ) ? 'block' : 'none' );
            toggleTarget.attr( 'aria-hidden', toggleTarget.hasClass( 'active' ) ? 'false' : 'true' );
            return;
        }

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
