<?php

namespace ViMagento\HelloWorld\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'post_id';

    protected function _construct()
    {
        $this->_init('ViMagento\HelloWorld\Model\Post', 'ViMagento\HelloWorld\Model\ResourceModel\Post');
    }
}
