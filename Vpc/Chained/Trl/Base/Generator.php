<?php
class Vpc_Chained_Trl_Base_Generator extends Vpc_Chained_Trl_Generator
{
    protected function _getChainedChildComponents($parentData, $select)
    {
        return $parentData->parent->getChildComponent('-de')->getChildComponents($select);
    }
}
