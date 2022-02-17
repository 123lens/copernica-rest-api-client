<?php

namespace Budgetlens\CopernicaRestApi\Support;

class Math
{

    public static function compare($value1, $value2, $comparison)
    {
        switch ($comparison) {
            case "=":
                return $value1 == $value2;
            case "==":
                return $value1 === $value2;
            case "!=":
                return $value1 != $value2;
            case ">=":
                return $value1 >= $value2;
            case "<=":
                return $value1 <= $value2;
            case ">":
                return $value1 >  $value2;
            case "<":
                return $value1 <  $value2;
            default:
                return true;
        }
    }
}
