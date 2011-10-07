<?php
class Vpc_Root_Category_GeneratorRow extends Vps_Model_Tree_Row
{
    protected function _beforeInsert()
    {
        parent::_beforeInsert();
        if (!$this->is_home) $this->is_home = 0;
        if (!$this->visible) $this->visible = 0;
        if (!$this->pos) $this->pos = 1;
    }

    protected function _beforeUpdate()
    {
        parent::_beforeUpdate();
        if ($this->is_home && !$this->visible) {
            throw new Vps_ClientException(trlVps('Cannot set Home Page invisible'));
        }
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        if (count($this->getChildNodes())) {
            throw new Vps_ClientException(trlVps("Can't delete page as there are child pages."));
        }

        // Dranhängende Komponente löschen
        $generators = Vps_Component_Data_Root::getInstance()->getPageGenerators();
        foreach ($generators as $generator) {
            $class = Vpc_Abstract::getChildComponentClass($generator->getClass(), null, $this->component);
            Vpc_Admin::getInstance($class)->delete($this->id);
        }
    }

    public function getComponentsDependingOnRow()
    {
        $ret = array();

        foreach (Vpc_Admin::getDependsOnRowInstances() as $a) {
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
