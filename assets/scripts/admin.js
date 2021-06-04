/**
 * Require admin style file here for concatenation.
 */

import '../styles/admin.scss';

/*global ajaxurl */

( function( $, acf, _ ) {

    /**
     * EventSearch
     *
     * @return {void}
     */
    function EventSearch() {

        /**
         * Filter fields
         *
         * @type {Array}
         */
        const filterFields = [
            'text',
            'location',
            'keyword',
            'publisher',
        ];

        let $el = '';

        /**
         * Init
         *
         * @return {void}
         */
        const init = () => {
            const body = $( 'body' );

            if ( ! body.hasClass( 'post-type-dynamic-event-cpt' ) || ( ! body.hasClass( 'post-new-php' ) && ! body.hasClass( 'post-php' ) ) ) {
                return;
            }

            $el = $( '.acf-fields' );

            $el.on( 'input', 'input', _.debounce( doSearch, 500 ) );
            $el.on( 'change', 'select', _.debounce( doSearch, 500 ) );
        };

        /**
         * Search
         *
         * @return {void}
         */
        const doSearch = () => {
            const fields = acf.getFields( {
                parent: $el,
            } );
            const eventField = fields.find( ( f ) => f.get( 'name' ) === 'event' );

            const paramFields = fields.filter( ( field ) => {
                if ( filterFields.includes( field.get( 'name' ) ) && ! field.hiddenByTab ) {
                    return field.val() !== '';
                }

                return false;
            } );

            if ( paramFields.length === 0 || eventField.length === 0 ) {
                return;
            }

            acf.showLoading( $el );

            $.ajax( {
                type: 'get',
                url: ajaxurl,
                data: {
                    action: 'event_search',
                    params: createQueryObject( paramFields ),
                },
                success: ( response ) => {
                    if ( response ) {
                        const select = eventField.$el.find( 'select' );

                        response.forEach( ( item ) => {
                            select.append( $( '<option /> ' ).attr( 'value', item.id ).text( item.name.fi ) );
                        } );
                    }
                },
                complete: () => {
                    acf.hideLoading( $el );
                },
            } );
        };

        /**
         * Create query object
         *
         * @param {Array} fields ACF fields.
         *
         * @return {Object} Query
         */
        const createQueryObject = ( fields ) => {
            const query = {};

            fields.filter( ( f ) => f.val() !== '' )
                .forEach( ( field ) => {
                    query[ field.get( 'name' ) ] = field.val();
                } );

            return query;
        };

        init();
    }

    $( document ).ready( () => {
        new EventSearch();
    } );
}( jQuery, window.acf, window._ ) );
