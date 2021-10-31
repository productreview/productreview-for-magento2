<?php

namespace Productreview\Reviews\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Sales\Model\Order;
use Productreview\Reviews\Helper\ProductreviewHttpClient;
use Productreview\Reviews\Helper\Repository;
use \Exception;

class SalesOrderSaveAfterProductReviewObserver implements ObserverInterface
{
    const NOTIFICATION_ORDER_STATUSES = ['canceled', 'complete'];

    protected $processor;
    protected $productRepository;
    protected $productreviewHttpClient;
    protected $repository;

    public function __construct(
        DataObjectProcessor $processor,
        ProductRepositoryInterface $productRepository,
        ProductreviewHttpClient $productreviewHttpClient,
        Repository $repository
    ) {
        $this->processor               = $processor;
        $this->productRepository       = $productRepository;
        $this->productreviewHttpClient = $productreviewHttpClient;
        $this->repository              = $repository;
    }

    public function execute(Observer $observer)
    {

        if (!$this->repository->isModuleEnabled()) {
            return;
        }

        $credentials = $this->repository->findCredentials();

        if (!$credentials) {
            return;
        }

        $order = $observer->getEvent()->getData('order');

        if (!($order instanceof Order)) {
            return;
        }

        if ($order->getOrigData('status') === $order->getData('status')) {
            return;
        }

        if (!in_array($order->getData('status'), self::NOTIFICATION_ORDER_STATUSES)) {
            return;
        }

        try {
            $this->productreviewHttpClient->notifyOrder($credentials, $order);
        } catch (Exception $exception) {
            // @TODO log
        }
    }
}
