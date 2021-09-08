/*
 *  Copyright (c) 2021. Geniem Oy
 */

/**
 * CopyToClipboard JS controller.
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Class CopyToClipboard
 */
export default class CopyToClipboard {

    /**
     * Copy link href to clipboard
     *
     * @param {Object} event The click event object.
     *
     * @return {void}
     */
    copyLink( event ) {
        event.preventDefault();

        const target = $( event.currentTarget );
        const callbackText = target.data( 'callback-text' );

        navigator.clipboard.writeText( target.attr( 'href' ) );

        if ( callbackText ) {
            target
                .find( '.js-callback-container' )
                .removeClass( 'is-hidden' )
                .text( callbackText );

            setTimeout( () => {
                target
                    .find( '.js-callback-container' )
                    .addClass( 'is-hidden' );
            }, 2000 );
        }
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.js-copy-to-clipboard' ).on( 'click', this.copyLink.bind( this ) );
    }
}
