<?php
class Kwc_Advanced_GoogleMap_Update_20150309Legacy22701 extends Kwf_Update
{
    protected function _init()
    {
        $db = Kwf_Registry::get('db');
        $q = $db->query("SHOW TABLES");
        $tables = array();
        while($t = $q->fetchColumn()) {
            $tables[] = $t;
        }
        if (in_array('kwc_advanced_googlemap', $tables)) {
            $this->_actions[] = new Kwf_Update_Action_Component_ConvertTableToFieldModel(array(
                'table'=>'kwc_advanced_googlemap',
            ));
            $this->_actions[] = new Kwf_Update_Action_Db_DropTable(array(
                'table'=>'kwc_advanced_googlemap',
            ));
        }
    }
}
