<?php
/**
 * @package Form
 */
class Kwf_Form_Container extends Kwf_Form_Container_Abstract
{
    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        if (!isset($ret['border'])) $ret['border'] = false;
        return $ret;
    }
}
