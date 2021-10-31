<?php

namespace Productreview\Reviews\Plugin\Catalog\Block\Product;

use \Magento\Catalog\Block\Product\View as ProductView;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Productreview\Reviews\Helper\Repository;
use Productreview\Reviews\Model\Settings;

class View
{
    private $repository;
    private $scopeConfig;

    public function __construct(
        Repository $repository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->repository  = $repository;
        $this->scopeConfig = $scopeConfig;
    }

    public function beforeToHtml(ProductView $productView)
    {
        if (
            !$this->repository->isModuleActive() ||
            $this->repository->getSettings()->getNativeReviewSystem() === Settings::NATIVE_REVIEW_SYSTEM_ENABLED
        ) {
            return;
        }

        $productView->getLayout()->unsetElement('product.review.form');
        $productView->getLayout()->unsetElement('reviews.tab');
    }
}
