<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ViMagento\HelloWorld\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Registry;
use ViMagento\HelloWorld\Model\PostFactory;
use ViMagento\HelloWorld\Model\ResourceModel\Post as PostResource;

class Delete extends \ViMagento\HelloWorld\Controller\Adminhtml\Post implements HttpPostActionInterface
{
    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var PostResource
     */
    private $postResource;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PostFactory|null $postFactory
     * @param PostResource|null $postResource
     */
    public function __construct(
        Context      $context,
        Registry     $coreRegistry,
        PostFactory  $postFactory = null,
        PostResource $postResource = null
    )
    {
        $this->postFactory = $postFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(postFactory::class);
        $this->postResource = $postResource
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(postResource::class);
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('post_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->postFactory->create();
                $this->postResource->load($model, $id);
                $this->postResource->delete($model);
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the post.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['post_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a post to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
