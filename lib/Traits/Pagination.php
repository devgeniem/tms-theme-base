<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Traits;

/**
 * Trait EnrichPost
 *
 * Provides additional post data.
 *
 * @package TMS\Theme\Base\Traits
 */
trait Pagination {

    /**
     * Returns pagination data.
     *
     * @return object
     */
    public function pagination() : ?object {
        if ( isset( $this->pagination->page ) && isset( $this->pagination->max_page ) ) {
            if ( $this->pagination->page <= $this->pagination->max_page ) {
                return $this->pagination;
            }
        }

        return null;
    }
}
