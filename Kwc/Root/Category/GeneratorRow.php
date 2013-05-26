<?php
class Kwc_Root_Category_GeneratorRow extends Kwf_Model_Tree_Row
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
            throw new Kwf_ClientException(trlKwf('Cannot set Home Page invisible'));
        }
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        // Dranhängende Komponente löschen
        $generators = Kwf_Component_Data_Root::getInstance()->getPageGenerators();
        foreach ($generators as $generator) {
            $class = Kwc_Abstract::getChildComponentClass($generator->getClass(), null, $this->component);
            Kwc_Admin::getInstance($class)->delete($this->id);
        }
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
