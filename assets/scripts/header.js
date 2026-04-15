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
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.site-header-notice__close' ).on( 'click', this.onNoticeClose.bind( this ) );
        $( '.fly-out-nav__close' ).on( 'click', this.closeFlyOutMenu.bind( this ) );
        $( '.fly-out-nav .js-scroll-children' ).on( 'click', this.closeFlyOutMenu.bind( this ) );

        MicroModal.init( {
            disableScroll: true,
            onShow: this.onFlyOutMenuOpen.bind( this ),
        } );

        this.maybeShowGeneralNotification();
    }
}
