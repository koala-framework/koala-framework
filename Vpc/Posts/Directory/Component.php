<?php
class Vpc_Posts_Directory_Component extends Vpc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Posts');
        $ret['componentIcon'] = new Vps_Asset('comments');
        $ret['childModel'] = 'Vpc_Posts_Directory_Model';

        $ret['generators']['detail']['class'] = 'Vps_Component_Generator_PseudoPage_Table';
        $ret['generators']['detail']['component'] = 'Vpc_Posts_Detail_Component';
        $ret['generators']['detail']['filenameColumn'] = 'id';
        $ret['generators']['detail']['uniqueFilename'] = 'id';

        $ret['generators']['write'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Write_Component',
            'name' => trlVpsStatic('Write'),
        );
        $ret['generators']['child']['component']['view'] = 'Vpc_Posts_Directory_View_Component';
        $ret['placeholder']['writeText'] = null;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['write'] = $this->getData()->getChildComponent('_write');
        return $ret;
    }
    public function hasContent()
    {
        //der write-link ist ja immer da
        return true;
    }
}
