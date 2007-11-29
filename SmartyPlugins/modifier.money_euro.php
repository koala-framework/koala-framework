<?php

function smarty_modifier_money_euro($amount)
{
    $ret = number_format($amount, 2, ",", ".");
    $ret .= ' €';
    return $ret;
}
