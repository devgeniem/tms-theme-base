<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

use \TMS\Theme\Base\Comments;
?>

<section class="section has-border has-border-secondary has-border-top-1">
    <div class="columns">
        <div class="column is-10 is-offset-1">
            <div class="is-content-grid">
                <h2>
                    <?php esc_html_e( 'Comments', 'tms-theme-base' ); ?>
                </h2>

                <div class="comments-area">
                    <?php if ( have_comments() ) : ?>
                        <div class="comments__list-container">
                            <?php
                            wp_list_comments(
                                [
                                    'reply_text' => __( 'Reply', 'tms-theme-base' ),
                                    'callback'   => Closure::fromCallable( [ Comments::class, 'comment_callback' ] ),
                                    'style'      => 'div',
                                ]
                            );
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="comments__form-container">
                        <?php
                        comment_form();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
