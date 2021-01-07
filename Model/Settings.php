<?php

namespace Productreview\Reviews\Model;

use \Exception;

class Settings
{
	const CURRENT_VERSION = 1;

	const LOGGING = 'logging';

	const LOGGING_EMERGENCY = 'emergency';
	const LOGGING_ALERT = 'alert';
	const LOGGING_CRITICAL = 'critical';
	const LOGGING_ERROR = 'error';
	const LOGGING_WARNING = 'warning';
	const LOGGING_NOTICE = 'notice';
	const LOGGING_INFO = 'info';
	const LOGGING_DEBUG = 'debug';
	const LOGGING_DISABLED = 'disabled';

	const LOGGING_LEVELS = [
		self::LOGGING_EMERGENCY,
		self::LOGGING_ALERT,
		self::LOGGING_CRITICAL,
		self::LOGGING_ERROR,
		self::LOGGING_WARNING,
		self::LOGGING_NOTICE,
		self::LOGGING_INFO,
		self::LOGGING_DEBUG,
		self::LOGGING_DISABLED,
	];

	const NATIVE_REVIEW_SYSTEM = 'native_review_system';
	const NATIVE_REVIEW_SYSTEM_ENABLED = 'enabled';
	const NATIVE_REVIEW_SYSTEM_DISABLED = 'disabled';

	const MAIN_LOADER_SCRIPT = 'main_loader_script';
	const MAIN_LOADER_SCRIPT_MANUAL_IMPLEMENTATION = 'manual_implementation';
	const MAIN_LOADER_SCRIPT_ENABLED = 'enabled';

	const PRODUCT_PAGE_COMPREHENSIVE_WIDGET = 'product_page_comprehensive_widget';
	const PRODUCT_PAGE_COMPREHENSIVE_WIDGET_DISABLED = 'disabled';
	const PRODUCT_PAGE_COMPREHENSIVE_WIDGET_MANUAL_IMPLEMENTATION = 'manual_implementation';
	const PRODUCT_PAGE_COMPREHENSIVE_WIDGET_TAB = 'tab';
	const PRODUCT_PAGE_COMPREHENSIVE_WIDGET_PAGE_FOOTER = 'page_footer';

	const PRODUCT_PAGE_INLINE_RATING = 'product_page_inline_rating';
	const PRODUCT_PAGE_INLINE_RATING_MANUAL_IMPLEMENTATION = 'manual_implementation';
	const PRODUCT_PAGE_INLINE_RATING_DISABLED = 'disabled';
	const PRODUCT_PAGE_INLINE_RATING_ENABLED = 'enabled';

	const CATEGORY_PAGE_INLINE_RATING = 'category_page_inline_rating';
	const CATEGORY_PAGE_INLINE_RATING_MANUAL_IMPLEMENTATION = 'manual_implementation';
	const CATEGORY_PAGE_INLINE_RATING_DISABLED = 'disabled';
	const CATEGORY_PAGE_INLINE_RATING_ENABLED = 'enabled';

	private $logging;
	private $nativeReviewSystem;
	private $mainLoaderScript;
	private $productPageComprehensiveWidget;
	private $productPageInlineRating;
	private $categoryPageInlineRating;

	public function __construct(
		$logging = self::LOGGING_DISABLED,
		$nativeReviewSystem = self::NATIVE_REVIEW_SYSTEM_DISABLED,
		$mainLoaderScript = self::MAIN_LOADER_SCRIPT_ENABLED,
		$productPageComprehensiveWidget = self::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_DISABLED,
		$productPageInlineRating = self::PRODUCT_PAGE_INLINE_RATING_DISABLED,
		$categoryPageInlineRating = self::CATEGORY_PAGE_INLINE_RATING_DISABLED
	) {
		$this->ensureLoggingValueIsValid($logging);
		$this->ensureNativeReviewSystemValueIsValid($nativeReviewSystem);
		$this->ensureMainLoaderScriptValueIsValid($mainLoaderScript);
		$this->ensureProductPageComprehensiveWidgetValueIsValid($productPageComprehensiveWidget);
		$this->ensureProductPageInlineRatingValueIsValid($productPageInlineRating);
		$this->ensureCategoryPageInlineRatingValueIsValid($categoryPageInlineRating);

		$this->logging                        = $logging;
		$this->nativeReviewSystem             = $nativeReviewSystem;
		$this->mainLoaderScript               = $mainLoaderScript;
		$this->productPageComprehensiveWidget = $productPageComprehensiveWidget;
		$this->productPageInlineRating        = $productPageInlineRating;
		$this->categoryPageInlineRating       = $categoryPageInlineRating;
	}

