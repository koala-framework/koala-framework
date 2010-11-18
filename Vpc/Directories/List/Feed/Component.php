<?php
class Vpc_Directories_List_Feed_Component extends Vpc_Abstract_Feed_Component
{
    protected function _getSelect()
    {
        return $this->getData()->parent->getComponent()->getSelect();
    }

    protected function _getRssEntries()
    {
        $select = $this->_getSelect();
        if (!$select) return array();
        $select->limit(10);
        if (!$select->hasPart('group')) {
            $select->group('id');
        }

        $itemDirectory = $this->getData()->parent->getComponent()->getItemDirectory();
        if (is_string($itemDirectory)) {
            $c = Vpc_Abstract::getComponentClassByParentClass($itemDirectory);
            $generator = Vps_Component_Generator_Abstract::getInstance($c, 'detail');
            $items = $generator->getChildData(null, array('select'=>$select));
            //TODO: callModifyItemData aufrufen
        } else {
            $items = $itemDirectory->getChildComponents($select);
            foreach ($items as $item) {
                $itemDirectory->getComponent()->callModifyItemData($item);
            }
        }

        $ret = array();
        foreach ($items as $item) {
            $ret[] = $this->_getRssEntryByItem($item);
        }

        return $ret;
    }

    public function getCacheVars()
    {
        $dir = $this->getData()->parent->getComponent()->getItemDirectory();
        if (is_string($dir)) {
            $c = Vpc_Abstract::getComponentClassByParentClass($dir);
            $generator = Vps_Component_Generator_Abstract::getInstance($c, 'detail');
        } else {
            $generator = $dir->getGenerator('detail');
        }
        return $generator->getCacheVars($dir instanceof Vps_Component_Data ? $dir : null);
    }

    protected function _getRssEntryByItem(Vps_Component_Data $item)
    {
        return array(
            'title' => $item->getTitle(),
            'description' => '',
            'link' => 'http://'.$_SERVER['HTTP_HOST'].$item->url
        );
    }

    protected function _getRssTitle()
    {
        return parent::_getRssTitle().' - '.trlVps('Feed');
    }
}
