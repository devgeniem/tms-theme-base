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
     * Show the notification if related cookie is not found.
     */
    maybeShowGeneralNotification() {
        const $generalNotice = $( '.site-header-notice' );

        if ( $generalNotice.length > 0 && ! Common.cookieExists( $generalNotice.data( 'notice-id' ) ) ) {
            $generalNotice.addClass( 'is-block' );
        }
    }

    /**
     * Toggle aria-expanded attribute based on .fly-out-nav state.
     *
     * @return {void}
     */
    toggleFlyOutNavAria() {
        const $flyOutNav = $( '.fly-out-nav' );
        const $trigger = $( '.fly-out-nav__trigger' );
        const ariaExpandedState = $flyOutNav.hasClass( 'is-open' );
        $trigger.attr( 'aria-expanded', ariaExpandedState );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.site-header-notice__close' ).on( 'click', this.onNoticeClose.bind( this ) );
        $( '.fly-out-nav__close' ).on( 'click', this.closeFlyOutMenu.bind( this ) );
        $( '.fly-out-nav .js-scroll-children' ).on( 'click', this.closeFlyOutMenu.bind( this ) );

        // Prevent closing the menu when clicking inside the fly-out nav.
        $( '.fly-out-nav__inner' ).on( 'click', ( event ) => {
            event.stopPropagation();
        } );

        MicroModal.init( {
            disableScroll: true,
            onShow: this.onFlyOutMenuOpen.bind( this ),
        } );

        this.maybeShowGeneralNotification();

        // Add an event listener to monitor changes to the .fly-out-nav class
        const flyOutNav = document.getElementById( 'js-fly-out-nav' );
        const observer = new MutationObserver( () => {
            this.toggleFlyOutNavAria();
        } );

        observer.observe( flyOutNav, { attributes: true, attributeFilter: [ 'class' ] } );
    }
}
