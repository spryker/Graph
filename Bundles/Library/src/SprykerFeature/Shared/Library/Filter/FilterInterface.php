<?php
namespace SprykerFeature\Shared\Library\Filter;

interface FilterInterface
{
    /**
     * @param string $string
     * @return string
     */
    public function filter($string);
}