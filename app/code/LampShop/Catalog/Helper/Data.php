<?php

namespace LampShop\Catalog\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Directory\Model\Currency $currency
     */
    protected $_currency;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param  \Magento\Directory\Model\Currency $currency
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency
    )
    {
        $this->_storeManager = $storeManager;
        $this->_currency = $currency;
    }

    /**
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }
}
