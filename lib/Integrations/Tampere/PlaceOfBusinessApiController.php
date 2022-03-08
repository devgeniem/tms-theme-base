<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Integrations\Tampere;

/**
 * Place of Business API Controller
 */
class PlaceOfBusinessApiController extends ApiController {

    /**
     * API slug
     */
    const SLUG = 'place_of_business';

    /**
     * Get endpoint slug
     *
     * @return string
     */
    protected function get_slug() : string {
        return self::SLUG;
    }
}
