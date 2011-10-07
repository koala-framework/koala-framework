<?php
class Kwc_Basic_Line_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlKwf('Line')
        ));
    }
}
