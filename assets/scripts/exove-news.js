/*
 *  Copyright (c) 2021. Geniem Oy
 */

import 'styles/exove.scss';

/**
 * Exove news JS controller.
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Class ExoveNews
 */
class ExoveNews {

    /**
     * Cache dom elements for use in the class's methods
     *
     * @return {void}
     */
    cache() {
        this.exoveContent = $( '.topical-content' );
    }

    /**
     * Modify news elements.
     *
     * @return {void}
     */
    modifyNewsElements() {
        if ( ! this.exoveContent.length ) {
            return;
        }

        this.exoveContent.find( '.process-accordion__heading, .accordion__heading' ).each( function() {
            $( this ).addClass( 'js-toggle' );
        } );

        this.exoveContent.find( 'use' ).each( function() {
            const xlinkHref = $( this ).attr( 'xlink:href' );
            $( this ).attr( 'xlink:href', `${exoveData.urlPrefix}${xlinkHref}` );
        } );
    }

    /**
     * Constructor
     *
     * @return {void}
     */
    constructor() {
        document.addEventListener( 'DOMContentLoaded', ( e ) => {
            this.cache();
            this.modifyNewsElements();
        } );
    }
}

new ExoveNews();
