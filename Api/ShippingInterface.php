<?php

declare(strict_types=1);

namespace Adojado\WeightDelivery\Api;

/**
 * Gate-Software
 *
 * @copyright Copyright (c) 2022 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Dev Team
 * @author    adrian.biedrzycki@gate-software.com
 *
 * @package Adojado_WeightDelivery
 */
interface ShippingInterface
{
    /**
     * @var string
     * */
    const METHOD_CODE = 'weightdelivery';

    /**
     * @var string
     * */
    const FIELD_ACTIVE_KEY = 'active';

    /**
     * @var string
     * */
    const FIELD_NAME_KEY = 'name';

    /**
     * @var string
     * */
    const FIELD_PRICE_KEY = 'price';

    /**
     * @var string
     * */
    const FIELD_CHECK_WEIGHT_KEY = 'check_weight';

    /**
     * @var string
     * */
    const FIELD_MAX_WEIGHT_KEY = 'max_weight';

}
