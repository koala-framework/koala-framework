<?php
class Vpc_Abstract_Cards_Update_2 extends Vps_Update
{
    public function update()
    {
        if (Vps_Registry::get('db')->fetchAll('SHOW TABLES LIKE "vpc_basic_linktag"')) {
            foreach (Vps_Registry::get('db')->fetchAll('SELECT component_id, component FROM vpc_basic_linktag') as $row) {
                $sql = "REPLACE INTO vpc_basic_cards SET component_id='{$row['component_id']}', component='{$row['component']}'";
                Vps_Registry::get('db')->query($sql);
                $sql = "DELETE FROM vpc_basic_linktag WHERE component_id='{$row['component_id']}'";
                Vps_Registry::get('db')->query($sql);
            }
            Vps_Registry::get('db')->query('DROP TABLE vpc_basic_linktag');
        }
        foreach (Vps_Registry::get('db')->fetchAll('SELECT component_id, component FROM vpc_basic_cards') as $row) {
            $config = array(
                'search' => $row['component_id'] . '-link',
                'replace' => $row['component_id'] . '-child',
                'pattern' => $row['component_id'] . '-link%',
            );
            $this->_actions[] = new Vps_Update_Action_Component_ConvertComponentIds($config);
        }
        parent::preUpdate();
    }
}