<?php

namespace Space48\ConversantDataLayer\Block\Data;

use Space48\ConversantDataLayer\Helper\Data as ConversantHelper;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

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

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        ConversantHelper $conversantHelper,
        CategoryCollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        array $data = []
    ) {
        $this->_coreRegistry = $context->getRegistry();
        $this->jsonHelper = $jsonHelper;
        $this->conversantHelper = $conversantHelper;
        $this->imageBuilder = $context->getImageBuilder();
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
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
        $json = $this->getCategoryData($json);
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

    public function getProductCategories($categoryIds)
    {
        return $this->categoryCollectionFactory->create()
            ->addAttributeToFilter('entity_id', array("in" => $categoryIds))
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('path');
    }

    public function getDeepestCategory($categories)
    {
        $categoryPathCounts = [];

        foreach ($categories as $category) {
            $explodedPath = explode("/", $category->getPath());
            $pathCount = count($explodedPath);
            $categoryPathCounts[$pathCount] = $category;
        }

        ksort($categoryPathCounts);

        return end($categoryPathCounts);

    }

    public function getCategoryById($categoryId)
    {
        return is_numeric($categoryId)
            ? $this->categoryRepository->get($categoryId)
            : "";
    }

    public function getCategoryData($json)
    {
        if ($currentCategory = $this->_coreRegistry->registry('current_category')) {
            $category = $currentCategory;
        } else {
            $categoryIds = $this->getProduct()->getCategoryIds();
            $categories = $this->getProductCategories($categoryIds);
            $category = $this->getDeepestCategory($categories);
        }

        if (!empty($category)) {

            $explodedPath = explode("/", $category->getPath());

            // remove the Root Catalog & Default Category values
            unset($explodedPath[0]);
            unset($explodedPath[1]);


            $explodedPath = array_values($explodedPath);
            $pathArrayCount = count($explodedPath);

            $json['department'] = $this->getCategoryById($explodedPath[0])->getName();

            if ($pathArrayCount == 2) {
                $json['category'] = $this->getCategoryById($explodedPath[1])->getName();
            }

            if ($pathArrayCount >= 3) {
                $json['category'] = $this->getCategoryById($explodedPath[$pathArrayCount-2])->getName();
                $json['subcategory'] = $this->getCategoryById(end($explodedPath))->getName();
            }
        }

        return $json;
    }
}
