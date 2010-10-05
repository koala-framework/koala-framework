<?php
class Vpc_Root_Category_Trl_Generator extends Vpc_Chained_Trl_Generator
{
    public function getPagesControllerConfig($component)
    {
        $ret = parent::getPagesControllerConfig($component);
        foreach ($ret['actions'] as &$a) $a = false;
        $ret['actions']['properties'] = true;
        $ret['actions']['visible'] = true;

        // Bei Pages muss nach oben gesucht werden, weil Klasse von Generator
        // mit Komponentklasse übereinstimmen muss
        $c = $component;
        while ($c && $c->componentClass != $this->getClass()) {
            $c = $c->parent;
        }
        if ($c) { //TODO warum tritt das auf?
            $ret['editControllerComponentId'] = $c->componentId;
        }
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

        $select = clone $select;

        // Nach Filename selbst suchen, da ja andere Sprache
        if ($select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
            $filename = $select->getPart(Vps_Component_Select::WHERE_FILENAME);
            $select->unsetPart(Vps_Component_Select::WHERE_FILENAME);
        }
        // Limit auch selbst beachten, da in slave eigenes visible gesetzt ist
        if ($select->hasPart(Vps_Component_Select::LIMIT_COUNT)) {
            $limit = $select->getPart(Vps_Component_Select::LIMIT_COUNT);
            $select->unsetPart(Vps_Component_Select::LIMIT_COUNT);
        }
        $select->ignoreVisible();
        $ret = array();
        foreach (parent::getChildData($parentData, $select) as $key => $c) {
            if (($ignoreVisible || $c->visible) &&
                (!$filename || $c->filename == $filename)
            ){
                $ret[$key] = $c;
            }
            if ($limit && count($ret) == $limit) {
                return $ret;
            }
        }
        return $ret;
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);

        //im pages generator fangen die ids immer von vorne an
        $id = $this->_getIdFromRow($row);
        if (!is_numeric($id)) throw new Vps_Exception("Id must be numeric");
        $idParent = $parentData;
        while ($idParent->componentClass != $this->_class) {
            $idParent = $idParent->parent;
        }
        $id = $this->_getIdFromRow($row);
        $ret['componentId'] = $idParent->componentId.$this->getIdSeparator().$id;
        $ret['dbId'] = $idParent->dbId.$this->getIdSeparator().$id;

        //parent geradebiegen
        if (!$parentData || ($parentData->componentClass == $this->_class && is_numeric($ret['chained']->parent->componentId))) {
            $c = new Vps_Component_Select();
            $c->ignoreVisible(true);
            $c->whereId('_'.$ret['chained']->parent->componentId);
            $parentData = $parentData->getChildComponent($c);
        }
        $ret['parent'] = $parentData;

        $dbRow = $this->_getModel()->getRow($ret['componentId']);
        if (!$dbRow) {
            $dbRow = $this->_getModel()->createRow(array(
                'component_id' => $ret['componentId'],
                'name' => $row->getRow()->name,
                'filename' => $row->getRow()->filename,
                'visible' => $row->isHome, //home ist standardmäßig immer sichtbar, andere seiten nicht
                'custom_filename' => false
            ));
        }
        $ret['row'] = $dbRow;
        $ret['name'] = $dbRow->name;
        $ret['filename'] = $dbRow->filename;
        $ret['visible'] = $row->isHome ? true : $dbRow->visible;
        $ret['isHome'] = $row->isHome;
        return $ret;
    }

    protected function _getDataClass($config, $id)
    {
        if (isset($config['isHome']) && $config['isHome']) {
            return 'Vps_Component_Data_Home';
        } else {
            return parent::_getDataClass($config, $id);
        }
    }

    public function getStaticCacheVarsForMenu()
    {
        $ret = parent::getStaticCacheVarsForMenu();
        $ret[] = array(
            'model' => $this->_getModel()
        );
        return $ret;
    }
}
