<?php
class Kwc_Root_Category_Cc_Generator extends Kwc_Chained_Cc_Generator
{
    public function getPagesControllerConfig($component)
    {
        $ret = parent::getPagesControllerConfig($component);
        foreach ($ret['actions'] as &$a) $a = false;
        return $ret;
    }

    public function getChildData($parentData, $select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }

        if ($parentData) {
            if ($parentData->generator != $this && $parentData->componentClass != $this->getClass()) {
                return array();
            }
        }
        return parent::getChildData($parentData, $select);
    }

    protected function _getDataClass($config, $id)
    {
        if (isset($config['isHome']) && $config['isHome']) {
            return 'Kwf_Component_Data_Home';
        } else {
            return parent::_getDataClass($config, $id);
        }
    }

    protected function _getComponentIdFromRow($parentData, $row)
    {
        while ($parentData->componentClass != $this->getClass()) {
           $parentData = $parentData->parent;
        }
        return $parentData->componentId.$this->getIdSeparator().$this->_getIdFromRow($row);
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
        return $ret;
    }
}
