<?php
class Vpc_Root_Category_Cc_Generator extends Vpc_Chained_Cc_Generator
{
    public function getPagesControllerConfig($component)
    {
        $ret = parent::getPagesControllerConfig($component);
        foreach ($ret['actions'] as &$a) $a = false;
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
        return $ret;
    }
}
