<?php
class Vpc_Chained_Trl_Generator extends Vps_Component_Generator_Abstract
    implements Vps_Component_Generator_PseudoPage_Interface, Vps_Component_Generator_Page_Interface
{
    protected function _getChainedChildComponents($parentData, $select)
    {
        return $parentData->chained->getChildComponents($select);
    }

    public function getChildData($parentData, $select = array())
    {
        $ret = array();
        if (is_array($select)) $select = new Vps_Component_Select($select);
        $select->whereGenerator($this->_settings['generator']);
        foreach ($this->_getChainedChildComponents($parentData, $select) as $c) {
            $data = $this->_createData($parentData, $c, $select);
            if ($data) {
                $ret[] = $data;
            }
        }
        return $ret;
    }

    protected function _getIdFromRow($row)
    {
        return substr($row->componentId, max(strrpos($row->componentId, '-'),strrpos($row->componentId, '_'))+1);
    }

    protected function _getIdWithSeparatorFromRow($row)
    {
        return substr($row->componentId, max(strrpos($row->componentId, '-'),strrpos($row->componentId, '_')));
    }

    protected function _formatConfig($parentData, $row)
    {
        $componentClass = $this->_settings['masterComponentsMap'][$row->componentClass];
        $id = $this->_getIdWithSeparatorFromRow($row);
        $data = array(
            'componentId' => $parentData->componentId.$id,
            'dbId' => $parentData->dbId.$id,
            'componentClass' => $componentClass,
            'parent' => $parentData,
            'chained' => $row,
            'isPage' => $row->isPage,
            'isPseudoPage' => $row->isPseudoPage,
        );
        if ($row->isPseudoPage) {
            $data['filename'] = $row->filename;
        }
        if ($row->isPage) {
            $data['name'] = $row->name;
        }
        return $data;
    }

    public function getChildIds($parentData, $select = array())
    {
        $ret = array();
        foreach ($parentData->getChildComponents($select) as $c) {
            $ret[] = $this->_getIdFromRow($c);
        }
        return $ret;
    }

    public function getIdSeparator()
    {
        return Vps_Component_Generator_Abstract
            ::getInstance(Vpc_Abstract::getSetting($this->_class, 'masterComponentClass'), $this->_settings['generator'])
            ->getIdSeparator();
    }
}
