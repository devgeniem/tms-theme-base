<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use Geniem\LinkedEvents\LinkedEventsClient;
use Geniem\LinkedEvents\LinkedEventsException;
use TMS\Theme\Base\ACF\Layouts;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

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
            'acf/load_field/name=keyword',
            \Closure::fromCallable( [ $this, 'fill_keyword_choices' ] )
        );

        add_filter(
            'acf/load_field/name=location',
            \Closure::fromCallable( [ $this, 'fill_location_choices' ] )
        );

        add_filter(
            'acf/load_field/name=publisher',
            \Closure::fromCallable( [ $this, 'fill_publisher_choices' ] )
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
            'tab'       => 'Tapahtuma',
            'text'      => [
                'label'        => 'Hakusana',
                'instructions' => '',
            ],
            'keyword'   => [
                'label'        => 'Avainsana',
                'instructions' => '',
            ],
            'location'  => [
                'label'        => 'Tapahtumapaikka',
                'instructions' => '',
            ],
            'publisher' => [
                'label'        => 'Julkaisija',
                'instructions' => '',
            ],
            'event'     => [
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

            $location_field = ( new Field\Select( $strings['location']['label'] ) )
                ->set_key( "${key}_location" )
                ->set_name( 'location' )
                ->use_ui()
                ->allow_null()
                ->use_ajax()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['location']['instructions'] );

            $keyword_field = ( new Field\Select( $strings['keyword']['label'] ) )
                ->set_key( "${key}_keyword" )
                ->set_name( 'keyword' )
                ->use_ui()
                ->use_ajax()
                ->allow_null()
                ->allow_multiple()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['keyword']['instructions'] );

            $publisher_field = ( new Field\Select( $strings['publisher']['label'] ) )
                ->set_key( "${key}_publisher" )
                ->set_name( 'publisher' )
                ->use_ui()
                ->use_ajax()
                ->allow_null()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['publisher']['instructions'] );

            $event_field = ( new Field\Select( $strings['event']['label'] ) )
                ->set_key( "${key}_event" )
                ->set_name( 'event' )
                ->allow_null()
                ->use_ui()
                ->set_instructions( $strings['event']['instructions'] );

            $tab->add_fields( [
                $search_field,
                $location_field,
                $keyword_field,
                $publisher_field,
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
     * Fill publisher choices
     *
     * @param array $field ACF field.
     *
     * @return array
     */
    protected function fill_publisher_choices( $field ) : array {
        return $this->fill_choices_from_response(
            $field,
            $this->get_choices( 'organization' ),
        );
    }

    /**
     * Fill keyword choices
     *
     * @param array $field ACF field.
     *
     * @return array
     */
    protected function fill_keyword_choices( array $field ) : array {
        return $this->fill_choices_from_response(
            $field,
            $this->get_choices( 'keyword', [ 'page_size' => 250 ] ),
        );
    }

    /**
     * Fill location choices
     *
     * @param array $field ACF field.
     *
     * @return array
     */
    protected function fill_location_choices( array $field ) : array {
        return $this->fill_choices_from_response(
            $field,
            $this->get_choices( 'place', [ 'data_source' => 'system' ] ),
        );
    }

    /**
     * Get choices from API
     *
     * @param string $slug           API slug.
     * @param array  $params         API query params.
     * @param int    $cache_duration Cache duration.
     *
     * @return array|bool|mixed|string
     */
    protected function get_choices( string $slug, array $params = [], int $cache_duration = 15 ) {
        $cache_key = 'events-' . $slug;
        $response  = wp_cache_get( $cache_key );

        if ( ! $response ) {
            $client = new LinkedEventsClient( PIRKANMAA_EVENTS_API_URL );

            try {
                $response = $client->get_all( $slug, $params );

                wp_cache_set(
                    $cache_key,
                    $response,
                    '',
                    MINUTE_IN_SECONDS * $cache_duration
                );
            }
            catch ( LinkedEventsException $e ) {
                ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
            }
            catch ( \JsonException $e ) {
                ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
            }
        }

        return $response ?? [];
    }

    /**
     * Fill ACF select choices with response items
     *
     * @param array $field    ACF field.
     * @param array $response API response.
     *
     * @return array
     */
    protected function fill_choices_from_response( array $field, array $response ) : array {
        if ( empty( $response ) ) {
            return $field;
        }

        foreach ( $response as $item ) {
            $name                          = $item->name->fi ?? $item->name;
            $field['choices'][ $item->id ] = $name . ' : ' . $item->id;
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
                Layouts\ArticlesLayout::class,
                Layouts\ImageCarouselLayout::class,
            ]
        );

        foreach ( $component_layouts as $component_layout ) {
            $components_field->add_layout( new $component_layout( $key ) );
        }

        $tab->add_field( $components_field );

        return $tab;
    }
}

( new DynamicEventGroup() );
