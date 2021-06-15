/**
 * Comments JS controller.
 */

/**
 * Class Comments
 */
export default class Comments {

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        window.DustPress.Comments.addListener( ( state, container ) => {
            console.log( state );
            console.log( container );
        } );
    }
}
