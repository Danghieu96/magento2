<?php

namespace ViMagento\HelloWorld\Controller\Post;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use ViMagento\HelloWorld\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;

class Index extends \Magento\Framework\App\Action\Action implements ArgumentInterface
{
    /**
     * @var PostCollectionFactory
     */
    private $postCollectionFactory;

    /**
     * @param PostCollectionFactory $postCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PostCollectionFactory                 $postCollectionFactory
    )
    {
        $this->postCollectionFactory = $postCollectionFactory;
        return parent::__construct($context);
    }

    /**
     * Return products by use resource model collection factory
     *
     * @return \ViMagento\HelloWorld\Model\ResourceModel\Post\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function execute()
    {
        $data = $this->postCollectionFactory->create()
            ->addFieldToSelect('*');
        foreach ($data as $value) {
            echo "<pre>";
            print_r($value->getData());
            echo "</pre>";
        }
    }
}
