<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ViMagento\HelloWorld\Model;

use ViMagento\HelloWorld\Api\PostRepositoryInterface;
use ViMagento\HelloWorld\Api\Data;
use ViMagento\HelloWorld\Model\ResourceModel\Post as ResourcePost;
use ViMagento\HelloWorld\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Default post repo impl.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PostRepository implements PostRepositoryInterface
{
    /**
     * @var ResourcePost
     */
    protected $resource;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var PostCollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * @var Data\PostSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \ViMagento\HelloWorld\Api\Data\PostInterfaceFactory
     */
    protected $dataPostFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param ResourcePost $resource
     * @param PostFactory $postFactory
     * @param Data\PostInterfaceFactory $dataPostFactory
     * @param PostCollectionFactory $postCollectionFactory
     * @param Data\PostSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param HydratorInterface|null $hydrator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourcePost $resource,
        PostFactory $postFactory,
        \ViMagento\HelloWorld\Api\Data\PostInterfaceFactory $dataPostFactory,
        PostCollectionFactory $postCollectionFactory,
        Data\PostSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null,
        ?HydratorInterface $hydrator = null
    ) {
        $this->resource = $resource;
        $this->postFactory = $postFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPostFactory = $dataPostFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
        $this->hydrator = $hydrator ?? ObjectManager::getInstance()->get(HydratorInterface::class);
    }

    /**
     * Save Post data
     *
     * @param \ViMagento\HelloWorld\Api\Data\PostInterface $post
     * @return Post
     * @throws CouldNotSaveException
     */
    public function save(Data\PostInterface $post)
    {
        if (empty($post->getStoreId())) {
            $post->setStoreId($this->storeManager->getStore()->getId());
        }

        if ($post->getId() && $post instanceof Post && !$post->getOrigData()) {
            $post = $this->hydrator->hydrate($this->getById($post->getId()), $this->hydrator->extract($post));
        }

        try {
            $this->resource->save($post);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $post;
    }

    /**
     * Load Post data by given Post Identity
     *
     * @param string $postId
     * @return Post
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($postId)
    {
        $post = $this->postFactory->create();
        $this->resource->load($post, $postId);
        if (!$post->getId()) {
            throw new NoSuchEntityException(__('The Post with the "%1" ID doesn\'t exist.', $postId));
        }
        return $post;
    }

    /**
     * Load Post data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \ViMagento\HelloWorld\Api\Data\PostSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \ViMagento\HelloWorld\Model\ResourceModel\Post\Collection $collection */
        $collection = $this->PostCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\PostSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Post
     *
     * @param \ViMagento\HelloWorld\Api\Data\PostInterface $post
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\PostInterface $post)
    {
        try {
            $this->resource->delete($post);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Post by given Post Identity
     *
     * @param string $postId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($postId)
    {
        return $this->delete($this->getById($postId));
    }

    /**
     * Retrieve collection processor
     *
     * @deprecated 102.0.0
     * @return CollectionProcessorInterface
     */
//    private function getCollectionProcessor()
//    {
//        //phpcs:disable Magento2.PHP.LiteralNamespaces
//        if (!$this->collectionProcessor) {
//            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
//                'ViMagento\HelloWorld\Model\Api\SearchCriteria\PostCollectionProcessor'
//            );
//        }
//        return $this->collectionProcessor;
//    }
}
