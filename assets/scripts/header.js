/*
 *  Copyright (c) 2021. Geniem Oy
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars
import MicroModal from 'micromodal';
import Common from './common';

/**
 * Export the class reference.
 */
export default class Header {

    /**
     * Toggle search form
     *
     * @param {Object} event Click event object.
     *
     * @return {void}
     */
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
     * Close fly out modal
     *
     * @return {void}
     */
    closeFlyOutMenu() {
        MicroModal.close( 'js-fly-out-nav' );
    }

    /**
     * Open menu callback
     *
     * @return {void}
     */
    onFlyOutMenuOpen() {
        $( '#js-fly-out-nav' ).height( $( 'body' ).height() );
    }

    /**
     * Close notice button callback
     *
     * @return {void}
     */
    onNoticeClose() {
        const $exceptionNotice = $( '.site-header-notice' );

        Common.setCookie( $exceptionNotice.data( 'notice-id' ), '', 30 );

        $exceptionNotice.remove();
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.js-toggle' ).on( 'click', this.toggleSearchForm.bind( this ) );
        $( '.fly-out-nav__close' ).on( 'click', this.closeFlyOutMenu.bind( this ) );
        $( '.site-header-notice__close' ).on( 'click', this.onNoticeClose.bind( this ) );

        MicroModal.init( {
            disableScroll: true,
            onShow: this.onFlyOutMenuOpen.bind( this ),
        } );
    }
}
