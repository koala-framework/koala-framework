<?php
class Kwc_Box_TitleEditable_Trl_Component extends Kwc_Box_Title_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    protected function _getTitle()
    {
        if (trim($this->_getRow()->title)) return $this->_getRow()->title;

        if ($ret = Kwc_Box_TitleEditable_Component::getTitleFromNextSubroot($this->getData())) return $ret;

        $ret = $this->getData()->getTitle();
        if ($ret) $ret .= ' - ';
        $ret .= $this->_getApplicationTitle();
        return $ret;
    }

    protected function _getApplicationTitle()
    {
        return Kwf_Config::getValue('application.name');
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['title'] = $this->_getTitle();
        return $ret;
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
