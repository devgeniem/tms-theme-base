/*
 *  Copyright (c) 2023. Hion Digital
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Export the class reference.
 */
export default class FocusOnSearch {

    /**
     * Focus on search input when button is clicked
     */
    FocusOnSearch() {
        const $searchContainer = $( '#js-search-toggle-target' );
        const $searchInput = $searchContainer.find( 'input[type=search]' );

        if ( $( '#js-search-toggle' ).hasClass( 'is-active' ) ) {
            $searchInput.trigger( 'focus' );
        }
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '#js-search-toggle' ).on( 'click', this.FocusOnSearch.bind( this ) );
    }
}
