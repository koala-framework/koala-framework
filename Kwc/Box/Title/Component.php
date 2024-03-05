<?php
class Kwc_Box_Title_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Title');
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    protected function _getTitle()
    {
        $ret = $this->getData()->getTitle();
        if ($ret) $ret .= ' - ';
        $ret .= $this->_getApplicationTitle();
        return $ret;
    }

    protected function _getApplicationTitle()
    {
        return $this->getData()->getBaseProperty('application.name');
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['title'] = $this->_getTitle();
        return $ret;
    }

    public function getTitle() {
        return $this->_getTitle();
    }

    public function injectIntoRenderedHtml($html)
    {
        return self::injectTitle($html, $this->getData()->render());
    }

    //public for trl
    public static function injectTitle($html, $title)
    {
        $startPos = strpos($html, '<title>');
        $endPos = strpos($html, '</title>')+8;
        $html = substr($html, 0, $startPos)
                .$title
                .substr($html, $endPos);
        return $html;
    }
}
