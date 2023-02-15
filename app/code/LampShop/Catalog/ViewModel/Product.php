<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LampShop\Catalog\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Amasty\ShopbyBase\Model\OptionSettingRepository;

/**
 * Class Product
 * @package LampShop\Catalog\ViewModel
 */
class Product implements ArgumentInterface
{
    const ATTR_GUARANTEE = 'attr_guarantee';
    /**
     * @var OptionSettingRepository
     */
    protected $_optionSettingRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_helperImage;

    /**
     * Product constructor.
     * @param OptionSettingRepository $optionSettingRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        OptionSettingRepository $optionSettingRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Helper\Image $helperImage
    ) {
        $this->_optionSettingRepository = $optionSettingRepository;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_helperImage = $helperImage;
    }
    public function getImageOptions($attr, $optionId){
        $storeId = $this->_storeManager->getStore()->getId();
        $optionsSetting = $this->_optionSettingRepository->getByParams($attr,$optionId,$storeId);
        return $optionsSetting;
    }

    public function getCurrentProduct(){
        return $this->_coreRegistry->registry('product');
    }

    /**
     * @param $_product
     * @return string
     */
    public function getImageProduct($_product){
        $imageUrl = $this->_helperImage->init($_product, 'product_page_image_small')
            ->setImageFile($_product->getProductLogo())
            ->resize(100)
            ->getUrl();
        return $imageUrl;
    }
}
