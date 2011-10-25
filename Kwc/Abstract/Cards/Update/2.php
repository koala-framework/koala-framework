<?php
class Kwc_Abstract_Cards_Update_2 extends Kwf_Update
{
    public function update()
    {
        if (Kwf_Registry::get('db')->fetchAll('SHOW TABLES LIKE "kwc_basic_linktag"')) {
            foreach (Kwf_Registry::get('db')->fetchAll('SELECT component_id, component FROM kwc_basic_linktag') as $row) {
                $sql = "REPLACE INTO kwc_basic_cards SET component_id='{$row['component_id']}', component='{$row['component']}'";
                Kwf_Registry::get('db')->query($sql);
                $sql = "DELETE FROM kwc_basic_linktag WHERE component_id='{$row['component_id']}'";
                Kwf_Registry::get('db')->query($sql);
            }
            Kwf_Registry::get('db')->query('DROP TABLE kwc_basic_linktag');
        }
        foreach (Kwf_Registry::get('db')->fetchAll('SELECT component_id, component FROM kwc_basic_cards') as $row) {
            $config = array(
                'search' => $row['component_id'] . '-link',
                'replace' => $row['component_id'] . '-child',
                'pattern' => $row['component_id'] . '-link%',
            );
            $this->_actions[] = new Kwf_Update_Action_Component_ConvertComponentIds($config);
        }
        parent::preUpdate();
    }
}