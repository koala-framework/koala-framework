<?php
class Vpc_Chained_Cc_Generator extends Vpc_Chained_Abstract_Generator
{
    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['chainedType'] = 'Cc';
        return $ret;
    }
}
