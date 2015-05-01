<?php

namespace SprykerFeature\Shared\Customer\Code;

interface Messages
{
    const CUSTOMER_ALREADY_AUTHENTICATED = "customer.already.authenticated";
    const CUSTOMER_REGISTRATION_SUCCESS = "customer.registration.success";
    const CUSTOMER_REGISTRATION_CONFIRMED = "customer.registration.confirmed";
    const CUSTOMER_REGISTRATION_TIMEOUT = "customer.registration.timeout";
    const CUSTOMER_ADDRESS_UNKNOWN = "customer.address.unknown";
    const CUSTOMER_ADDRESS_UPDATED = "customer.address.updated";
    const CUSTOMER_ADDRESS_NOT_ADDED = "customer.address.not.added";
    const CUSTOMER_ADDRESS_ADDED = "customer.address.added";
    const CUSTOMER_ADDRESS_DELETE_SUCCESS = "customer.address.delete.success";
    const CUSTOMER_ADDRESS_DELETE_FAILED = "customer.address.delete.failed";
    const CUSTOMER_PASSWORD_RECOVERY_MAIL_SENT = "customer.password.recovery.mail.sent";
    const CUSTOMER_DELETE_FAILED = "customer.delete.failed";
}