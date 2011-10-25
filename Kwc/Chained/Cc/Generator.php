<?php
class Kwc_Chained_Cc_Generator extends Kwc_Chained_Abstract_Generator
{
    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['chainedType'] = 'Cc';
        return $ret;
    }
}
