<?php
/**
 * Define the single post class.
 */

use TMS\Theme\Base\PostType\BlogArticle;

/**
 * The Single class.
 */
class SingleBlogArticle extends Single {

    /**
     * Post type
     */
    const POST_TYPE = BlogArticle::SLUG;

}
