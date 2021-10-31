<?php

namespace Productreview\Reviews\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Productreview\Reviews\Model\Credentials;
use Productreview\Reviews\Model\ModuleDetails;
use \Exception;

/**
 * TODO: Extract and inject Order normalization logic
 */
class ProductreviewHttpClient
{
    private $processor;
	private $urlGenerator;
    private $imageHelper;
    private $configurableHelper;
    private $productRepository;
    private $productMetadata;
    private $moduleList;

	public function __construct(
        DataObjectProcessor $processor,
        Image $imageHelper,
        Configurable $configurableHelper,
        ProductRepositoryInterface $productRepository,
        UrlGenerator $urlGenerator,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList
    ) {
        $this->processor          = $processor;
        $this->imageHelper        = $imageHelper;
        $this->configurableHelper = $configurableHelper;
        $this->productRepository  = $productRepository;
        $this->productMetadata    = $productMetadata;
        $this->moduleList         = $moduleList;
        $this->urlGenerator       = $urlGenerator;
    }

    public function getIntegrationState(Credentials $credentials = null)
    {
        if (!$credentials) {
            return [
                'status' => ModuleDetails::CONNECTION_STATUS_NO_CREDENTIALS,
            ];
        }

        try {
            $response = $this->curlGet(
                $this->urlGenerator->generate(
                    UrlGenerator::PR_API_INTEGRATION_MAGENTO2_INTEGRATION_STATE,
                    array_merge($credentials->toArray(), $this->buildSoftwareVersions())
                )
            );

            if ($response['status'] !== 200) {
                throw new Exception('Response is not successful.');
            }

            return $this->unserializeFromJson($response['body']);

        } catch (Exception $exception) {
            return [
                'status' => ModuleDetails::CONNECTION_STATUS_FAIL,
            ];
        }
    }

    public function notifyOrder(Credentials $credentials, Order $order)
	{
        $this->curlPost(
            $this->urlGenerator->generate(
                UrlGenerator::PR_API_INTEGRATION_MAGENTO2_NOTIFY_ORDER,
                array_merge($credentials->toArray(), $this->buildSoftwareVersions())
            ),
            [
                'order' => $this->normalizeOrder($order),
            ]
        );
	}

	public function notifyManyOrders(Credentials $credentials, array $orders)
	{
        $this->curlPost(
            $this->urlGenerator->generate(
                UrlGenerator::PR_API_INTEGRATION_MAGENTO2_NOTIFY_ORDER,
                array_merge($credentials->toArray(), $this->buildSoftwareVersions())
            ),
            [
                'orders' => array_map(
                    function ($order) {
                        return $this->normalizeOrder($order);
                    },
                    $orders
                )
            ]
        );
	}

	private function buildSoftwareVersions()
    {
        return [
            'magentoVersion' => $this->getMagentoVersion(),
            'pluginVersion'  => $this->getPluginVersion(),
            'phpVersion'     => $this->getPhpVersion(),
        ];
    }

    public function getPhpVersion()
    {
        return PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;
    }

    public function getMagentoVersion()
    {
        return $this->productMetadata->getName() . '_' . $this->productMetadata->getEdition() . '_' . $this->productMetadata->getVersion();
    }

    public function getPluginVersion()
    {
        $pluginVersion = $this->moduleList->getOne(ModuleDetails::MODULE_NAME)['setup_version'];

        return $pluginVersion ? $pluginVersion : 'unknown';
    }

    private function normalizeOrder(Order $order)
    {
        $products = [];

        foreach ($order->getItems() as $item) {
            $products[] = $this->productRepository->getById($item['product_id'], false, $item['store_id']);

            $parentProductIds = $this->configurableHelper->getParentIdsByChild($item['product_id']);

            foreach ($parentProductIds as $parentProductId) {
                $products[] = $this->productRepository->getById($parentProductId, false, $item['store_id']);
            }
        }

        $orderDTO = $this->processor->buildOutputDataArray($order, OrderInterface::class);

        $orderDTO['__products'] = array_map(
            function (ProductInterface $product) {
                $productData = $this->processor->buildOutputDataArray($product, ProductInterface::class);

                $productData['__extra_data'] = [
                    'configurable_parent_ids' =>  $this->configurableHelper->getParentIdsByChild($product->getId()),

                    'category_collection' => $product->getCategoryCollection(),
                    'brand'               => $product->getBrand() ? $product->getAttributeText('brand') : null,

                    'product_url'  => $product->getProductUrl(),
                    'image_url'    => $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl(),

                    'external_sku' => $product->getSku(),
                    'upc'          => $product->getUpc(),
                    'isbn'         => $product->getIsbn(),
                    'mpn'          => $product->getMpn(),
                ];

                return $productData;
            },
            $products
        );

        return $orderDTO;
    }

    private function curlPost($uri, $data)
    {
        $serializedData = $this->serializeAsJson($data);

        $curl = new Curl();

        $curl->addHeader('Content-Type', 'application/json');
        $curl->addHeader('Content-Length', strlen($serializedData));

        $curl->post($uri, $serializedData);
    }


    private function curlGet($uri)
    {
        $curl = new Curl();

        $curl->addHeader('Content-Type', 'application/json');

        $curl->get($uri);

        return [
            'status'  => $curl->getStatus(),
            'headers' => $curl->getHeaders(),
            'body'    => $curl->getBody(),
        ];
    }

    private function serializeAsJson(array $data)
    {
        return (new Json())->serialize($data);
    }

    private function unserializeFromJson($string)
    {
        return (new Json())->unserialize($string);
    }
}
