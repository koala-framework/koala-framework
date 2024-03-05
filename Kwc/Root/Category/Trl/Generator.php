<?php
class Kwc_Root_Category_Trl_Generator extends Kwc_Chained_Trl_Generator
{
    protected $_eventsClass = 'Kwc_Root_Category_Trl_GeneratorEvents';

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
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

    protected function _getComponentIdFromRow($parentData, $row)
    {
        while ($parentData->componentClass != $this->getClass()) {
           $parentData = $parentData->parent;
        }
        return $parentData->componentId.$this->getIdSeparator().$this->_getIdFromRow($row);
    }

    public function getChildData($parentData, $select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }

        if (($id = $select->getPart(Kwf_Component_Select::WHERE_ID)) && substr($id, 0, 1) == '_') {
            $select->whereId(substr($id, 1));
        }

        if ($parentData) {
            if ($parentData->generator != $this && $parentData->componentClass != $this->getClass()) {
                return array();
            }
        }


        $filename = null;
        $limit = null;
        $ignoreVisible = $select->hasPart(Kwf_Component_Select::IGNORE_VISIBLE) ?
            $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE) : false;
        if (Kwf_Component_Data_Root::getShowInvisible()) $ignoreVisible = true;

        $select = clone $select;

        // Nach Filename selbst suchen, da ja andere Sprache
        if ($select->hasPart(Kwf_Component_Select::WHERE_FILENAME)) {
            $filename = $select->getPart(Kwf_Component_Select::WHERE_FILENAME);
            $select->unsetPart(Kwf_Component_Select::WHERE_FILENAME);
        }
        // Limit auch selbst beachten, da in slave eigenes visible gesetzt ist
        if ($select->hasPart(Kwf_Component_Select::LIMIT_COUNT)) {
            $limit = $select->getPart(Kwf_Component_Select::LIMIT_COUNT);
            $select->unsetPart(Kwf_Component_Select::LIMIT_COUNT);
        }
        $select->ignoreVisible();
        $ret = array();
        $components = parent::getChildData($parentData, $select);
        foreach ($components as $key => $c) {
            if (($ignoreVisible || $c->visible || $c->isHome) &&
                (!$filename || $c->filename == $filename)
            ){
                $ret[$key] = $c;
            }
            if ($limit && count($ret) == $limit) {
                return $ret;
            }
        }
        if ($filename) {
            $componentIds = array();
            foreach ($components as $key => $c) $componentIds[$c->dbId] = $key;
            $model = $this->getHistoryModel();
            $select = $model->select()
                ->whereEquals('component_id', array_keys($componentIds))
                ->whereEquals('filename', $filename)
                ->order('date', 'DESC');
            $rows = $model->export(Kwf_Model_Interface::FORMAT_ARRAY, $select, array('columns' => array('component_id')));
            foreach ($rows as $row) {
                $key = $componentIds[$row['component_id']];
                $ret[$key] = $components[$key];
                if ($limit && count($ret) == $limit) {
                    return $ret;
                }
            }
        }
        return $ret;
    }

    protected function _createData($parentData, $row, $select)
    {
        //needed if multiple category generators exist in the same web
        $idParent = $parentData;
        while ($idParent->componentClass != $this->_class) {
            $idParent = $idParent->parent;
            if (!$idParent) {
                return null;
            }
        }

        $ret = parent::_createData($parentData, $row, $select);
        return $ret;
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);

        //im pages generator fangen die ids immer von vorne an
        $id = $this->_getIdFromRow($row);
        if (!is_numeric($id)) throw new Kwf_Exception("Id must be numeric");
        $idParent = $parentData;
        while ($idParent->componentClass != $this->_class) {
            $idParent = $idParent->parent;
        }
        $id = $this->_getIdFromRow($row);
        $ret['componentId'] = $idParent->componentId.$this->getIdSeparator().$id;
        $ret['dbId'] = $idParent->dbId.$this->getIdSeparator().$id;

        //parent geradebiegen
        if (!$parentData || ($parentData->componentClass == $this->_class && is_numeric($ret['chained']->parent->componentId))) {
            $c = new Kwf_Component_Select();
            $c->ignoreVisible(true);
            $c->whereId('_'.$ret['chained']->parent->componentId);
            $parentData = $parentData->getChildComponent($c);
        }
        $ret['parent'] = $parentData;

        $dbRow = $this->_getRowByPrimaryId($ret['componentId'], $row);
        $ret['row'] = $dbRow;
        $ret['name'] = $dbRow->name;
        $ret['filename'] = $dbRow->filename;
        $ret['visible'] = $row->isHome ? true : $dbRow->visible;
        $ret['isHome'] = $row->isHome;
        return $ret;
    }

    public function getRowByLazyRow($lazyRow, $component)
    {
        return $this->_getRowByPrimaryId($lazyRow, $component->chained);
    }

    private function _getRowByPrimaryId($componentId, $chainedComponent)
    {
        $ret = $this->_getModel()->getRow($componentId);
        if (!$ret) {
            $ret = $this->_getModel()->createRow(array(
                'component_id' => $componentId,
                'name' => $chainedComponent->getRow()->name,
                'filename' => $chainedComponent->getRow()->filename,
                'visible' => $chainedComponent->isHome, //home ist standardmäßig immer sichtbar, andere seiten nicht
                'custom_filename' => false
            ));
        }
        return $ret;
    }

    public function getModel()
    {
        return $this->_getModel();
    }

    protected function _getDataClass($config, $id)
    {
        if (isset($config['isHome']) && $config['isHome']) {
            return 'Kwf_Component_Data_Home';
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

    public function getPagePropertiesForm($componentOrParent)
    {
        return new Kwc_Root_Category_Trl_GeneratorForm($this);
    }

    public function getHistoryModel()
    {
        return Kwf_Model_Abstract::getInstance($this->_settings['historyModel']);
    }

    public function setVisible(Kwf_Component_Data $data, $visible)
    {
        $data->row->visible = $visible;
    }

    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $row = $cmp->row;
        $ret['name'] = $row->name;
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        if (isset($data['name'])) {
            $cmp->row->name = $data['name'];
            $cmp->row->save();
        }
    }
}
