<?php

namespace Productreview\Reviews\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Productreview\Reviews\Model\Credentials;
use Productreview\Reviews\Model\Settings;

class Repository
{
    const CONFIG_BOOLEAN_TRUE  = '1';
    const CONFIG_BOOLEAN_FALSE = '0';

    const CACHE_BUCKET_INTEGRATION_STATE = 'integration_state';

    static $cache = [];

    private $productreviewHttpClient;
    private $scopeConfig;
    private $urlGenerator;

    public function __construct(
        ProductreviewHttpClient $productreviewHttpClient,
        ScopeConfigInterface $scopeConfig,
        UrlGenerator $urlGenerator
    ) {
        $this->productreviewHttpClient = $productreviewHttpClient;
        $this->scopeConfig             = $scopeConfig;
        $this->urlGenerator            = $urlGenerator;
    }

    public function isModuleEnabled($storeId = null)
    {
        return $this->getConfigValue($storeId, 'productreview/state/is_enabled') === self::CONFIG_BOOLEAN_TRUE;
    }

    public function isModuleActive($storeId = null)
    {
        if (!$this->isModuleEnabled($storeId)) return false;
        if ($this->findCredentials($storeId) === null) return false;

        return true;
    }

    public function findCredentials($storeId = null)
    {
        if (!$this->areConfigValuesSet($storeId, [
            'productreview/credentials/catalog_id',
            'productreview/credentials/external_catalog_id',
            'productreview/credentials/secret_key'
        ])) {
            return null;
        }

        return new Credentials(
            $this->getConfigValue($storeId, 'productreview/credentials/catalog_id'),
            $this->getConfigValue($storeId, 'productreview/credentials/external_catalog_id'),
            $this->getConfigValue($storeId, 'productreview/credentials/secret_key')
        );
    }

    public function getSettings($storeId = null)
    {
        if (!$this->areConfigValuesSet($storeId, [
            'productreview/settings/main_loader_script',
            'productreview/settings/product_page_comprehensive_widget',
            'productreview/settings/product_page_inline_rating',
            'productreview/settings/category_page_inline_rating',
        ])) {
            return Settings::currentDefault();
        }

        return new Settings(
            $this->getConfigValue($storeId, 'productreview/settings/logging'),
            $this->getConfigValue($storeId, 'productreview/settings/native_review_system'),
            $this->getConfigValue($storeId, 'productreview/settings/main_loader_script'),
            $this->getConfigValue($storeId, 'productreview/settings/product_page_comprehensive_widget'),
            $this->getConfigValue($storeId, 'productreview/settings/product_page_inline_rating'),
            $this->getConfigValue($storeId, 'productreview/settings/category_page_inline_rating')
        );
    }

    public function getIntegrationState($storeId = null)
    {
        $credentials = $this->findCredentials($storeId);

        return static::heavy(
            static::computeCacheId($storeId, self::CACHE_BUCKET_INTEGRATION_STATE, $credentials ? $credentials->computeHash() : 'empty'),
            function () use ($credentials){
                return $this->productreviewHttpClient->getIntegrationState($credentials);
            }
        );
    }

    private function getConfigValue($storeId, $xmlPath)
    {
        return $this->scopeConfig->getValue($xmlPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    private function areConfigValuesSet($storeId, array $xmlPaths = [])
    {
        foreach ($xmlPaths as $xmlPath) {
            if (!$this->isConfigValueSet($storeId, $xmlPath)) return false;
        }

        return true;
    }

    private function isConfigValueSet($storeId, $xmlPath)
    {
        return $this->scopeConfig->isSetFlag($xmlPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    static private function computeCacheId($storeId, $bucket, $hash)
    {
        return $storeId . ':' . $bucket . ':' . $hash;
    }

    static private function isCached($cacheId)
    {
        return isset(static::$cache[$cacheId]);
    }

    static private function findFromCache($cacheId)
    {
        if (!static::isCached($cacheId)) return null;

        return static::$cache[$cacheId];
    }

    static private function cache($cacheId, $value)
    {
        static::$cache[$cacheId] = $value;
    }

    static private function heavy($cacheId, callable $do)
    {
        if (!static::isCached($cacheId)) {

            static::cache($cacheId, $do());
        }

        return static::findFromCache($cacheId);
    }
}
