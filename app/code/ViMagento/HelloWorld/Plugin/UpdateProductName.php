<?php

namespace ViMagento\HelloWorld\Plugin;

class UpdateProductName
{
    public function afterGetName(\Magento\Catalog\Model\Product $subject, $result)
    {
        $result = $result."-Vi-Magento HelloWorld";
        return $result;
    }
}
