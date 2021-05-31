/**
 *  Copyright (c) 2021. Geniem Oy
 */

const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Class Home
 */
export default class Home {

    events() {
        $( '.js-trigger-form-submit' ).on( 'change', this.triggerFormSubmit.bind( this ) );
    }

    triggerFormSubmit( event ) {
        $( event.target ).closest( 'form' ).submit();
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        this.events();
    }
}
