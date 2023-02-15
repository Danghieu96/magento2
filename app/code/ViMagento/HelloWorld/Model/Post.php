<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ViMagento\HelloWorld\Model;

use ViMagento\HelloWorld\Api\Data\PostInterface;
use Magento\Framework\App\ObjectManager;
//use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Validation\ValidationException;
use Magento\Framework\Validator\HTML\WYSIWYGValidatorInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Backend\Model\Validator\UrlKey\CompositeUrlKey;
use Magento\Framework\Exception\LocalizedException;

/**
 * Post model
 *
 * @method Post setStoreId(int $storeId)
 * @method int getStoreId()
 */
//class Post extends AbstractModel implements PostInterface, IdentityInterface
class Post extends AbstractModel implements PostInterface
{
    /**
     * Post cache tag
     */
    const CACHE_TAG = 'post_b';

    /**#@+
     * Post's statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**#@-*/

    /**#@-*/
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'post';

    /**
     * @var WYSIWYGValidatorInterface
     */
    private $wysiwygValidator;

    /**
     * @var CompositeUrlKey
     */
    private $compositeUrlValidator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param WYSIWYGValidatorInterface|null $wysiwygValidator
     * @param CompositeUrlKey|null $compositeUrlValidator
     */
    public function __construct(
        Context                    $context,
        Registry                   $registry,
        AbstractResource           $resource = null,
        AbstractDb                 $resourceCollection = null,
        array                      $data = [],
        ?WYSIWYGValidatorInterface $wysiwygValidator = null,
        CompositeUrlKey            $compositeUrlValidator = null
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->wysiwygValidator = $wysiwygValidator
            ?? ObjectManager::getInstance()->get(WYSIWYGValidatorInterface::class);
        $this->compositeUrlValidator = $compositeUrlValidator
            ?? ObjectManager::getInstance()->get(CompositeUrlKey::class);
    }

    /**
     * Construct.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\ViMagento\HelloWorld\Model\ResourceModel\Post::class);
    }

    /**
     * Prevent Posts recursion
     *
     * @return AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        if ($this->hasDataChanges()) {
            $this->setUpdateTime(null);
        }

        $needle = 'Post_id="' . $this->getId() . '"';
        if (strstr($this->getContent(), (string)$needle) !== false) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Make sure that post does not reference the post itself.')
            );
        }

        $errors = $this->compositeUrlValidator->validate($this->getIdentifier());
        if (!empty($errors)) {
            throw new LocalizedException($errors[0]);
        }

        parent::beforeSave();

        //Validating HTML content.
        if ($this->getContent() && $this->getContent() !== $this->getOrigData(self::CONTENT)) {
            try {
                $this->wysiwygValidator->validate($this->getContent());
            } catch (ValidationException $exception) {
                throw new ValidationException(
                    __('Content field contains restricted HTML elements. %1', $exception->getMessage()),
                    $exception
                );
            }
        }

        return $this;
    }

    /**
     * Retrieve Post id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::POST_ID);
    }

    /**
     * Retrieve Post identifier
     *
     * @return string
     */
//    public function getIdentifier()
//    {
//        return (string)$this->getData(self::IDENTIFIER);
//    }

    /**
     * Retrieve Post title
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Retrieve Post content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * Retrieve Post creation time
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Retrieve Post update time
     *
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return PostInterface
     */
    public function setId($id)
    {
        return $this->setData(self::POST_ID, $id);
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return PostInterface
     */
//    public function setIdentifier($identifier)
//    {
//        return $this->setData(self::IDENTIFIER, $identifier);
//    }

    /**
     * Set title
     *
     * @param string $name
     * @return PostInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set status
     *
     * @param int $status
     * @return PostInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return PostInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return PostInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return PostInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }

    /**
     * Prepare Post's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
