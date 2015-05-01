<?php

namespace SprykerFeature\Zed\Discount\Business\Collector;

use SprykerFeature\Shared\Discount\Dependency\Transfer\DiscountableContainerInterface;

class Item implements CollectorInterface
{
    /**
     * @param DiscountableContainerInterface $container
     * @return array
     */
    public function collect(DiscountableContainerInterface $container)
    {
        $discountableItems = [];
        $items = $container->getItems();

        foreach ($items as $item) {
            $discountableItems[] = $item;
        }

        return $discountableItems;
    }
}