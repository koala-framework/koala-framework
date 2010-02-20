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

    public function getChildData($parentData, $select = array())
    {
        $filename = null;
        $limit = null;
        $ignoreVisible = $select->hasPart(Vps_Component_Select::IGNORE_VISIBLE) ?
            $select->getPart(Vps_Component_Select::IGNORE_VISIBLE) : false;
            static $showInvisible;
        if (is_null($showInvisible)) {
            $showInvisible = Vps_Registry::get('config')->showInvisible;
        }
        if ($showInvisible) $ignoreVisible = true;

        // Nach Filename selbst suchen, da ja andere Sprache
        if ($select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
            $filename = $select->getPart(Vps_Component_Select::WHERE_FILENAME);
            $select->unsetPart(Vps_Component_Select::WHERE_FILENAME);
            if ($select->hasPart(Vps_Component_Select::LIMIT_COUNT)) {
                $limit = $select->getPart(Vps_Component_Select::LIMIT_COUNT);
                $select->unsetPart(Vps_Component_Select::LIMIT_COUNT);
            }
        }
        $select->ignoreVisible();

        $ret = array();
        foreach (parent::getChildData($parentData, $select) as $key => $c) {
            if (($ignoreVisible || $c->visible) &&
                (!$filename || $c->filename == $filename)
            ){
                $ret[$key] = $c;
            }
            if ($limit && count($ret) == $limit) return $ret;
        }
        return $ret;
    }

    protected function _formatConfig($parentData, $row)
    {
        $parentData = $this->_getParentData($row); // Muss hier jedes Mal gemacht werden, weil es auf unterschiedliche Weise im PageGenerator erstellt wird

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
            $model->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data)); // import, weil sonst FilenameFilter rekursiv reinläuft TODO: geht wahrscheinlich besser
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
