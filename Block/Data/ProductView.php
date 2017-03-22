<?php

namespace Space48\ConversantDataLayer\Block\Data;

use Space48\ConversantDataLayer\Helper\Data as ConversantHelper;

class ProductView extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $_product = null;

    protected $imageBuilder = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * ConversantDataLayer Helper
     *
     * @var \Space48\ConversantDataLayer\Helper\Data
     */
    protected $conversantHelper = null;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $registry,
        ConversantHelper $conversantHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->jsonHelper = $jsonHelper;
        $this->conversantHelper = $conversantHelper;
        $this->imageBuilder = $context->getImageBuilder();
        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    protected function _toHtml()
    {
        if (!$this->conversantHelper->isEnabled()) {
            return '';
        }

        return $this->getOutput();
    }

    public function getOutput()
    {
        $json = $result = array();
        $json['promo_id'] = "5";

        if ($brand = $this->getBrand()) {
            $json['brand'] = $brand;
        }

        $relatedProductSkus = $this->getRelatedProductSkus();

        if (!empty($relatedProductSkus)) {
            $json['related_products'] = $relatedProductSkus;
        }

        $result[] = 'dataLayer.push(' . $this->jsonHelper->jsonEncode($json) . ");\n";

        return implode("\n", $result);
    }

    public function getRelatedProductSkus()
    {
        $relatedProductSkus = array();
        $relatedProducts = $this->getProduct()->getRelatedProducts();

        if (!empty($relatedProducts)) {
            foreach ($relatedProducts as $relatedProduct) {
                $relatedProductSkus[] = $relatedProduct->getSku();
            }
        }

        return $relatedProductSkus;
    }

    public function getBrand()
    {
        $brand = $this->getProduct()->getAttributeText($this->getBrandAttributeCode());
        return $brand ? $brand : $this->getDefaultBrand();
    }

    public function getBrandAttributeCode()
    {
        return $this->conversantHelper->getConfig('brand_attribute_code');
    }

    public function getDefaultBrand()
    {
        return $this->conversantHelper->getConfig('brand');
    }

}