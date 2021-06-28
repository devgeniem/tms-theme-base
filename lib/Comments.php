<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * This class handles the Comments section modifications.
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
        \add_filter( 'comment_form_fields', [ $this, 'customize_comment_form_fields' ], 10, 2 );
        \add_filter( 'comment_form_submit_button', [ $this, 'override_comment_form_submit' ], 10, 0 );
    }

    /**
     * Customize reply link.
     *
     * @param string      $link    The HTML markup for the comment reply link.
     * @param array       $args    An array of arguments overriding the defaults.
     * @param \WP_Comment $comment The object of the comment being replied.
     * @param \WP_Post    $post    The WP_Post object.
     *
     * @return string
     */
    public function amend_reply_link_class( // phpcs:ignore
        string $link,
        array $args,
        \WP_Comment $comment,
        \WP_Post $post
    ) : string {
        return str_replace( 'comment-reply-link', 'comment-reply-link button button-primary', $link );
    }

    /**
     * Comment form submit button.
     *
     * @return string
     */
    public function override_comment_form_submit() : string {
        return sprintf(
            '<button name="submit" type="submit" id="submit" class="button button--icon is-primary" >%s %s</button>',
            __( 'Send Comment', 'tms-theme-base' ),
            '<svg class="icon icon--chevron-right icon--large is-primary-invert">
                <use xlink:href="#icon-chevron-right"></use>
            </svg>'
        );
    }

    /**
     * Customize comment form fields.
     *
     * @param array $fields Form fields.
     *
     * @return array
     */
    public function customize_comment_form_fields( array $fields ) : array {
        unset( $fields['url'] );
        unset( $fields['cookies'] );

        $comment_field = $fields['comment'];
        unset( $fields['comment'] );
        $fields['comment'] = $comment_field;

        return $fields;
    }

    /**
     * Custom wp_list_comments callback.
     *
     * @param \WP_Comment $comment Current comment object.
     * @param array       $args    Callback args.
     * @param int         $depth   Comment depth.
     *
     * @return void
     */
    public static function comment_callback( \WP_Comment $comment, array $args, int $depth ) { // phpcs:ignore
        ?>
    <div id="comment-<?php comment_ID(); ?>" <?php comment_class( $comment ? 'parent' : '', $comment ); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment__body">
            <?php if ( '0' === $comment->comment_approved ) : ?>
                <p class="comment-awaiting-moderation has-text-weight-bold has-text-primary">
                    (<?php esc_html_e( 'Your comment is awaiting moderation', 'tms-theme-base' ); ?>)
                </p>
            <?php endif; ?>

            <div class="comment__content mb-6 has-word-break-break-all keep-vertical-spacing">
                <?php comment_text(); ?>
            </div>

            <div class="comment__footer is-flex-tablet is-justify-content-space-between">
                <div class="comment__info mr-2">
                    <?php
                    echo sprintf(
                        '<a href="%s" class="%s">%s</a>',
                        esc_url( get_comment_link( $comment ) ),
                        'h5 comment__heading has-text-black',
                        esc_html( get_comment_author_link( $comment ) )
                    );
                    ?>

                    <p class="comment__date mt-2 mb-0">
                        <time datetime="<?php get_comment_time( 'c' ); ?>">
                            <?php
                            echo esc_html(
                                sprintf( '%s - %s', get_comment_date( '', $comment ), get_comment_time() )
                            );
                            ?>
                        </time>
                    </p>

                    <?php
                    edit_comment_link(
                        __( 'Edit', 'tms-theme-base' ),
                        ' <span class="edit-link">', '</span>'
                    );
                    ?>
                </div>

                <?php
                if ( '1' === $comment->comment_approved ) {
                    comment_reply_link(
                        [
                            'depth'     => $depth,
                            'max_depth' => get_option( 'thread_comments_depth' ),
                            'before'    => '<div class="comment__reply is-flex is-align-items-center mt-4 mt-0-tablet">',
                            'after'     => '</div>',
                        ]
                    );
                }
                ?>
            </div>
        </article>
        <?php
    }
}
