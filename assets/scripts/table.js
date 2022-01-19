/**
 * Table JS controller.
 */

import Indicate from 'indicate';

/**
 * Class MapLayout
 */
export default class Table {

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        if ( document.getElementsByTagName( 'table' ).length > 0 ) {

            const allTables = document.getElementsByTagName( 'table' );
            const indicate = new Indicate( allTables, { arrows: true } );

            //make tables tabbable for scrolling with keyboard
            for ( const table of indicate.targetElements ) {
                table.setAttribute( 'tabindex', '0' );
            }

        }

    }
}
