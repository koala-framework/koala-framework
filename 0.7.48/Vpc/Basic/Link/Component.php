<?php
class Vpc_Basic_Link_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_Link_Model',
            'componentName' => 'Link',
            'componentIcon' => new Vps_Asset('page_white_link'),
            'childComponentClasses'   => array(
                'linkTag' => 'Vpc_Basic_LinkTag_Component',
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->_getRow()->text;
        return $return;
    }

}
