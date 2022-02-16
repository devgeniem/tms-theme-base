/**
 * Patch for Gravity Forms' block. Ensures that multifile upload works with block also.
 * Hopefully temporary.
 */

export default class GravityFormsPatch {

    gfBlockPatch() {

        jQuery( 'section:not(.gravityform) .gform_wrapper form' ).each( function() {
            const formID = parseInt( this.id.split( '_' )[ 1 ] );
            jQuery( document ).trigger( 'gform_post_render', [ formID, 1 ] );
        } );

    }

    docReady() {
        this.gfBlockPatch();
    }

}
