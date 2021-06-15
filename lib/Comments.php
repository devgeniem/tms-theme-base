<?php
/**
 * In this file we define our custom additions to the DustPress Comments plugin's settings.
 */

namespace TMS\Theme\Base;

/**
 * This class handles the DustPress Comments modifications.
 */
class Comments implements Interfaces\Controller {

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \add_filter( 'comment_reply_link', [ $this, 'amend_reply_link_class' ], 10, 4 );
        \add_filter( 'comment_form_fields', [ $this, 'reorder_form_fields' ], 10, 2 );
        \add_filter( 'dustpress/comments/tms_comments/get_form_args', [ $this, 'get_form_args' ], 10, 1 );
        \add_filter( 'dustpress/comments/tms_comments/get_comments_args', [ $this, 'get_comments_args' ], 10, 1 );
    }

    /**
     * Customize reply link.
     *
     * @param string     $link    The HTML markup for the comment reply link.
     * @param array      $args    An array of arguments overriding the defaults.
     * @param WP_Comment $comment The object of the comment being replied.
     * @param WP_Post    $post    The WP_Post object.
     *
     * @return mixed
     */
    public function amend_reply_link_class( $link, $args, $comment, $post ) { // phpcs:ignore
        return str_replace( 'comment-reply-link', 'comment-reply-link button button-primary', $link );
    }

    /**
     * Customize reply link.
     *
     * @param string $submit_button The HTML markup for the submit button.
     * @param array  $args          An array of arguments overriding the defaults.
     *
     * @return mixed
     */
    public function customize_submit_button( $submit_button, $args ) { // phpcs:ignore
        return $submit_button;
    }

    /**
     * Reorder comment form fields.
     *
     * @param array $fields Form fields.
     *
     * @return array
     */
    public function reorder_form_fields( $fields ) {
        $comment_field = $fields['comment'];
        unset( $fields['comment'] );
        $fields['comment'] = $comment_field;

        return $fields;
    }

    /**
     * Customize comment args.
     *
     * @param array $args Comment args.
     *
     * @return array
     */
    public function get_comments_args( $args ) {
        $args['reply'] = true;

        return $args;
    }

    /**
     * Customize comment form args.
     *
     * @param array $args Form args.
     *
     * @return array
     */
    public function get_form_args( $args ) {
        $args['remove_input']  = [ 'url', 'cookies' ];
        $args['class_submit']  = 'button button-primary';

        return $args;
    }
}
