<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Refund\Dependency\Plugin;

use Generated\Shared\Refund\PaymentDataInterface;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentDataTransfer;

interface PaymentDataPluginInterface
{

    /**
     * @param int $idOrder
     *
     * @return PaymentDataInterface
     */
    public function getPaymentData($idOrder);

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundPossible(OrderTransfer $orderTransfer);

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isPaymentDataRequired(OrderTransfer $orderTransfer);

    /**
     * @param PaymentDataTransfer $paymentData
     * @param int $idOrder
     *
     * @return void
     */
    public function updatePaymentDetail(PaymentDataTransfer $paymentData, $idOrder);

}