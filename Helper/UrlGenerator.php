<?php

namespace Productreview\Reviews\Helper;

use \Exception;

final class UrlGenerator
{
    const AUSTRALIAN_CATALOG = 'au';

	private $baseAppUrl;
	private $baseApiUrl;

	const PR_API_INTEGRATION_MAGENTO2_INTEGRATION_STATE = 'pr_api.integrations.magento2.integration_state';
	const PR_API_INTEGRATION_MAGENTO2_NOTIFY_ORDER      = 'pr_api.integrations.magento2.notify_order';

    const PR_APP_BM_EXTERNAL_CATALOG_CONTACT                       = 'pr_app.bm.external_catalog.request_review_import';
    const PR_APP_BM_EXTERNAL_CATALOG_AUTOMATIC_CAMPAIGN_INVITATION = 'pr_app.bm.external_catalog.automatic_campaign_invitation';
    const PR_APP_BM_EXTERNAL_CATALOG_INTEGRATION_MAGENTO2          = 'pr_app.bm.external_catalog.integrations.magento2';
    const PR_APP_BM_EXTERNAL_CATALOG_INTEGRATION_MAGENTO2_INSTALL  = 'pr_app.bm.external_catalog.integrations.magento2.install';
    const PR_APP_BM_EXTERNAL_CATALOG_WIDGETS                       = 'pr_app.bm.external_catalog.widgets';
    const PR_APP_BM_EXTERNAL_CATALOG_WIDGET_DETAILS                = 'pr_app.bm.external_catalog.widgets.details';

	const EXTERNAL_GITHUB_REPOSITORY = 'external.github_repository';

	const ROUTE_NAME_TO_PATH_MAP = [
		self::PR_API_INTEGRATION_MAGENTO2_INTEGRATION_STATE => '/api/integrations/bmp-integrations/magento2/app-state',
		self::PR_API_INTEGRATION_MAGENTO2_NOTIFY_ORDER      => '/api/integrations/bmp-integrations/magento2/order',

		self::PR_APP_BM_EXTERNAL_CATALOG_CONTACT                       => '/bm/help/contact',
		self::PR_APP_BM_EXTERNAL_CATALOG_AUTOMATIC_CAMPAIGN_INVITATION => '/bm/au/{brandSlug}/{externalCatalogSlug}/marketing/automatic-invitations',
		self::PR_APP_BM_EXTERNAL_CATALOG_INTEGRATION_MAGENTO2          => '/bm/au/{brandSlug}/{externalCatalogSlug}/integrations/magento2',
		self::PR_APP_BM_EXTERNAL_CATALOG_INTEGRATION_MAGENTO2_INSTALL  => '/integrations/magento2/install',
		self::PR_APP_BM_EXTERNAL_CATALOG_WIDGETS                       => '/bm/au/{brandSlug}/{externalCatalogSlug}/marketing-tools/widgets',
		self::PR_APP_BM_EXTERNAL_CATALOG_WIDGET_DETAILS                => '/bm/au/{brandSlug}/{externalCatalogSlug}/marketing-tools/widgets/{widgetSlug}',

		self::EXTERNAL_GITHUB_REPOSITORY => 'https://www.github.com/productreview/productreview-for-magento2'
	];

	public function __construct(
	    $baseAppUrl = null,
        $baseApiUrl = null
    ) {
        if (!$baseAppUrl) {
            $baseAppUrl = isset($_ENV['PRODUCTREVIEW_BASE_APP_URL']) ? $_ENV['PRODUCTREVIEW_BASE_APP_URL'] : 'https://www.productreview.com.au';
        }
	    if (!$baseApiUrl) {
            $baseApiUrl = isset($_ENV['PRODUCTREVIEW_BASE_API_URL']) ? $_ENV['PRODUCTREVIEW_BASE_API_URL'] : 'https://api.productreview.com.au';
        }

		$this->baseAppUrl = $baseAppUrl;
		$this->baseApiUrl = $baseApiUrl;
	}

	public function generate($routeName, array $parameters = [], array $context = [])
	{
		$routeNameToPathMap = self::ROUTE_NAME_TO_PATH_MAP;

		if (!isset($routeNameToPathMap[$routeName])) {
			throw new Exception(sprintf('Route "%s" not found.', $routeName));
		}

		$routePath = $routeNameToPathMap[$routeName];

		$requiredAttributes = $this->resolveRequiredAttributesFromRoutePath($routePath);

		$attributes = array_intersect_key($parameters, array_flip($requiredAttributes));

		$queryParameters = $this->resolveUnusedParameters($parameters, $attributes);

		return $this->resolveBaseUrl($routeName, $context) . $this->replacePlaceholders($routePath, $attributes) . $this->buildQueryParametersString($queryParameters);
	}

	private function resolveRequiredAttributesFromRoutePath($routePath)
	{
		preg_match_all('~{(.+?)}~', $routePath, $matches);

		return $matches[1];
	}

	private function replacePlaceholders($routePath, array $parameters)
	{
		$path = $routePath;

		foreach ($parameters as $parameterName => $parameterValue) {
			$path = str_replace('{' . $parameterName . '}', $parameterValue, $path);
		}

		$missingRouteAttributes = $this->resolveRequiredAttributesFromRoutePath($path);

		if (count($missingRouteAttributes) > 0) {
			throw new Exception(sprintf('Route attributes missing %s.', json_encode($missingRouteAttributes)));
		}

		return $path;
	}

	private function resolveUnusedParameters(array $parameters, array $requiredAttributes)
	{
		foreach ($requiredAttributes as $key => $value) {
			unset($parameters[$key]);
		}

		return $parameters;
	}

	private function resolveBaseUrl($routeName, array $context)
	{
		$prefix = explode('.', $routeName)[0];

		$catalogId = $this->resolveCatalogIdFromContext($context);

		if (!in_array($catalogId, ['au'])) {
			throw new Exception('Impossible to generate url - catalog unknown.');
		}

		switch ($prefix) {
			case 'pr_app':
				return $this->baseAppUrl;
			case 'pr_api':
				return $this->baseApiUrl;
			case 'external':
				return '';
		}

		throw new Exception('Invalid route.');
	}

	private function buildQueryParametersString(array $queryParameters)
	{
		if (empty($queryParameters)) {
			return '';
		}

		return '?' . http_build_query($queryParameters);
	}

	private function resolveCatalogIdFromContext(array $context)
	{
		return isset($context['catalogId']) ? $context['catalogId'] : self::AUSTRALIAN_CATALOG;
	}
}
