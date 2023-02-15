<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ViMagento\HelloWorld\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context;
use ViMagento\HelloWorld\Model\PostFactory;
use ViMagento\HelloWorld\Model\ResourceModel\Post as PostResource;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Cms\Api\Data\BlockInterface;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'ViMagento_HelloWorld::post';

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var PostResource
     */
    private $postResource;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param PostFactory $postFactory
     * @param PostResource $postResource
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context      $context,
        PostFactory  $postFactory,
        PostResource $postResource,
        JsonFactory  $jsonFactory
    )
    {
        parent::__construct($context);
        $this->postFactory = $postFactory;
        $this->postResource =  $postResource;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {

                /** @var \ViMagento\HelloWorld\Model\Post $model */
                $model = $this->postFactory->create();
                foreach (array_keys($postItems) as $postId) {
                    $this->postResource->load($model, $postId);
                    try {
                        $model->setData(array_merge($model->getData(), $postItems[$postId]));
                        $this->postResource->save($model);
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithBlockId(
                            $model->getId(),
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add block title to error message
     *
     * @param PostFactory $post
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithBlockId($postId, $errorText)
    {
        return '[Post ID: ' . $postId . '] ' . $errorText;
    }
}
