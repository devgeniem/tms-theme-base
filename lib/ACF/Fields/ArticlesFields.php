<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType\Post;
use TMS\Theme\Base\Taxonomy\Category;
use Geniem\ACF\ConditionalLogicGroup;

/**
 * Class ArticlesFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class ArticlesFields extends \Geniem\ACF\Field\Group {

    /**
     * UI Strings.
     *
     * @var array
     */
    private array $strings;

    /**
     * The constructor for field.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) {
        parent::__construct( $label, $key, $name );

        $this->strings = [
            'title'             => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description'       => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'limit'             => [
                'label'        => 'Lukumäärä',
                'instructions' => 'Valitse väliltä 4-12',
            ],
            'highlight_article' => [
                'label'        => 'Korostettu artikkeli',
                'instructions' => '',
            ],
            'article'           => [
                'label'        => 'Artikkeli',
                'instructions' => 'Ylikirjoita artikkelin ote. Tyhjäksi jättäminen käyttää oletusta.',
            ],
            'article_excerpt'   => [
                'label'        => 'Ote',
                'instructions' => '',
            ],
            'feed_type'         => [
                'label'          => 'Listuksen tyyppi',
                'instructions'   => '',
                'type_automatic' => 'Automaattinen',
                'type_manual'    => 'Manuaalinen',
            ],
            'category'          => [
                'label'        => 'Kategoriat',
                'instructions' => 'Esitä artikkeleja valituista kategorioista',
            ],
            'article_repeater'  => [
                'label'        => 'Artikkelit',
                'instructions' => 'Valitse 4-12 artikkelia',
                'button'       => 'Lisää artikkeli',
            ],
            'link'              => [
                'label'        => 'Linkki',
                'instructions' => '',
            ],
            'display_image'     => [
                'label'        => 'Kuvat käytössä',
                'instructions' => '',
            ],
        ];

        try {
            $this->add_fields( $this->sub_fields() );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * Add Repeater selection: Relationship.
     *
     * @return \Geniem\ACF\Field\Group
     * @throws \Geniem\ACF\Exception Thrown if mandatory fields have not been set.
     */
    protected function article_item_field_group() : Field\Group {
        $group = ( new Field\Group( $this->strings['article']['label'] ) )
            ->hide_label()
            ->set_key( $this->get_key() . '_article_item' )
            ->set_name( 'article_item' );

        $article_field = ( new Field\PostObject( $this->strings['article']['label'] ) )
            ->set_key( $this->get_key() . '_article' )
            ->set_name( 'article' )
            ->set_return_format( 'id' )
            ->set_required()
            ->set_post_types( [ Post::SLUG ] )
            ->set_instructions( $this->strings['article']['instructions'] );

        $excerpt_field = ( new Field\Textarea( $this->strings['article_excerpt']['label'] ) )
            ->set_key( $this->get_key() . '_article_excerpt' )
            ->set_name( 'article_excerpt' )
            ->set_rows( 4 )
            ->set_instructions( $this->strings['article_excerpt']['instructions'] );

        $group->add_fields( [ $article_field, $excerpt_field ] );

        return $group;
    }

    /**
     * This returns all sub fields of the parent groupable.
     *
     * @return array
     * @throws \Geniem\ACF\Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $key = $this->get_key();

        $title_field = ( new Field\Text( $this->strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $this->strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $this->strings['description']['label'] ) )
            ->set_key( "${key}_description" )
            ->set_name( 'description' )
            ->set_wrapper_width( 50 )
            ->set_rows( 4 )
            ->set_instructions( $this->strings['description']['instructions'] );

        $link_field = ( new Field\Link( $this->strings['link']['label'] ) )
            ->set_key( $this->get_key() . '_link' )
            ->set_name( 'link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $this->strings['link']['instructions'] );

        $highlight_article_field = ( new Field\PostObject( $this->strings['highlight_article']['label'] ) )
            ->set_key( "${key}_highlight_article" )
            ->set_name( 'highlight_article' )
            ->set_post_types( [ Post::SLUG ] )
            ->set_instructions( $this->strings['highlight_article']['instructions'] );

        $display_image_field = ( new Field\TrueFalse( $this->strings['display_image']['label'] ) )
            ->set_key( "${key}_display_image" )
            ->set_name( 'display_image' )
            ->set_wrapper_width( 50 )
            ->set_default_value( true )
            ->use_ui()
            ->set_instructions( $this->strings['display_image']['instructions'] );

        $feed_type_field = ( new Field\Radio( $this->strings['feed_type']['label'] ) )
            ->set_key( "${key}_feed_type" )
            ->set_name( 'feed_type' )
            ->set_choices( [
                'automatic' => $this->strings['feed_type']['type_automatic'],
                'manual'    => $this->strings['feed_type']['type_manual'],
            ] )
            ->set_instructions( $this->strings['feed_type']['instructions'] );

        $rule_group_automatic = ( new ConditionalLogicGroup() )
            ->add_rule( $feed_type_field, '==', 'automatic' );
        $rule_group_manual    = ( new ConditionalLogicGroup() )
            ->add_rule( $feed_type_field, '==', 'manual' );

        $category_field = ( new Field\Taxonomy( $this->strings['category']['label'] ) )
            ->set_key( "${key}_category" )
            ->set_name( 'category' )
            ->set_taxonomy( Category::SLUG )
            ->set_return_format( 'id' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $this->strings['category']['instructions'] );

        $limit_field = ( new Field\Number( $this->strings['limit']['label'] ) )
            ->set_key( "${key}_number" )
            ->set_name( 'number' )
            ->set_min( 4 )
            ->set_max( 12 )
            ->set_default_value( 12 )
            ->set_wrapper_width( 50 )
            ->set_instructions( $this->strings['limit']['instructions'] );

        $category_field->add_conditional_logic( $rule_group_automatic );
        $limit_field->add_conditional_logic( $rule_group_automatic );

        $article_item_manual_field_group = $this->article_item_field_group();

        $articles_repeater_field = ( new Field\Repeater( $this->strings['article_repeater']['label'] ) )
            ->set_key( $this->get_key() . '_article_repeater' )
            ->set_name( 'article_repeater' )
            ->set_layout( 'block' )
            ->set_min( 4 )
            ->set_max( 12 )
            ->set_button_label( $this->strings['article_repeater']['button'] )
            ->set_instructions( $this->strings['article_repeater']['instructions'] );

        $articles_repeater_field->add_field( $article_item_manual_field_group );
        $articles_repeater_field->add_conditional_logic( $rule_group_manual );

        return [
            $title_field,
            $description_field,
            $highlight_article_field,
            $feed_type_field,
            $articles_repeater_field,
            $category_field,
            $limit_field,
            $link_field,
            $display_image_field,
        ];
    }
}
