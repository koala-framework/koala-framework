<?php
class Kwc_Root_Category_GeneratorRow extends Kwf_Model_Tree_Row
{
    protected function _beforeInsert()
    {
        parent::_beforeInsert();
        if (!$this->is_home) $this->is_home = 0;
        if (!$this->visible) $this->visible = 0;
        if (!$this->pos) $this->pos = 1;

        //fill parent_subroot_id cache
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->parent_id, array('ignoreVisible'=>true));
        $this->parent_subroot_id = $c->getSubroot()->componentId;
    }

    protected function _beforeUpdate()
    {
        parent::_beforeUpdate();
        if ($this->is_home && !$this->visible) {
            throw new Kwf_ClientException(trlKwf('Cannot set Home Page invisible'));
        }
        if (in_array('parent_id', $this->getDirtyColumns()) && $this->getCleanValue('parent_id')) {
            $oldSubroot = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($this->getCleanValue('parent_id'), array('ignoreVisible'=>true))
                ->getSubroot();
            $newSubroot = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($this->parent_id, array('ignoreVisible'=>true))
                ->getSubroot();
            if ($oldSubroot != $newSubroot) {
                throw new Kwf_Exception_Client(trlKwf("Can't move Page to other Subroot"));
            }
        }
        if (in_array('filename', $this->getDirtyColumns())) {
            $model = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($this->id, array('ignoreVisible'=>true))
                ->generator->getHistoryModel();
            $data = array(
                'page_id' => $this->id,
                'parent_id' => $this->parent_id,
                'filename' => $this->getCleanValue('filename'),
            );
            $row = $model->createRow($data);
            $row->save();
        }
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        // Dranhängende Komponente löschen
        $data = Kwf_Component_Data_Root::getInstance()->getComponentById($this->id, array('ignoreVisible'=>true));
        Kwc_Admin::getInstance($data->componentClass)->delete($this->id);
    }

    public function getComponentsDependingOnRow()
    {
        $ret = array();

        foreach (Kwc_Admin::getDependsOnRowInstances() as $a) {
            foreach ($a->getComponentsDependingOnRow($this) as $i) {
                if ($i) {
                    $ret[] = $i;
                }
            }
        }

        //unterseiten
        foreach ($this->getChildNodes() as $c) {
            $ret = array_merge($ret, $c->getComponentsDependingOnRow());
        }

        //rekursive links ignorieren
        foreach ($ret as $k=>$r) {
            while ($r) {
                if ($r->componentId == $this->id) {
                    unset($ret[$k]);
                    break;
                }
                $r = $r->parent;
            }
        }
        return array_values($ret);
    }
}
