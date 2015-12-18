<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Client\Customer\Session;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Session\SessionClientInterface;

class CustomerSession implements CustomerSessionInterface
{

    const SESSION_KEY = 'customer data';

    /**
     * @var SessionClientInterface
     */
    private $sessionClient;

    /**
     * @param SessionClientInterface $sessionClient
     */
    public function __construct(SessionClientInterface $sessionClient)
    {
        $this->sessionClient = $sessionClient;
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->sessionClient->remove(self::SESSION_KEY);
    }

    /**
     * @return bool
     */
    public function hasCustomer()
    {
        return $this->sessionClient->has(self::SESSION_KEY);
    }

    /**
     * @return CustomerTransfer
     */
    public function getCustomer()
    {
        return $this->sessionClient->get(self::SESSION_KEY);
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerTransfer
     */
    public function setCustomer(CustomerTransfer $customerTransfer)
    {
        $this->sessionClient->set(
            self::SESSION_KEY,
            $customerTransfer
        );

        return $customerTransfer;
    }

}