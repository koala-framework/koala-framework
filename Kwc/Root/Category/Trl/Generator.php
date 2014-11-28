<?php
class Kwc_Root_Category_Trl_Generator extends Kwc_Chained_Trl_Generator
{
    protected $_eventsClass = 'Kwc_Root_Category_Trl_GeneratorEvents';

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
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
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
        foreach (parent::getChildData($parentData, $select) as $key => $c) {
            if (($ignoreVisible || $c->visible || $c->isHome) &&
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
        $ret['selfVisible'] = $dbRow->visible;
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
}
