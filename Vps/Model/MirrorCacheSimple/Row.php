<?php
class Vps_Model_MirrorCacheSimple_Row extends Vps_Model_Proxy_Row
{
    protected function _afterSave()
    {
        parent::_afterSave();
        $pk = $this->getModel()->getPrimaryKey();
        $sourceRow = $this->getModel()->getSourceModel()->getRow($this->$pk);
        foreach ($this->getProxiedRow()->toArray() as $k=>$i) {
            $sourceRow->$k = $i;
        }
        $sourceRow->save();
    }

    public function _beforeDelete()
    {
        parent::_beforeDelete();
        $pk = $this->getModel()->getPrimaryKey();
        $sourceRow = $this->getModel()->getSourceModel()->getRow($this->$pk);
        $sourceRow->delete();
    }
}
