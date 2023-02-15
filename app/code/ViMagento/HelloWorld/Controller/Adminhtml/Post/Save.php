<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ViMagento\HelloWorld\Controller\Adminhtml\Post;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use ViMagento\HelloWorld\Api\PostRepositoryInterface;
use ViMagento\HelloWorld\Model\PostFactory;
use ViMagento\HelloWorld\Model\ResourceModel\Post as PostResource;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * Save post action.
 */
class Save extends \ViMagento\HelloWorld\Controller\Adminhtml\Post implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var PostResource
     */
    private $postResource;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param PostFactory|null $postFactory
     * @param PostResource|null $postResource
     * @param PostRepositoryInterface|null $postRepository
     */
    public function __construct(
        Context                 $context,
        Registry                $coreRegistry,
        DataPersistorInterface  $dataPersistor,
        PostFactory             $postFactory = null,
        PostResource            $postResource = null,
        PostRepositoryInterface $postRepository = null
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->postFactory = $postFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(postFactory::class);
        $this->postResource = $postResource
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(postResource::class);
        $this->postRepository = $postRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(BlockRepositoryInterface::class);
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['post_id'])) {
                $data['post_id'] = null;
            }

            /** @var \ViMagento\HelloWorld\Model\Post $model */
            $model = $this->postFactory->create();

            $id = $this->getRequest()->getParam('post_id');
            if ($id) {
                try {
                    $model = $this->postRepository->getById($id);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);
            try {
                $this->postRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
                $this->dataPersistor->clear('post');
                return $this->processPostReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
            }
            $this->dataPersistor->set('post', $data);
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the block return
     *
     * @param \ViMagento\HelloWorld\Model\Post $model
     * @param array $data
     * @param \Magento\Framework\Controller\ResultInterface $resultRedirect
     * @return \Magento\Framework\Controller\ResultInterface
     */
    private function processPostReturn($model, $data, $resultRedirect)
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect === 'continue') {
            $resultRedirect->setPath('*/*/edit', ['post_id' => $model->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } elseif ($redirect === 'duplicate') {
            $duplicateModel = $this->postFactory->create(['data' => $data]);
            $duplicateModel->setId(null);
            $this->postRepository->save($duplicateModel);
            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('You duplicated the post.'));
            $this->dataPersistor->set('post', $data);
            $resultRedirect->setPath('*/*/edit', ['post_id' => $id]);
        }
        return $resultRedirect;
    }
}
