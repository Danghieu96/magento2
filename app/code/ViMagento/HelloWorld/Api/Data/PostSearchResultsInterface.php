<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ViMagento\HelloWorld\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for post search results.
 * @api
 * @since 100.0.2
 */
interface PostSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get posts list.
     *
     * @return \ViMagento\HelloWorld\Api\Data\PostInterface[]
     */
    public function getItems();

    /**
     * Set blocks list.
     *
     * @param \ViMagento\HelloWorld\Api\Data\PostInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
