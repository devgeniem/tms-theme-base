<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Roles
 *
 * @package TMS\Theme\Base
 */
class Roles implements \TMS\Theme\Base\Interfaces\Controller {

    /**
     * Hooks
     */
    public function hooks() : void {
        $this->modify_administrator_caps();
    }

    /**
     * Modify 'administrator' capabilities
     */
    public function modify_administrator_caps() : void {
        $admin_rights = [
            // Site settings
            'edit_site_setting'              => true,
            'read_site_setting'              => true,
            'delete_site_setting'            => true,
            'edit_others_site_settings'      => true,
            'delete_site_settings'           => true,
            'publish_site_settings'          => true,
            'publish_site_setting'           => true,
            'read_private_site_settings'     => true,
            'delete_private_site_settings'   => true,
            'delete_published_site_settings' => true,
            'delete_others_site_settings'    => true,
            'edit_private_site_settings'     => true,
            'edit_published_site_settings'   => true,
            'edit_site_settings'             => true,

            // Common
            'unfiltered_html'                => true,
        ];

        $admin = get_role( 'administrator' );

        if ( empty( $admin ) ) {
            return;
        }

        foreach ( $admin_rights as $cap => $grant ) {
            $admin->add_cap( $cap, $grant );
        }

        unset( $admin );
    }
}
