<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ViMagento\HelloWorld\Controller\Adminhtml\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use ViMagento\HelloWorld\Model\PostFactory;
use ViMagento\HelloWorld\Model\ResourceModel\Post as PostResource;

/**
 * Edit CMS block action.
 */
class Edit extends \ViMagento\HelloWorld\Controller\Adminhtml\Post implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var PostResource
     */
    private $postResource;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param PostFactory $postFactory
     * @param PostResource $postResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context        $context,
        \Magento\Framework\Registry                $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        PostFactory                                $postFactory,
        PostResource                               $postResource
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit CMS block
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('post_id');
        $post = $this->postFactory->create();
        // 2. Initial checking
        if ($id) {
            $this->postResource->load($post, $id);
            if (!$post->getId()) {
                $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend($post->getId() ? $post->getName() : __('New Post'));
        return $resultPage;
    }
}
