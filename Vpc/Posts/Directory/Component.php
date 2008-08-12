<?php
class Vpc_Posts_Directory_Component extends Vpc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Posts');
        $ret['componentIcon'] = new Vps_Asset('comments');
        $ret['tablename'] = 'Vpc_Posts_Directory_Model';

        $ret['generators']['detail']['class'] = 'Vps_Component_Generator_PseudoPage_Table';
        $ret['generators']['detail']['component'] = 'Vpc_Posts_Post_Component';
        $ret['generators']['detail']['filenameColumn'] = 'id';
        $ret['generators']['detail']['uniqueFilename'] = 'id';

        $ret['generators']['write'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Posts_Write_Component',
            'name' => trlVps('Write'),
        );
        $ret['generators']['child']['component']['view'] = 'Vpc_Posts_Directory_View_Component';
        return $ret;
    }
    
    public function getUserComponent($userId)
    {
        return null;
    }
}
