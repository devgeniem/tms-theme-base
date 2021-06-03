<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Field;

/**
 * Class TextEditor
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class TextEditor extends \Geniem\ACF\Field\ExtendedWysiwyg {
    /**
     * What toolbar should be shown. We show our custom registered.
     *
     * @see \TMS\Theme\Base\Admin::modify_tinymce_toolbars
     * @var string
     */
    protected $toolbar = 'tms';
    /**
     * What wysiwyg tabs should be shown
     *
     * @var string
     */
    protected $tabs = 'visual';
    /**
     * Should media upload be allowed
     *
     * @var boolean
     */
    protected $media_upload = false;
    /**
     * The height of the element
     *
     * @var integer
     */
    protected $height = 100;
}
