/**
 * MapLayout JS controller.
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Class MapLayout
 */
export default class MapLayout {

    /**
     * Toggles a dropdown content visibility.
     *
     * @param {Object} event The click event object.
     *
     * @return {void}
     */
    showMap( event ) {
        const target = $( event.target );
        const mapContainer = $( target.data( 'target' ) );

        const iframe = $( '<iframe />' )
            .prop( 'src', mapContainer.data( 'url' ) );

        mapContainer
            .append( iframe )
            .removeClass( 'is-hidden' );

        target.closest( '.map__placeholder-wrapper' ).hide();
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.js-map-layout-toggle' ).on( 'click', this.showMap.bind( this ) );
    }
}
