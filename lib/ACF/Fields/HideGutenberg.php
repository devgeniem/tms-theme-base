<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use \Geniem\ACF\Field;

/**
 * Class HideGutenberg
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class HideGutenberg extends Field\PHP {

    /**
     * The original key prefix of the field.
     *
     * @var string
     */
    protected $key = 'hide_gutenberg';

    /**
     * The original name of the field.
     *
     * @var string
     */
    protected $name = 'hide_gutenberg';

    /**
     * The original label of the field.
     *
     * @var string
     */
    protected $label = 'Hide Gutenberg';

    /**
     * The constructor for this field
     *
     * @throws \Geniem\ACF\Exception Throw error if given parameter is not valid.
     *
     * @param string|null $label Label.
     * @param string|null $key   Field key.
     * @param string|null $name  Field name.
     */
    public function __construct( $label = null, $key = null, $name = null ) {

        // Construct the actual PHP field with given parameters or class defaults.
        $label = $label ?? $this->label;
        $key   = $key ?? $this->key;
        $name  = $name ?? $this->name;

        // Call the parent's constructor.
        parent::__construct( $label, $key, $name );

        // Run the code that hides Gutenberg.
        $this->run( [ $this, 'hide_gutenberg' ] );
    }

    /**
     * Echo inline styles to hide Gutenberg editor functionalities.
     *
     * @return void
     */
    public function hide_gutenberg() {
        // Hide this field itself.
        echo '<style>.acf-field-frontpage-hide-gutenberg { display: none; }</style>';

        // Hide block editor toolbar.
        echo '<style>.edit-post-header__toolbar { visibility: hidden; }</style>';

        // Hide block editor.
        echo '<style>.block-editor-block-list__layout { display: none; }</style>';

        // Override editor specific styles for cleaner look.
        echo '<style>.edit-post-layout__content .edit-post-visual-editor { flex: unset; flex-basis: unset }</style>';
    }
}
