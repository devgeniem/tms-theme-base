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
            const instance = new Indicate( document.getElementsByTagName( 'table' ), { arrows: true } );
        }
    }
}
