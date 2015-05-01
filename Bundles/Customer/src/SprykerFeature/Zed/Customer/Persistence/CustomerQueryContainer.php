<?php

namespace SprykerFeature\Zed\Customer\Persistence;

use SprykerEngine\Zed\Kernel\Persistence\AbstractQueryContainer;
use SprykerFeature\Zed\Customer\Persistence\Propel\SpyCustomerQuery;
use SprykerFeature\Zed\Customer\Persistence\Propel\SpyCustomerAddressQuery;
use Propel\Runtime\Exception\PropelException;

/**
 * @method CustomerDependencyContainer getDependencyContainer()
 */
class CustomerQueryContainer extends AbstractQueryContainer
{
    /**
     * @param string $email
     *
     * @return SpyCustomerQuery
     */
    public function queryCustomerByEmail($email)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();
        $query->filterByEmail($email);

        return $query;
    }

    /**
     * @param int $id
     *
     * @return SpyCustomerQuery
     */
    public function queryCustomerById($id)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();
        $query->filterByIdCustomer($id);

        return $query;
    }

    /**
     * @param string $token
     *
     * @return SpyCustomerQuery
     */
    public function queryCustomerByRegistrationKey($token)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();
        $query->filterByRegistrationKey($token);

        return $query;
    }

    /**
     * @param string $token
     *
     * @return SpyCustomerQuery
     */
    public function queryCustomerByRestorePasswordKey($token)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();
        $query->filterByRestorePasswordKey($token);

        return $query;
    }

    /**
     * @param int $id_address
     * @param string $email
     *
     * @return SpyCustomerAddressQuery
     * @throws PropelException
     */
    public function queryAddressForCustomer($id_address, $email)
    {
        $customer = $this->queryCustomerByEmail($email)->findOne();

        $query = $this->getDependencyContainer()->createSpyCustomerAddressQuery();
        $query->filterByIdCustomerAddress($id_address);
        $query->filterByCustomer($customer);

        return $query;
    }

    /**
     * @param int $id_address
     *
     * @return SpyCustomerAddressQuery
     * @throws PropelException
     */
    public function queryAddress($id_address)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerAddressQuery();
        $query->filterByIdCustomerAddress($id_address);

        return $query;
    }

    /**
     * @param string $email
     *
     * @return SpyCustomerAddressQuery
     */
    public function queryAddressesForCustomer($email)
    {
        $customer = $this->queryCustomerByEmail($email)->findOne();

        $query = $this->getDependencyContainer()->createSpyCustomerAddressQuery();
        $query->filterByCustomer($customer);

        return $query;
    }

    /**
     * @return SpyCustomerAddressQuery
     */
    public function queryAddresses()
    {
        $query = $this->getDependencyContainer()->createSpyCustomerAddressQuery();

        return $query;
    }

    /**
     * @return SpyCustomerQuery
     */
    public function queryCustomers()
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();

        return $query;
    }
}