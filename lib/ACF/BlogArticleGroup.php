<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class PostGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class BlogArticleGroup extends PostGroup {

    /**
     * PageGroup constructor.
     */
    public function __construct() {
        add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_fields' ] )
        );
    }

    /**
     * Register fields
     */
    protected function register_fields() : void {
        try {
            $field_group = ( new Group( 'Blogiartikkelin lisätiedot' ) )
                ->set_key( 'fg_blog_article_fields' );

            $blog_article_rule_group = ( new RuleGroup() )
                ->add_rule( 'post_type', '==', PostType\BlogArticle::SLUG );

            $field_group
                ->add_rule_group( $blog_article_rule_group )
                ->set_position( 'normal' );

            $field_group->add_fields(
                apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_credits_tab( $field_group->get_key() ),
                        $this->get_related_posts_tab( $field_group->get_key() ),
                        $this->get_components_tab( $field_group->get_key() ),
                    ]
                )
            );

            $field_group = apply_filters(
                'tms/acf/group/' . $field_group->get_key(),
                $field_group
            );

            $field_group->register();
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTraceAsString() );
        }
    }
}

( new BlogArticleGroup() );
