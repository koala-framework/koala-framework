<?php
class Vps_Model_MirrorCacheSimple_Row extends Vps_Model_Proxy_Row
{
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $pk = $this->getModel()->getPrimaryKey();
        $sourceRow = $this->getModel()->getSourceModel()->getRow($this->$pk);
        foreach ($this->getProxiedRow()->toArray() as $k=>$i) {
            if ($sourceRow->getModel()->hasColumn($k)) {
                $sourceRow->$k = $i;
            }
        }
        $sourceRow->save();

        //daten von sourceRow übernehmen wie zB auto_increment
        foreach ($sourceRow->toArray() as $k=>$i) {
            $this->getProxiedRow()->$k = $i;
        }
    }

    public function _beforeDelete()
    {
        parent::_beforeDelete();
        $pk = $this->getModel()->getPrimaryKey();
        $sourceRow = $this->getModel()->getSourceModel()->getRow($this->$pk);
        $sourceRow->delete();
    }
}
