<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Integrations\Tampere;

/**
 * Person API Controller
 */
class PersonApiController extends ApiController {

    /**
     * API slug
     */
    const SLUG = 'person';

    /**
     * Get endpoint slug
     *
     * @return string
     */
    protected function get_slug() : string {
        return self::SLUG;
    }
}