	static public function currentDefault()
	{
		return new self();
	}

	static public function fromArray(array $data)
	{
		return new self(
			$data[self::LOGGING],
			$data[self::NATIVE_REVIEW_SYSTEM],
			$data[self::MAIN_LOADER_SCRIPT],
			$data[self::PRODUCT_PAGE_COMPREHENSIVE_WIDGET],
			$data[self::PRODUCT_PAGE_INLINE_RATING],
			$data[self::CATEGORY_PAGE_INLINE_RATING]
		);
	}

	public function isLoggingEnabled()
	{
		return $this->logging !== self::LOGGING_DISABLED;
	}

	public function getLogging()
	{
		return $this->logging;
	}

	public function getNativeReviewSystem()
	{
		return $this->nativeReviewSystem;
	}

	public function getMainLoaderScript()
	{
		return $this->mainLoaderScript;
	}

	public function getProductPageComprehensiveWidget()
	{
		return $this->productPageComprehensiveWidget;
	}

	public function getProductPageInlineRating()
	{
		return $this->productPageInlineRating;
	}

	public function getCategoryPageInlineRating()
	{
		return $this->categoryPageInlineRating;
	}

	private function ensureLoggingValueIsValid($value)
	{
		if (!in_array($value, self::LOGGING_LEVELS)) {
			throw new Exception('Invalid logging value.');
		}
	}

	private function ensureNativeReviewSystemValueIsValid($value)
	{
		if (!in_array($value, [self::NATIVE_REVIEW_SYSTEM_ENABLED, self::NATIVE_REVIEW_SYSTEM_DISABLED])) {
			throw new Exception('Invalid native review system value.');
		}
	}

	private function ensureMainLoaderScriptValueIsValid($value)
	{
		if (!in_array(
			$value,
			[
				self::MAIN_LOADER_SCRIPT_MANUAL_IMPLEMENTATION,
				self::MAIN_LOADER_SCRIPT_ENABLED,
			]
		)) {
			throw new Exception('Invalid main loader script value.');
		}
	}

	private function ensureProductPageComprehensiveWidgetValueIsValid($value)
	{
		if (!in_array(
			$value,
			[
				self::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_DISABLED,
				self::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_MANUAL_IMPLEMENTATION,
				self::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_TAB,
				self::PRODUCT_PAGE_COMPREHENSIVE_WIDGET_PAGE_FOOTER
			]
		)) {
			throw new Exception('Invalid product page comprehensive widget value.');
		}
	}

	private function ensureProductPageInlineRatingValueIsValid($value)
	{
		if (!in_array($value, [self::PRODUCT_PAGE_INLINE_RATING_MANUAL_IMPLEMENTATION, self::PRODUCT_PAGE_INLINE_RATING_DISABLED, self::PRODUCT_PAGE_INLINE_RATING_ENABLED])) {
			throw new Exception('Invalid product page inline rating value');
		}
	}

	private function ensureCategoryPageInlineRatingValueIsValid($value)
	{
		if (!in_array($value, [self::CATEGORY_PAGE_INLINE_RATING_MANUAL_IMPLEMENTATION, self::CATEGORY_PAGE_INLINE_RATING_DISABLED, self::CATEGORY_PAGE_INLINE_RATING_ENABLED])) {
			throw new Exception('Invalid category page inline rating value');
		}
	}
}
