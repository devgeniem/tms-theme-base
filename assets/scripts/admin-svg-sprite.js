const copySpriteToCanvas = () => {
    const source = document.getElementById( 'tms-icons-sprite' );
    const iframe = document.querySelector( 'iframe.block-editor-iframe__iframe, iframe[name="editor-canvas"]' );

    if ( ! source || ! iframe || ! iframe.contentDocument || ! iframe.contentDocument.body ) {
        return;
    }

    if ( iframe.contentDocument.getElementById( 'tms-icons-sprite' ) ) {
        return;
    }

    iframe.contentDocument.body.insertAdjacentHTML( 'afterbegin', source.outerHTML );
};

document.addEventListener( 'click', copySpriteToCanvas, true );
