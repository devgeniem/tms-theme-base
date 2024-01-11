/*
 *  Copyright (c) 2023. Hion Digital
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Export the class reference.
 */
export default class SearchFilters {

    /**
     * Check or uncheck checkboxes depending on selections
     *
     * @param {Object} event Change event
     */
    SearchFilters( event ) {
        const $checkBoxContainer = $( '.search-filters' );
        const $checkBoxes = $checkBoxContainer.find( 'input[type=checkbox]' );
        const $clickedCheckbox = event.target;

        if ( $( $clickedCheckbox ).is( ':checked' ) ) {
            // Uncheck "All"-checkbox when others are checked
            if ( $( $clickedCheckbox ).prop( 'id' ) !== 'cpt-all' ) {
                $( '#cpt-all' ).prop( 'checked', false );
            }
            // Uncheck other checkboxes when "All" is selected
            else {
                $checkBoxes.each( function() {
                    if ( $( this ).prop( 'id' ) !== 'cpt-all' ) {
                        $( this ).prop( 'checked', false );
                    }
                } );
            }
        }
    }

    /**
     * Clear all event-search form inputs
     *
     * @param {Object} event Click event
     */
    ClearEventSearch( event ) {
        const $parentContainer = jQuery( event.target ).parents( '.columns' );
        $parentContainer.find( 'input' ).val( '' );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.search-filters input[type=checkbox]' ).on( 'change', this.SearchFilters.bind( this ) );
        $( '.button-clear-inputs' ).on( 'click', this.ClearEventSearch.bind( this ) );
    }
}
