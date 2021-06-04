/*
 *  Copyright (c) 2021. Geniem Oy
 */
/**
 * Admin scripts.
 *
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
            if ( ! $( 'body' ).hasClass( 'post-type-dynamic-event-cpt' ) || ! $( 'body' ).hasClass( 'post-new-php' ) ) {
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
            let fields = acf.getFields( {
                parent: $el,
            } );

            fields = fields.filter( ( field ) => filterFields.includes( field.get( 'name' ) ) && ! field.hiddenByTab );

            if ( fields.length === 0 ) {
                return;
            }

            const selectedEvents = fields.find( ( field ) => field.get( 'name' ) === 'selected_events' );

            resultLink.$el.on( 'change', ( e ) => {
                if ( e.target.value.length > 0 ) { // skip when resultLink gets emptied
                    const queryFields = createQueryObject( fields );
                    selectedEvents.data.query = queryFields;
                    selectedEvents.fetch( queryFields );
                }
            } );



            acf.showLoading( $el );

            $.ajax( {
                type: 'get',
                url: ajaxurl,
                data: {
                    action: 'event_search',
                    params: createQueryObject( fields ),
                },
                success: ( response ) => {
                    console.log( response ); // eslint-disable-line
                    if ( response && response.data ) {
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

            console.log( fields ); // eslint-disable-line

            fields.forEach( ( field ) => {
                if ( field.val() !== '' ) {
                    query[ field.get( 'name' ) ] = field.val();
                }
            } );

            return query;
        };

        init();
    }

    $( document ).ready( () => {
        new EventSearch();
    } );
}( jQuery, window.acf, window._ ) );

( function( $, acf, undef ) {

    const Field = acf.Field.extend( {
        type: 'rest_relationship',
        events: {
            'click .choices-list .acf-rel-item': 'onClickAdd',
            'click [data-name="remove_item"]': 'onClickRemove',
        },

        $control() {
            return this.$( '.acf-rest-relationship' );
        },

        $list( list ) {
            return this.$( '.' + list + '-list' );
        },

        $listItems( list ) {
            return this.$list( list ).find( '.acf-rel-item' );
        },

        $listItem( list, id ) {
            return this.$list( list ).find( '.acf-rel-item[data-id="' + id + '"]' );
        },

        getValue() {
            const val = [];
            this.$listItems( 'values' ).each( function() {
                val.push( $( this ).data( 'id' ) );
            } );
            return val.length ? val : false;
        },

        newChoice( props ) {
            return [
                '<li>',
                '<span data-id="' + props.id + '" class="acf-rel-item">' + props.text + '</span>',
                '</li>',
            ].join( '' );
        },

        newValue( props ) {
            const name = this.getInputName();
            const id = props.id;
            const text = props.text || id;

            return [
                '<li>',
                '<input type="hidden" name="' + name + '[' + id + ']" value="' + text + '" />',
                '<span data-id="' + id + '" class="acf-rel-item">' + text,
                '<a href="#" class="acf-icon -minus small dark" data-name="remove_item"></a>',
                '</span>',
                '</li>',
            ].join( '' );
        },

        initialize() {
            // Delay initialization until "interacted with" or "in view".
            const delayed = this.proxy( acf.once( function() {
                // Add sortable.
                this.$list( 'values' ).sortable( {
                    items: 'li',
                    forceHelperSize: true,
                    forcePlaceholderSize: true,
                    scroll: true,
                    update: this.proxy( function() {
                        this.$input().trigger( 'change' );
                    } ),
                } );
                // Avoid browser remembering old scroll position and add event.
                this.$list( 'choices' ).scrollTop( 0 );
                // Fetch choices.
                this.fetch();
            } ) );

            // Bind "interacted with".
            this.$el.one( 'mouseover', delayed );
            this.$el.one( 'focus', 'input', delayed );

            // Bind "in view".
            acf.onceInView( this.$el, delayed );
        },

        onClickAdd( e, $el ) {

            // vars
            const val = this.val();
            const max = parseInt( this.get( 'max' ) );

            // can be added?
            if ( $el.hasClass( 'disabled' ) ) {
                return false;
            }

            // validate
            if ( max > 0 && val && val.length >= max ) {

                // add notice
                this.showNotice( {
                    text: acf.__( 'Maximum values reached ( {max} values )' ).replace( '{max}', max ),
                    type: 'warning',
                } );
                return false;
            }

            // disable
            $el.addClass( 'disabled' );

            // add
            const html = this.newValue( {
                id: $el.data( 'id' ),
                text: $el.html(),
            } );
            this.$list( 'values' ).append( html );

            // trigger change
            this.$input().trigger( 'change' );
        },

        onClickRemove( e, $el ) {

            // Prevent default here because generic handler wont be triggered.
            e.preventDefault();

            // vars
            const $span = $el.parent();
            const $li = $span.parent();
            const id = $span.data( 'id' );

            // remove value
            $li.remove();

            // show choice
            this.$listItem( 'choices', id ).removeClass( 'disabled' );

            // trigger change
            this.$input().trigger( 'change' );
        },

        maybeFetch() {

            // vars
            let timeout = this.get( 'timeout' );

            // abort timeout
            if ( timeout ) {
                clearTimeout( timeout );
            }

            // fetch
            timeout = this.setTimeout( this.fetch, 300 );
            this.set( 'timeout', timeout );
        },

        getAjaxData( passedFields = undef ) {

            // load data based on element attributes
            let ajaxData = this.$control().data() || {};
            for ( const name in ajaxData ) {
                if ( ajaxData.hasOwnProperty( name ) ) {
                    ajaxData[ name ] = this.get( name );
                }
            }

            if ( passedFields !== typeof undefined ) {
                ajaxData.query = ajaxData.query || {};
                for ( const passedFieldsKey in passedFields ) {
                    if ( passedFields.hasOwnProperty( passedFieldsKey ) ) {
                        ajaxData.query[ passedFieldsKey ] = passedFields[ passedFieldsKey ];
                    }
                }
            }

            // extra
            ajaxData.action = 'acf/fields/rest_relationship/query';
            ajaxData.field_key = this.get( 'key' );

            // Filter.
            ajaxData = acf.applyFilters( 'rest_relationship_ajax_data', ajaxData, this );

            // return
            return ajaxData;
        },

        fetch( passedFields = undef ) {
            // abort XHR if this field is already loading AJAX data
            if ( this.get( 'xhr' ) ) {
                this.get( 'xhr' ).abort();
            }

            // add to this.o
            const ajaxData = this.getAjaxData( passedFields );

            // clear html if is new query
            const $choicesList = this.$list( 'choices' );
            if ( parseInt( ajaxData.paged ) === 1 ) {
                $choicesList.html( '' );
            }

            // loading
            const $loading = $( '<li><i class="acf-loading"></i> ' + acf.__( 'Loading' ) + '</li>' );
            $choicesList.append( $loading );
            this.set( 'loading', true );

            // callback
            const onComplete = function() {
                this.set( 'loading', false );
                $loading.remove();
            };

            const onSuccess = function( json ) {
                // no results
                if ( ! json || ! json.results || ! json.results.length ) {

                    // prevent pagination
                    this.set( 'more', false );

                    // add message
                    if ( parseInt( this.get( 'paged' ) ) === 1 ) {
                        this.$list( 'choices' ).append( '<li>' + acf.__( 'No matches found' ) + '</li>' );
                    }

                    // return
                    return;
                }

                // set more (allows pagination scroll)
                this.set( 'more', json.more || false );

                // get new results
                const html = this.walkChoices( json.results );
                const $html = $( html );

                // apply .disabled to left li's
                const val = this.val();
                if ( val && val.length ) {
                    val.map( ( id ) => {
                        return $html.find( '.acf-rel-item[data-id="' + id + '"]' ).addClass( 'disabled' );
                    } );
                }

                // append
                $choicesList.empty();
                $choicesList.append( $html );

                // merge together groups
                let $prevLabel = false;
                let $prevList = false;

                $choicesList.find( '.acf-rel-label' ).each( function() {

                    const $label = $( this );
                    const $list = $label.siblings( 'ul' );

                    // eslint-disable-next-line eqeqeq
                    if ( $prevLabel && $prevLabel.text() == $label.text() ) {
                        $prevList.append( $list.children() );
                        $( this ).parent().remove();
                        return;
                    }

                    // update vars
                    $prevLabel = $label;
                    $prevList = $list;
                } );
            };

            /**
             * get results
             *
             * @type {*|jQuery}
             */
            const results = $.ajax( {
                url: acf.get( 'ajaxurl' ),
                dataType: 'json',
                type: 'post',
                data: acf.prepareForAjax( ajaxData ),
                context: this,
                success: onSuccess,
                complete: onComplete,
            } );

            // set
            this.set( 'xhr', results );
        },

        walkChoices( data ) {

            /**
             * This is our deduplication object.
             *
             * @type {{}}
             */
            const knownElements = {};

            // walker
            const walk = function( walkerData, known = {} ) {

                // vars
                let html = '';

                // is array
                if ( $.isArray( walkerData ) ) {
                    walkerData.map( ( item ) => {
                        html += walk( item );
                        return html;
                    } );

                    // is item
                }
                else if ( $.isPlainObject( walkerData ) ) {

                    // group
                    if ( walkerData.children !== undef ) {

                        // If there's no unique children, no need for the parent either
                        const children = walk( walkerData.children );

                        if ( children.length > 0 ) {
                            html += '<li><span class="acf-rel-label">';
                            html += acf.escHtml( walkerData.text );
                            html += '</span><ul class="acf-bl">';
                            html += children;
                            html += '</ul></li>';
                        }

                        // single
                    }
                    else if ( known[ walkerData.id ] === undef ) {
                        html += '<li><span class="acf-rel-item" data-id="' +
                            acf.escAttr( walkerData.id ) +
                            '">' +
                            acf.escHtml( walkerData.text ) +
                            '</span></li>';

                        known[ walkerData.id ] = walkerData.id;
                    }
                }

                // return
                return html;
            };

            return walk( data, knownElements );
        },

    } );

    acf.registerFieldType( Field );

}( jQuery, window.acf, undefined ) );
