<?php

namespace Unit\SprykerFeature\Zed\Discount\Business\Calculator;

use Codeception\TestCase\Test;
use SprykerFeature\Zed\Discount\Business\Calculator\Fixed;
use SprykerEngine\Zed\Kernel\Locator;
use SprykerFeature\Shared\Sales\Transfer\OrderItem;

/**
 * Class FixedTest
 * @group DiscountCalculatorFixedTest
 * @group Discount
 * @package Unit\SprykerFeature\Zed\Discount\Business\Calculator
 */
class FixedTest extends \PHPUnit_Framework_TestCase
{
    const ITEM_GROSS_PRICE_1000 = 1000;
    const DISCOUNT_AMOUNT_FIXED_100 = 100;
    const DISCOUNT_AMOUNT_FIXED_MINUS_100 = -100;

    public function testCalculateFixedShouldReturnTheGivenAmount()
    {
        $items = $this->getItems(
            [
                self::ITEM_GROSS_PRICE_1000,
                self::ITEM_GROSS_PRICE_1000,
                self::ITEM_GROSS_PRICE_1000,
            ]
        );

        $calculator = new Fixed();
        $discountAmount = $calculator->calculate($items, self::DISCOUNT_AMOUNT_FIXED_100);

        $this->assertEquals(self::DISCOUNT_AMOUNT_FIXED_100, $discountAmount);
    }

    public function testCalculateFixedShouldReturnNullForGivenNegativeAmounts()
    {
        $items = $this->getItems(
            [
                self::ITEM_GROSS_PRICE_1000,
                self::ITEM_GROSS_PRICE_1000,
                self::ITEM_GROSS_PRICE_1000,
            ]
        );

        $calculator = new Fixed();
        $discountAmount = $calculator->calculate($items, -1 * self::DISCOUNT_AMOUNT_FIXED_100);

        $this->assertEquals(0, $discountAmount);
    }

    /**
     * @param array $grossPrices
     * @return OrderItem[]
     */
    protected function getItems(array $grossPrices)
    {
        $items = [];

        foreach ($grossPrices as $grossPrice) {
            $item = new \Generated\Shared\Transfer\SalesOrderItemTransfer();
            $item->setGrossPrice($grossPrice);
            $items[] = $item;
        }

        return $items;
    }
}