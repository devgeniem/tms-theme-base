<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\ConditionalLogicGroup;
use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class GridFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class GridFields extends Field\Group {
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
            'repeater'        => [
                'label'        => 'Nostot',
                'instructions' => 'Valitse 3-8 kpl nostoja',
                'button'       => 'Lisää nosto',
            ],
            'highlight_first' => [
                'label'        => 'Korosta ensimmäinen',
                'instructions' => 'Korostetaanko ensimmäinen nosto',
                'on'           => 'Korostetaan',
                'off'          => 'Ei korostusta',
            ],
            'selector'        => [
                'label'        => 'Noston tyypin valinta',
                'instructions' => 'Valitse onko nosto sivu, tai artikkeli vai vapaavalintainen sisältö',
                'on'           => 'Sivu tai artikkeli',
                'off'          => 'Vapaavalintainen nosto',
            ],
            'relationship'    => [
                'label'        => 'Sivu tai artikkeli',
                'instructions' => '',
            ],
            'custom'          => [
                'label'        => 'Vapaavalintainen nosto',
                'instructions' => '',
            ],
            'title'           => [
                'label'        => 'Otsikko',
                'instructions' => 'Vapaavalinnainen otsikko',
            ],
            'description'     => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'image'           => [
                'label'        => 'Kuva',
                'instructions' => '',
            ],
            'link'            => [
                'label'        => 'Linkki',
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
     * This returns all sub fields of the parent groupable.
     *
     * @return array
     * @throws Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $highlight_first = ( new Field\TrueFalse( $this->strings['highlight_first']['label'] ) )
            ->set_key( $this->get_key() . '_highlight_first' )
            ->set_name( 'highlight_first' )
            ->use_ui()
            ->set_instructions( $this->strings['highlight_first']['instructions'] )
            ->set_ui_on_text( $this->strings['highlight_first']['on'] )
            ->set_ui_off_text( $this->strings['highlight_first']['off'] )
            ->set_default_value( false );

        $repeater = ( new Field\Repeater( $this->strings['repeater']['label'] ) )
            ->set_key( $this->get_key() . "_repeater" )
            ->set_name( 'repeater' )
            ->set_instructions( $this->strings['repeater']['instructions'] )
            ->set_button_label( $this->strings['repeater']['button'] )
            ->set_layout( 'block' )
            ->set_min( 3 )
            ->set_max( 8 );

        $selector = ( new Field\TrueFalse( $this->strings['selector']['label'] ) )
            ->set_key( $this->get_key() . "_selector" )
            ->set_name( 'selector' )
            ->use_ui()
            ->set_default_value( true )
            ->set_instructions( $this->strings['selector']['instructions'] )
            ->set_ui_on_text( $this->strings['selector']['on'] )
            ->set_ui_off_text( $this->strings['selector']['off'] );

        $repeater->add_field( $selector );

        $rule_group_true = ( new ConditionalLogicGroup() )->add_rule( $selector, '==', '1' );
        $rule_group_false = ( new ConditionalLogicGroup() )->add_rule( $selector, '!=', '1' );

        $grid_item_relationship = $this->grid_item_relationship_fields();
        $grid_item_relationship->add_conditional_logic( $rule_group_true );
        $repeater->add_field( $grid_item_relationship );

        $grid_item_custom = $this->grid_item_type_custom_fields();
        $grid_item_custom->add_conditional_logic( $rule_group_false );
        $repeater->add_field( $grid_item_custom );

        return [
            $highlight_first,
            $repeater,
        ];
    }

    /**
     * Add Repeater selection: Relationship.
     *
     * @return \Geniem\ACF\Field\Group
     * @throws \Geniem\ACF\Exception
     */
    protected function grid_item_relationship_fields() : Field\Group {
        $group = ( new Field\Group( $this->strings['relationship']['label'] ) )
            ->hide_label()
            ->set_key( $this->get_key() . "_grid_item_relationship" )
            ->set_name( 'grid_item_relationship' );

        $relationship = ( new Field\PostObject( $this->strings['relationship']['label'] ) )
            ->set_instructions( $this->strings['relationship']['instructions'] )
            ->set_key( $this->get_key() . "_item" )
            ->set_name( 'item' )
            ->set_post_types( [ 'post', 'page' ] );

        $link_title = ( new Field\Text( $this->strings['title']['label'] ) )
            ->set_key( $this->get_key() . '_link_title' )
            ->set_name( 'link_title' )
            ->set_instructions( $this->strings['title']['instructions'] );

        $group->add_fields( [ $relationship, $link_title ] );

        return $group;
    }

    /**
     * Add Repeater selection: Custom grid item fields.
     *
     * @return \Geniem\ACF\Field\Group
     * @throws \Geniem\ACF\Exception
     */
    protected function grid_item_type_custom_fields() : Field\Group {
        $grid_item_custom = ( new Field\Group( $this->strings['custom']['label'] ) )
            ->set_key( $this->get_key() . "_grid_item_custom" )
            ->set_name( 'grid_item_custom' )
            ->set_instructions( $this->strings['custom']['instructions'] );

        $title_field = ( new Field\Text( $this->strings['title']['label'] ) )
            ->set_key( $this->get_key() . "_title" )
            ->set_name( 'title' )
            ->set_instructions( $this->strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $this->strings['description']['label'] ) )
            ->set_key( $this->get_key() . "_description" )
            ->set_name( 'description' )
            ->set_maxlength( 200 )
            ->set_instructions( $this->strings['description']['instructions'] );

        $image_field = ( new Field\Image( $this->strings['image']['label'] ) )
            ->set_key( $this->get_key() . '_image' )
            ->set_name( 'image' )
            ->set_instructions( $this->strings['image']['instructions'] );

        $link_field = ( new Field\Link( $this->strings['link']['label'] ) )
            ->set_key( $this->get_key() . "_link" )
            ->set_name( 'link' )
            ->set_instructions( $this->strings['link']['instructions'] );

        $grid_item_custom->add_fields( [
            $title_field,
            $description_field,
            $image_field,
            $link_field,
        ] );

        return $grid_item_custom;
    }
}
