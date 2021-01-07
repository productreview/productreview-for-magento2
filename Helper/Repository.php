<?php

namespace Productreview\Reviews\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Productreview\Reviews\Model\Credentials;
use Productreview\Reviews\Model\Settings;

final class Repository
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

    public function isModuleEnabled()
    {
        return $this->getConfigValue('productreview/state/is_enabled') === self::CONFIG_BOOLEAN_TRUE;
    }

    public function isModuleActive()
    {
        if (!$this->isModuleEnabled()) {
            return false;
        }

        if ($this->findCredentials() === null) {
            return false;
        }

        return true;
    }

    public function findCredentials()
    {
        if (!$this->areConfigValuesSet([
            'productreview/credentials/catalog_id',
            'productreview/credentials/external_catalog_id',
            'productreview/credentials/secret_key'
        ])) {
            return null;
        }

        return new Credentials(
            $this->getConfigValue('productreview/credentials/catalog_id'),
            $this->getConfigValue('productreview/credentials/external_catalog_id'),
            $this->getConfigValue('productreview/credentials/secret_key')
        );
    }

    public function getSettings()
    {
        if (!$this->areConfigValuesSet([
            'productreview/settings/main_loader_script',
            'productreview/settings/product_page_comprehensive_widget',
            'productreview/settings/product_page_inline_rating',
            'productreview/settings/category_page_inline_rating',
        ])) {
            return Settings::currentDefault();
        }

        return new Settings(
            $this->getConfigValue('productreview/settings/logging'),
            $this->getConfigValue('productreview/settings/native_review_system'),
            $this->getConfigValue('productreview/settings/main_loader_script'),
            $this->getConfigValue('productreview/settings/product_page_comprehensive_widget'),
            $this->getConfigValue('productreview/settings/product_page_inline_rating'),
            $this->getConfigValue('productreview/settings/category_page_inline_rating')
        );
    }

    public function getIntegrationState()
    {
        $credentials = $this->findCredentials();

        $hash = $credentials ? $credentials->computeHash() : 'empty';

        return static::heavy(
            static::computeCacheId(self::CACHE_BUCKET_INTEGRATION_STATE, $hash),
            function () use ($credentials){
                return $this->productreviewHttpClient->getIntegrationState($credentials);
            }
        );
    }

    private function getConfigValue($xmlPath)
    {
        return $this->scopeConfig->getValue($xmlPath, ScopeInterface::SCOPE_STORE);
    }

    private function areConfigValuesSet(array $xmlPaths = [])
    {
        foreach ($xmlPaths as $xmlPath) {
            if (!$this->isConfigValueSet($xmlPath)) {
                return false;
            }
        }

        return true;
    }

    private function isConfigValueSet($xmlPath)
    {
        return $this->scopeConfig->isSetFlag($xmlPath, ScopeInterface::SCOPE_STORE);
    }

    static private function computeCacheId($bucket, $hash)
    {
        return $bucket . ':' . $hash;
    }

    static private function isCached($cacheId)
    {
        return isset(static::$cache[$cacheId]);
    }

    static private function findFromCache($cacheId)
    {
        if (!static::isCached($cacheId)) {
            return null;
        }

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
