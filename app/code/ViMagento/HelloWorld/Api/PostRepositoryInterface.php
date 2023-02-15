<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ViMagento\HelloWorld\Api;

/**
 * Post CRUD interface.
 * @api
 * @since 100.0.2
 */
interface PostRepositoryInterface
{
    /**
     * Save post.
     *
     * @param \ViMagento\HelloWorld\Api\Data\PostInterface $post
     * @return \ViMagento\HelloWorld\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\PostInterface $post);

    /**
     * Retrieve post.
     *
     * @param string $postId
     * @return \ViMagento\HelloWorld\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($postId);

    /**
     * Retrieve posts matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \ViMagento\HelloWorld\Api\Data\postSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete post.
     *
     * @param \ViMagento\HelloWorld\Api\Data\PostInterface $post
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\PostInterface $post);

    /**
     * Delete post by ID.
     *
     * @param string $postId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($postId);
}
