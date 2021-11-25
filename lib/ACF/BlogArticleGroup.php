<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
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
                        $this->get_authors_tab( $field_group->get_key() ),
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

    /**
     * Get authors tab
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_authors_tab( string $key ) : Field\Tab {
        $strings = [
            'tab'     => 'Kirjoittajat',
            'authors' => [
                'title'        => _x( 'Kirjoittajat', 'theme ACF', 'tms-theme-base' ),
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $authors_field = ( new Field\PostObject( $strings['authors']['title'] ) )
            ->set_key( "${key}_authors" )
            ->set_name( 'authors' )
            ->set_post_types( [ PostType\BlogAuthor::SLUG ] )
            ->allow_multiple()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['authors']['instructions'] )
            ->redipress_queryable_filter( function ( $post_object_array ) {

                if ( ! empty( $post_object_array ) && is_object( $post_object_array[0] ) ) {

                    $ids = array_map( function ( $post_object ) {
                        return $post_object->ID;
                    }, $post_object_array );

                    return $ids;
                }
                return [];
            })
            ->redipress_set_field_type( 'Tag' )
            ->redipress_include_search( function( $ids ) {

                $names_string = '';

                if ( ! empty( $ids ) ) {

                    foreach ( $ids as $id ) {
                        $title = \get_the_title( $id );

                        if ( ! empty( $title ) ) {
                            $names_string = $names_string . $title . ' ';
                        }
                    }
                }

                return $names_string;
            });

        $tab->add_fields( [
            $authors_field,
        ] );
        return $tab;
    }
}

( new BlogArticleGroup() );

