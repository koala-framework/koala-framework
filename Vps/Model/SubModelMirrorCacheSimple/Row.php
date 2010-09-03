<?php
class Vps_Model_SubModelMirrorCacheSimple_Row extends Vps_Model_Proxy_Row
{
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $sourceModel = $this->getModel()->getSourceModel();
        $pk = $sourceModel->getPrimaryKey();
        $sourceRow = $sourceModel->getRow($this->$pk);
        if (!$sourceRow) 
        foreach ($this->getProxiedRow()->toArray() as $k=>$i) {
            if ($sourceModel->hasColumn($k)) {
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
