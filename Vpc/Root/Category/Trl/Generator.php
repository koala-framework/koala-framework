<?php
class Vpc_Root_Category_Trl_Generator extends Vpc_Chained_Trl_Generator
{
    public function getPagesControllerConfig($component)
    {
        $ret = parent::getPagesControllerConfig($component);

        foreach ($ret['actions'] as &$a) $a = false;
        $ret['actions']['properties'] = true;
        $ret['actions']['visible'] = true;

        return $ret;
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $model = Vps_Model_Abstract::getInstance('Vpc_Root_Category_Trl_GeneratorModel');
        $dbRow = $model->getRow($ret['componentId']);
        if (!$dbRow) {
            // Row anlegen, sobald angezeigt wird, damit visibility immer bei
            // alles rows unabhängig von Chained Row ist
            // TODO: nicht mehr verwendete Rows löschen
            $data = array(
                'component_id' => $ret['componentId'],
                'name' => $row->getRow()->name,
                'filename' => $row->getRow()->filename,
                'visible' => $row->getRow()->visible,
                'tags' => '',
                'custom_filename' => $row->getRow()->custom_filename
            );
            $model->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data));
            $dbRow = $model->getRow($ret['componentId']);
        }
        $ret['row'] = $dbRow;
        $ret['name'] = $dbRow->name;
        $ret['filename'] = $dbRow->filename;
        $ret['visible'] = $dbRow->visible;
        $ret['isHome'] = $row->getRow()->is_home;
        return $ret;
    }
}
