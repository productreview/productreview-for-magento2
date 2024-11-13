<?php

namespace Productreview\Reviews\Block;

use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Productreview\Reviews\Helper\Repository;
use \Exception;

class ProductReviewCodeSnippets extends Template
{
    private $session;
    private $registry;
    private $repository;

    public function __construct(
        Context $context,
        Session $session,
        Registry $registry,
        Repository $repository,
        array $data = []
    ) {
        $this->session    = $session;
        $this->registry   = $registry;
        $this->repository = $repository;

        parent::__construct($context, $data);
    }

    public function isModuleActive($storeId = null)
    {
        return $this->repository->isModuleActive($storeId);
    }

    public function getCredentials($storeId = null)
    {
        $credentials = $this->repository->findCredentials($storeId);

        if (!$credentials) {
            throw new Exception('No credentials found. Use isModuleActive before trying to use get credentials.');
        }

        return $credentials;
    }

    public function getSettings($storeId = null)
    {
        return $this->repository->getSettings($storeId);
    }

    /**
     * @return Product|null
     */
    public function findCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return Order
     */
    public function getLastRealOrder()
    {
        return $this->session->getLastRealOrder();
    }

    static public function mainLoaderScript($externalCatalogId, $brandId = null)
    {
        $json = json_encode(
            array_merge(
                ['externalCatalogId' => $externalCatalogId],
                $brandId ? ['brandId' => $brandId] : []
            )
        );

        $env = isset($_ENV['PRODUCTREVIEW_ENV']) ? $_ENV['PRODUCTREVIEW_ENV'] : 'prod';

        $loaderSrc = ($env === 'prod') ? 'https://cdn.productreview.com.au/assets/widgets/loader.js' : 'https://www.pr.test/dist/widgets/loader.js';

        return <<<HTML
<script>
  window.__productReviewSettings = $json;
</script>
<script src="$loaderSrc" async></script>
HTML;
    }

    static public function conversionTracking($externalOwnerId, $orderId, $orderTotal, $orderCurrency)
    {

        return self::generateConventionalSnippet(
            'conversion-tracking',
            [
                'integrationHook' => 'magento2',
                'externalOwnerId' => $externalOwnerId,
                'order' => [
                    'id'       => $orderId,
                    'amount'   => $orderTotal,
                    'currency' => $orderCurrency
                ]
            ]
        );
    }

    static public function comprehensiveWidget($productId) {
        $script = self::generateConventionalSnippet(
            'comprehensive-widget',
            [
                'container'             => '#pr-reviews-comprehensive-widget',
                'identificationDetails' => [
                    'type'       => 'single',
                    'strategy'   => 'from-catalog-external-entry-external-id',
                    'identifier' => "$productId"
                ]
            ]
        );

        return <<<HTML
<div id="pr-reviews-comprehensive-widget"></div>
$script
HTML;
    }

    static public function inlineRating($productId, $alias)
    {

        return <<<HTML
<div data-id="$productId" class="pr-inline-rating-$alias">
  &nbsp;
</div>
HTML;
    }

    static public function inlineRatingLoaderScript($alias) {

        return self::generateConventionalSnippet(
            'inline-rating',
            [
                'nodes'                  => ".pr-inline-rating-$alias",
                'alias'                  => $alias,
                'identificationStrategy' => 'from-catalog-external-entry-external-id'
            ]
        );
    }

    static private function generateConventionalSnippet($widgetSlug, $settings)
    {
        $settingsAsJavascriptString = is_array($settings) ? json_encode($settings) : $settings;

        return <<<HTML
<script>
  window.__productReviewCallbackQueue = window.__productReviewCallbackQueue || [];
  window.__productReviewCallbackQueue.push(function(ProductReview) {
    ProductReview.use('$widgetSlug', $settingsAsJavascriptString);
  });
</script>
HTML;
    }
}
