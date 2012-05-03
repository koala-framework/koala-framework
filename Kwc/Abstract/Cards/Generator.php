<?php
class Kwc_Abstract_Cards_Generator extends Kwf_Component_Generator_Static
{
    private $_model;

    protected function _getModel()
    {
        if (!$this->_model) {
            $this->_model = Kwc_Abstract::createOwnModel($this->_class);
        }
        return $this->_model;
    }

    public function getChildData($parentData, $select = array())
    {
        Kwf_Benchmark::count('GenCards::getChildData');

        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }

        //es gibt exakt eine unterkomponente mit der id 'child'
        if ($select->hasPart(Kwf_Component_Select::WHERE_ID)) {
            if ($select->getPart(Kwf_Component_Select::WHERE_ID) != '-child') {
                return array();
            }
        }

        $ret = array();
        if (!$parentData) {
            if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
                $cc = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
                $componentValues = array_keys(array_intersect($this->_settings['component'], $cc));
                if (!$componentValues) throw new Kwf_Exception("no component classes found in this generator, should not have been clled");
                reset($this->_settings['component']);
                if (in_array(current($this->_settings['component']), $cc)) throw new Kwf_Exception("can't get the first=default component without parentData as it can be not in the database");
                $s = new Kwf_Model_Select();
                $s->whereEquals('component', $componentValues);
                if ($p = $select->getPart(Kwf_Component_Select::WHERE_CHILD_OF_SAME_PAGE)) {
                    $s->where(new Kwf_Model_Select_Expr_Like('component_id', $p->dbId.'%')); //so db can make use of index
                    $s->where(new Kwf_Model_Select_Expr_RegExp('component_id', '^'.$p->dbId.'[-_][^_]+$'));
                }
                if ($subRoots = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT)) {
                    foreach ($subRoots as $subRoot) {
                        $s->where(new Kwf_Model_Select_Expr_Like('component_id', $subRoot->dbId.'%'));
                    }
                }
                $data = $this->_getModel()->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, array('columns'=>array('component_id')));
                $parentData = array();
                $s = new Kwf_Component_Select();
                $s->copyParts(array(
                    Kwf_Component_Select::IGNORE_VISIBLE,
                ), $select);
                foreach ($data as $i) {
                    foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($i['component_id'].'-child', $s) as $d) {
                        if ($d->parent->componentClass == $this->_class) {
                            $ret[] = $d;
                        }
                    }
                }
            } else {
                throw new Kwf_Exception_NotYetImplemented();
            }
            return $ret;
        }
        $pData = $parentData;
        $parentDatas = is_array($parentData) ? $parentData : array($parentData);
        foreach ($this->_fetchKeys($pData, $select) as $key) {
            foreach ($parentDatas as $parentData) {
                $data = $this->_createData($parentData, $key, $select);
                if ($data) $ret[] = $data;
            }
        }
        return $ret;
    }

    protected function _formatSelect($parentData, $select = array())
    {

        if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $cc = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
            if (is_array($parentData)) {
            } else {
                $component = $this->_getModel()->fetchColumnByPrimaryId('component', $parentData->dbId);
                if (!$component || !in_array($this->_settings['component'][$component], $cc)) return null;
            }
        }
        return parent::_formatSelect($parentData, $select);
    }

    protected function _fetchKeys($parentData, $select)
    {
        //es gibt exakt eine unterkomponente mit der id 'child'
        $ret = array();
        $select = $this->_formatSelect($parentData, $select);
        if (is_null($select)) return array();
        $ret[] = 'child';
        return $ret;
    }

    protected function _acceptKey($key, $select, $parentData)
    {
        return true;
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $componentId = '';
        if ($parentData->componentId) {
            $componentId = $parentData->componentId . $this->_idSeparator;
        }
        $componentId .= $componentKey;
        $dbId = '';
        if ($parentData->dbId) {
            $dbId = $parentData->dbId . $this->_idSeparator;
        }
        $dbId .= $componentKey;
        $component = $this->_getModel()->fetchColumnByPrimaryId('component', $parentData->dbId);
        if (!$component) {
            $components = array_keys($this->getChildComponentClasses()); //sollte eigentlich nicht vorkommen
            $component = $components[0];
        }
        return array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $this->_settings['component'][$component],
            'parent' => $parentData,
            'isPage' => false,
            'isPseudoPage' => false
        );
    }

    public function getStaticChildComponentIds()
    {
        return array($this->_idSeparator.'child');
    }
}
