<?php

namespace Productreview\Reviews\Plugin\Review\Block\Product;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Review\Block\Product\ReviewRenderer as MagentoReviewRenderer;
use Magento\Catalog\Model\Product;
use Productreview\Reviews\Block\ProductReviewCodeSnippets;
use Productreview\Reviews\Helper\Repository;
use Productreview\Reviews\Model\Settings;

final class ReviewRenderer
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
        MagentoReviewRenderer $reviewRendererBlock,
        callable $proceed,
        Product $product,
        $templateType = MagentoReviewRenderer::DEFAULT_VIEW,
        $displayIfNoReviews = false
    ) {
        if (!$this->repository->isModuleActive()) {

            return $proceed($product, $templateType, $displayIfNoReviews);
        }

        $settings = $this->repository->getSettings();

        /** @var Product $currentProduct */
        $currentProduct = $this->registry->registry('current_product');

        // if we're on the product page and product is the main product
        if ($currentProduct && $currentProduct->getId() === $product->getId()) {
            if ($settings->getCategoryPageInlineRating() !== Settings::PRODUCT_PAGE_INLINE_RATING_ENABLED) {
                if ($settings->getNativeReviewSystem() !== Settings::NATIVE_REVIEW_SYSTEM_ENABLED) {

                    return '';
                }

                return $proceed($product, $templateType, $displayIfNoReviews);
            }

            return ProductReviewCodeSnippets::inlineRating($product->getId(), 'magento2_product_page');
        }

        if ($settings->getCategoryPageInlineRating() !== Settings::CATEGORY_PAGE_INLINE_RATING_ENABLED) {
            if ($settings->getNativeReviewSystem() !== Settings::NATIVE_REVIEW_SYSTEM_ENABLED) {

                return '';
            }

            return $proceed($product, $templateType, $displayIfNoReviews);
        }

        return ProductReviewCodeSnippets::inlineRating($product->getId(), 'magento2_category_page');
    }
}
