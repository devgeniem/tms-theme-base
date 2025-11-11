<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class CommentGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class CommentGroup {

    /**
     * CommentGroup constructor.
     */
    public function __construct() {
        add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_fields' ] )
        );
    }

    /**
     * Register fields.
     */
    protected function register_fields() : void {
        if ( ! current_user_can( 'moderate_comments' ) ) {
            return;
        }

        try {
            $field_group = ( new Group( 'Blogikirjoittaja' ) )
                ->set_key( 'fg_comment_fields' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'comment', '==', 'all' );

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'normal' );

            $field_group->add_fields(
                apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_author_field( $field_group->get_key() ),
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
     * Get author field.
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_author_field( string $key ) : Field\PostObject {
        $strings = [
            'author' => [
                'title'        => _x( 'Blogikirjoittaja', 'theme ACF', 'tms-theme-base' ),
                'instructions' => '',
            ],
        ];

        return ( new Field\PostObject( $strings['author']['title'] ) )
            ->set_key( "{$key}_author" )
            ->set_name( 'author' )
            ->set_post_types( [ PostType\BlogAuthor::SLUG ] )
            ->allow_null()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['author']['instructions'] );
    }
}

( new CommentGroup() );
