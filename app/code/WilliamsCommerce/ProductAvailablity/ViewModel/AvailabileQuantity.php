<?php
/*
 * @Author: CHANDRA
 * @Email: chandra.sharma@williamscommerce.com
 * @Date: 2021-07-28 14:52:13
 * @Last Modified by: CHANDRA
 * @Last Modified time: 2021-07-30 19:37:59
 * @Description: Round down to the nearest 10 Stock Qty Message
 */


namespace WilliamsCommerce\ProductAvailablity\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class AvailabileQuantity implements ArgumentInterface
{
    /**
     * @var Magento\Framework\Registry
     */
    protected $_registry;
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistryInterface;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Api\StockRegistryInterface  $stockRegistryInterface

    ) {
        $this->_registry = $registry;
        $this->_stockRegistryInterface = $stockRegistryInterface;
    }


    public function getStockAvailabileQuantity($product)
    {
        $stockHtml = "";
        $productTypeInstance = $product->getTypeInstance();
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $usedProducts = $productTypeInstance->getUsedProducts($product);

            foreach ($usedProducts  as $child) {
                $productStockObj =  $this->_stockRegistryInterface->getStockItem($child->getId());
                $divId = "div" . $child->getId();
                $stockQty = $productStockObj->getQty();

                if ($stockQty > 0 && $productStockObj->getIsInStock()) {

                    $nearToTen = ($stockQty / 10);
                    $containDecimal = $this->containsDecimal($nearToTen);
                    if ($containDecimal) {
                        $roundDown = explode('.', $nearToTen);
                        $finalOverStockQTY = $roundDown[0] * 10;
                    } else {
                        $finalOverStockQTY = $stockQty - 10;
                    }

                    if ($finalOverStockQTY >= 10)
                        $stockMsg = "Over " . $finalOverStockQTY . " Available";
                    else
                        $stockMsg = "Less Than 10 Available";

                    $stockHtml .= '<span class="myli" id="' . $divId . '" style="display:none;">' . $stockMsg . '</span>';
                }
            }
        } else {

            $productStockSimple =  $this->_stockRegistryInterface->getStockItem($product->getId());
            $stockQty = $productStockSimple->getQty();
            if ($stockQty > 0 && $productStockSimple->getIsInStock()) {
                $nearToTen = ($stockQty / 10);
                $containDecimal = $this->containsDecimal($nearToTen);
                if ($containDecimal) {
                    $roundDown = explode('.', $nearToTen);
                    $finalOverStockQTY = $roundDown[0] * 10;
                } else {
                    $finalOverStockQTY = $stockQty - 10;
                }

                if ($finalOverStockQTY >= 10)
                    $stockHtml = "Over " . $finalOverStockQTY . " Available";
                else
                    $stockHtml = "Less Than 10 Available";
            }
        }

        return  $stockHtml;
    }

    public function containsDecimal($value)
    {
        if (strpos($value, ".") !== false) {
            return true;
        }
        return false;
    }
}
