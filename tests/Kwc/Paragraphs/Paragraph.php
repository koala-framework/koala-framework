<?php
class Vpc_Paragraphs_Paragraph extends Vpc_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Paragraph';
        return $ret;
    }

    protected function _getContent()
    {
        return 'foo';
    }
}
