<?php

declare(strict_types=1);

namespace Adojado\WeightDelivery\Model\Carrier;

use Adojado\WeightDelivery\Api\ShippingInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Gate-Software
 *
 * @copyright Copyright (c) 2022 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Dev Team
 * @author    adrian.biedrzycki@gate-software.com
 *
 * @package AdojadoWeightDelivery
 */
class Shipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = ShippingInterface::METHOD_CODE;

    /**
     * @var ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $rateMethodFactory;

    /**
     * Shipping constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
    }

    /**
     * getAllowedMethods method
     *
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return false|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag(ShippingInterface::FIELD_ACTIVE_KEY) || !$this->isAllowedWeight($request)) {
            return false;
        }

        /** @var Result $result */
        $result = $this->rateResultFactory->create();

        /** @var Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier(ShippingInterface::METHOD_CODE);
        $method->setCarrierTitle($this->getConfigData(ShippingInterface::FIELD_NAME_KEY));

        $method->setMethod(ShippingInterface::METHOD_CODE);
        $method->setMethodTitle($this->getConfigData(ShippingInterface::FIELD_NAME_KEY));

        $amount = $this->getConfigData(ShippingInterface::FIELD_PRICE_KEY);
        $shippingPrice = $this->getFinalPriceWithHandlingFee($amount);
        $method->setPrice($shippingPrice);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }

    /**
     * isAllowedWeight method
     *
     * @param RateRequest $request
     * @return bool
     */
    private function isAllowedWeight(RateRequest $request): bool
    {
        if (!$this->getConfigData(ShippingInterface::FIELD_CHECK_WEIGHT_KEY)) {
            return true;
        }

        $items = $request->getAllItems();
        $weight = 0;

        foreach ($items as $item) {
            $weight += ($item->getWeight() * $item->getQty());

            if ($weight > $this->getConfigData(ShippingInterface::FIELD_MAX_WEIGHT_KEY)) {
                return false;
            }
        }

        return true;
    }
}
