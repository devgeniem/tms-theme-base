/**
 * Sitemap JS controller.
 * Copyright (c) 2021. Geniem Oy
 */

import Common from './common';

const $ = jQuery;

/**
 * Class Sitemap
 */
export default class Sitemap {
    /**
     * Sitemap translations from windows.s or defaults.
     *
     * @return {*|{close: string, open: string}} Translations: open and close button texts.
     */
    static translations() {
        return window.s.sitemap || {
            open: 'Open',
            close: 'Close',
        };
    }

    /**
     * Toggles sitemap content visibility.
     *
     * @param {Object} event The click event object.
     *
     * @return {void}
     */
    static toggleVisibleLevels( event ) {
        const target = $( event.target );
        const children = $( target.next( '.children' ) );

        children.toggle();
        Common.toggleAriaHidden( children[ 0 ] );

        target.toggleClass( 'is-active' );

        Sitemap.toggleButtonText( target.find( '.button-text' ), children.is( ':visible' ) );
    }

    /**
     * Toggle Open button text based on state.
     *
     * @param {jQuery|HTMLElement} button Button to change text of.
     * @param {boolean}            open   If true, show text 'Open', else 'Close'.
     *
     * @return {void}
     */
    static toggleButtonText( button, open = true ) {
        button.text( open ? Sitemap.translations().close : Sitemap.translations().open );
    }

    /**
     * Takes each sitemap link and checks for children, if found,
     * and depth is sufficient adds toggling button.
     *
     * @return {void}
     */
    addToggleButtons() {
        const link = $( this );

        if ( link.parent( 'li' ).hasClass( 'page_item_has_children' ) ) {
            // Add toggler button and bind toggling functionality to it.
            const icon = Common.makeIcon( 'chevron-down', 'icon--small' );
            const buttonTextClass = 'button-text is-sr-only has-pointer-events-none';
            let button = `<span class="${ buttonTextClass }">${ Sitemap.translations().open }</span>`;
            button += `<span class="has-pointer-events-none" aria-hidden="true">${ icon }</span>`;

            const toggler = $( Common.makeButton(
                button,
                'ml-3 button is-primary button-toggle sitemap--toggle'
            ) );

            // ID for children ul, to attach aria-controls labels.
            const childrenId = link.attr( 'data-depth-id' ) + '-children';

            toggler.attr( {
                'aria-controls': childrenId, // Button controls
                'aria-live': 'polite', // Button text changes
            } );

            toggler.on( 'click', Sitemap.toggleVisibleLevels.bind( this ) );

            link.after( toggler );

            const children = link.siblings( '.children' ).first();
            children.hide();
            children.attr( {
                'id': childrenId,
                'aria-hidden': true,
            } );

            Sitemap.toggleButtonText( link.siblings( 'button' ).find( '.button-text' ), children.is( ':visible' ) );
        }
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        const depthLimits = $( '.sitemap--wrapper [data-depth-toggle]' );

        depthLimits.each( this.addToggleButtons );
    }
}
