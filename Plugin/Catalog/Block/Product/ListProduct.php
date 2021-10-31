<?php

namespace Productreview\Reviews\Plugin\Catalog\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Block\Product\ListProduct as MagentoListProduct;
use Productreview\Reviews\Block\ProductReviewCodeSnippets;
use Productreview\Reviews\Helper\Repository;
use Productreview\Reviews\Model\Settings;

class ListProduct
{
    private $repository;
    private $scopeConfig;
    private $registry;

    public function __construct(
        Repository $repository,
        ScopeConfigInterface $scopeConfig,
        Registry $registry
    ) {
        $this->repository  = $repository;
        $this->scopeConfig = $scopeConfig;
        $this->registry    = $registry;
    }

    public function aroundGetReviewsSummaryHtml(
        MagentoListProduct $listProductBlock,
        callable $proceed,
        Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        if (!$this->repository->isModuleActive()) {

            return $proceed($product, $templateType, $displayIfNoReviews);
        }

        $settings = $this->repository->getSettings();

        if ($settings->getCategoryPageInlineRating() !== Settings::CATEGORY_PAGE_INLINE_RATING_ENABLED) {
            if ($settings->getNativeReviewSystem() !== Settings::NATIVE_REVIEW_SYSTEM_ENABLED) {

                return '';
            }

            return $proceed($product, $templateType, $displayIfNoReviews);
        }

        return ProductReviewCodeSnippets::inlineRating($product->getId(), 'magento2_category_page');
    }
}
