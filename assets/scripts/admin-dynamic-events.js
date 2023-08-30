/*
 *  Copyright (c) 2021. Geniem Oy
 */

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
            'area',
            'category',
            'target',
            'tag',
        ];

        let $el = '';

        /**
         * Init
         *
         * @return {void}
         */
        const init = () => {
            const body = $( 'body' );

            if ( ! body.hasClass( 'post-type-dynamic-event-cpt' ) ) {
                return;
            }

            if ( ! body.hasClass( 'post-new-php' ) && ! body.hasClass( 'post-php' ) ) {
                return;
            }

            $el = $( '.acf-fields' );

            $el.on( 'input', 'input', _.debounce( doSearch, 500 ) );
            $el.on( 'change', 'select', _.debounce( doSearch, 500 ) );
            $el.on( 'click', '.acf-tab-button', doSearch );

            doSearch();
        };

        /**
         * Search
         *
         * @param {Object} event Input or change event.
         *
         * @return {void}
         */
        const doSearch = ( event ) => {
            const fields = acf.getFields( {
                parent: $el,
            } );

            const eventField = fields.find( ( f ) => f.get( 'name' ) === 'event' );
            const eventSelectId = eventField.$el.find( 'select:first' ).attr( 'id' );

            if ( typeof event !== 'undefined' && event.type === 'change' ) {
                if ( $( event.currentTarget ).find( 'select' ).attr( 'id' ) === eventSelectId ) {
                    return;
                }
            }

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
                    post_id: $( '#post_ID' ).val(),
                },
                success: ( response ) => {
                    if ( response ) {
                        const select = eventField.$el.find( 'select' );
                        select.find( 'option' ).remove();

                        response.forEach( ( item ) => {
                            select.append(
                                $( '<option /> ' )
                                    .attr( 'value', item._id )
                                    .text( item.select_name )
                                    .prop( 'selected', item.selected )
                            );
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
