<?php
class Vpc_Basic_LinkTag_FirstChildPage_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'Link.FirstChildPage',
            'componentIcon' => new Vps_Asset('page_go')
        ));
        return $ret;
    }

    public function getTemplateVars()
    {
        $pc = $this->getPageCollection();
        $childPages = $pc->getMenuChildPages($this);

        $ret = parent::getTemplateVars();
        $ret['href'] = $pc->getUrl($childPages[0]);
        $ret['param'] = '';
        $ret['rel'] = '';
        return $ret;
    }
}
