<?php
class Kwc_Paragraphs_Paragraph extends Kwc_Component
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
