<?php

namespace Productreview\Reviews\Model;

use \Exception;

final class Credentials
{
	const CURRENT_VERSION = 1;

	const CATALOG_ID          = 'catalogId';
	const EXTERNAL_CATALOG_ID = 'externalCatalogId';
	const SECRET_KEY          = 'secretKey';

	private $catalogId;
	private $externalCatalogId;
	private $secretKey;

	public function __construct($catalogId, $externalCatalogId, $secretKey)
	{
		$this->ensureNotEmptyString($catalogId, self::CATALOG_ID);
		$this->ensureNotEmptyString($externalCatalogId, self::EXTERNAL_CATALOG_ID);
		$this->ensureNotEmptyString($secretKey, self::SECRET_KEY);

		$this->catalogId         = $catalogId;
		$this->externalCatalogId = $externalCatalogId;
		$this->secretKey         = $secretKey;
	}

	static public function fromArray(array $data)
	{
		return new self(
			$data[self::CATALOG_ID],
			$data[self::EXTERNAL_CATALOG_ID],
			$data[self::SECRET_KEY]
		);
	}

	public function toArray()
	{
		return [
            self::CATALOG_ID          => $this->catalogId,
            self::EXTERNAL_CATALOG_ID => $this->externalCatalogId,
            self::SECRET_KEY          => $this->secretKey,
        ];
	}

	private function ensureNotEmptyString($value, $name)
	{
		if (!is_string($value) || empty($value)) {
			throw new Exception(sprintf('Invalid value "%s" for "%s".', $value, $name));
		}
	}

	public function getCatalogId()
	{
		return $this->catalogId;
	}

	public function getExternalCatalogId()
	{
		return $this->externalCatalogId;
	}

	public function getSecretKey()
	{
		return $this->secretKey;
	}

	public function computeHash()
    {
        return sprintf('%s:%s:%s', $this->catalogId, $this->externalCatalogId, $this->secretKey);
    }
}
