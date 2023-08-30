<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use TMS\Theme\Base\ACF\Layouts;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;
use TMS\Theme\Base\EventzClient;
use TMS\Theme\Base\Localization;

/**
 * Class DynamicEventGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class DynamicEventGroup {

    /**
     * PageGroup constructor.
     */
    public function __construct() {
        add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_fields' ] )
        );

        add_filter(
            'acf/load_field/name=category',
            \Closure::fromCallable( [ $this, 'fill_category_choices' ] )
        );

        add_filter(
            'acf/load_field/name=area',
            \Closure::fromCallable( [ $this, 'fill_area_choices' ] )
        );

        add_filter(
            'acf/load_field/name=target',
            \Closure::fromCallable( [ $this, 'fill_target_choices' ] )
        );

        add_filter(
            'acf/load_field/name=tag',
            \Closure::fromCallable( [ $this, 'fill_tag_choices' ] )
        );
    }

    /**
     * Register fields
     */
    protected function register_fields() : void {
        try {
            $group_title = _x( 'Tiedot', 'theme ACF', 'tms-theme-base' );

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_dynamic_event_fields' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'post_type', '==', PostType\DynamicEvent::SLUG );

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'normal' )
                ->set_hidden_elements(
                    [
                        'discussion',
                        'comments',
                        'format',
                        'send-trackbacks',
                    ]
                );

            $field_group->add_fields(
                apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_event_tab( $field_group->get_key() ),
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
     * Get event tab
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_event_tab( string $key ) : ?Field\Tab {
        $strings = [
            'tab'      => 'Tapahtuma',
            'text'     => [
                'label'        => 'Hakusana',
                'instructions' => '',
            ],
            'category' => [
                'label'        => 'Kategoria',
                'instructions' => '',
            ],
            'area'     => [
                'label'        => 'Alue',
                'instructions' => '',
            ],
            'target'   => [
                'label'        => 'Kohderyhmä',
                'instructions' => '',
            ],
            'tag'      => [
                'label'        => 'Tag',
                'instructions' => '',
            ],
            'event'    => [
                'label'        => 'Tapahtuma',
                'instructions' => '',
            ],
        ];

        try {
            $tab = ( new Field\Tab( $strings['tab'] ) )
                ->set_placement( 'left' );

            $search_field = ( new Field\Text( $strings['text']['label'] ) )
                ->set_key( "${key}_text" )
                ->set_name( 'text' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['text']['instructions'] );

            $area_field = ( new Field\Select( $strings['area']['label'] ) )
                ->set_key( "{$key}_area" )
                ->set_name( 'area' )
                ->use_ui()
                ->allow_null()
                ->allow_multiple()
                ->use_ajax()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['area']['instructions'] );

            $category_field = ( new Field\Select( $strings['category']['label'] ) )
                ->set_key( "{$key}_category" )
                ->set_name( 'category' )
                ->use_ui()
                ->use_ajax()
                ->allow_null()
                ->allow_multiple()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['category']['instructions'] );

            $target_field = ( new Field\Select( $strings['target']['label'] ) )
                ->set_key( "${key}_target" )
                ->set_name( 'target' )
                ->use_ui()
                ->use_ajax()
                ->allow_null()
                ->allow_multiple()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['target']['instructions'] );

            $tag_field = ( new Field\Select( $strings['tag']['label'] ) )
                ->set_key( "${key}_tag" )
                ->set_name( 'tag' )
                ->use_ui()
                ->use_ajax()
                ->allow_null()
                ->allow_multiple()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['tag']['instructions'] );

            $event_field = ( new Field\Select( $strings['event']['label'] ) )
                ->set_key( "${key}_event" )
                ->set_name( 'event' )
                ->allow_null()
                ->use_ui()
                ->set_instructions( $strings['event']['instructions'] );

            $tab->add_fields( [
                $search_field,
                $area_field,
                $category_field,
                $target_field,
                $tag_field,
                $event_field,
            ] );

            return $tab;
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }

    /**
     * Fill target choices
     *
     * @param array $field ACF field.
     *
     * @return array
     */
    protected function fill_target_choices( $field ) : array {
        if ( ! is_admin() ) {
            return $field;
        }

        return $this->get_choices_by_name( 'targets', $field );
    }

    /**
     * Fill category choices
     *
     * @param array $field ACF field.
     *
     * @return array
     */
    protected function fill_category_choices( array $field ) : array {
        if ( ! is_admin() ) {
            return $field;
        }

        return $this->get_choices_by_name( 'categories', $field );
    }

    /**
     * Fill area choices
     *
     * @param array $field ACF field.
     *
     * @return array
     */
    protected function fill_area_choices( array $field ) : array {
        if ( ! is_admin() ) {
            return $field;
        }

        return $this->get_choices_by_name( 'areas', $field );
    }

    /**
     * Fill area choices
     *
     * @param array $field ACF field.
     *
     * @return array
     */
    protected function fill_tag_choices( array $field ) : array {
        if ( ! is_admin() ) {
            return $field;
        }

        return $this->get_choices_by_name( 'tags', $field );
    }

    /**
     * Fill area choices
     *
     * @param string $name name of the choice.
     * @param array  $field ACF field.
     *
     * @return array
     */
    protected function get_choices_by_name( string $name, array $field ) : array {
        if ( ! is_admin() ) {
            return $field;
        }

        $cache_key = 'events-' . $name;
        $response  = wp_cache_get( $cache_key );

        if ( ! $response ) {
            try {
                $lang_key = Localization::get_current_language();
                $client   = new EventzClient( PIRKANMAA_EVENTZ_API_URL, PIRKANMAA_EVENTZ_API_KEY );
                $response = $client->{'get_' . $name }( $lang_key );

                wp_cache_set(
                    $cache_key,
                    $response,
                    '',
                    MINUTE_IN_SECONDS * 15
                );
            }
            catch ( Exception $e ) {
                ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
            }
        }

        if ( ! empty( $response ) ) {
            foreach ( $response as $item ) {
                $field['choices'][ $item->_id ] = $item->name;
            }
        }

        return $field;
    }

    /**
     * Get components tab
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_components_tab( string $key ) : Field\Tab {
        $strings = [
            'tab'        => 'Komponentit',
            'components' => [
                'title'        => 'Komponentit',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $components_field = ( new Field\FlexibleContent( $strings['components']['title'] ) )
            ->set_key( "${key}_components" )
            ->set_name( 'components' )
            ->set_instructions( $strings['components']['instructions'] );

        $component_layouts = apply_filters(
            'tms/acf/field/' . $components_field->get_key() . '/layouts',
            [
                Layouts\ImageBannerLayout::class,
                Layouts\CallToActionLayout::class,
                Layouts\LogoWallLayout::class,
                Layouts\SocialMediaLayout::class,
                Layouts\MapLayout::class,
                Layouts\EventsLayout::class,
                Layouts\ArticlesLayout::class,
                Layouts\ImageCarouselLayout::class,
                Layouts\NoticeBannerLayout::class,
                Layouts\TextBlockLayout::class,
                Layouts\ContactsLayout::class,
                Layouts\VideoLayout::class,
            ],
            $key
        );

        foreach ( $component_layouts as $component_layout ) {
            $components_field->add_layout( new $component_layout( $key ) );
        }

        $tab->add_field( $components_field );

        return $tab;
    }
}

( new DynamicEventGroup() );
