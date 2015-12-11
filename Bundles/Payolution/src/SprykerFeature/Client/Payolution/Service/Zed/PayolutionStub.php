<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Client\Payolution\Service\Zed;

use Generated\Shared\Transfer\CheckoutRequestTransfer;
use Generated\Shared\Transfer\PayolutionCalculationResponseTransfer;
use SprykerFeature\Client\ZedRequest\Service\ZedRequestClient;

class PayolutionStub implements PayolutionStubInterface
{

    /**
     * @var ZedRequestClient
     */
    protected $zedRequestClient;

    /**
     * @param ZedRequestClient $zedRequestClient
     */
    public function __construct(ZedRequestClient $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param CheckoutRequestTransfer $checkoutRequestTransfer
     *
     * @return PayolutionCalculationResponseTransfer
     */
    public function calculateInstallmentPayments(CheckoutRequestTransfer $checkoutRequestTransfer)
    {
        return $this->zedRequestClient->call('/payolution/gateway/calculate-installment-payments', $checkoutRequestTransfer);
    }

}