<?php
/**
 * Define the generic Page class.
 */

use TMS\Theme\Base\Traits\Components;

/**
 * The Page class.
 */
class Page extends BaseModel {

    use Components;

    /**
     * Return page title.
     *
     * @return string
     */
    public function title() : string {
        return get_the_title();
    }

    /**
     * Return featured image ID.
     *
     * @return false|int
     */
    public function featured_image_id() {
        return has_post_thumbnail() ? get_post_thumbnail_id() : false;
    }
}
