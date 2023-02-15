<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ViMagento\HelloWorld\Api\Data;

/**
 * Post interface.
 * @api
 * @since 100.0.2
 */
interface PostInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const POST_ID       = 'post_id';
    const NAME          = 'name';
    const STATUS        = 'status';
    const CONTENT       = 'content';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME   = 'update_time';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();


    /**
     * Set ID
     *
     * @param int $id
     * @return PostInterface
     */
    public function setId($id);

    /**
     * Set name
     *
     * @param string $name
     * @return PostInterface
     */
    public function setName($name);

    /**
     * Set status
     *
     * @param int $status
     * @return PostInterface
     */
    public function setStatus($status);

    /**
     * Set content
     *
     * @param string $content
     * @return PostInterface
     */
    public function setContent($content);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return PostInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return PostInterface
     */
    public function setUpdateTime($updateTime);
}
