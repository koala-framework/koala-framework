<?php
class Kwf_Component_Generator_Box_StaticSelect extends Kwf_Component_Generator_Static
{
    protected $_eventsClass = 'Kwf_Component_Generator_Box_Events_StaticSelect';

    protected function _init()
    {
        if (count($this->_settings['component']) < 2) {
            throw new Kwf_Exception("You need at least two components for a Box_StaticSelect generator");
        }
        if (!isset($this->_settings['model'])) {
            $this->_settings['model'] = 'Kwf_Component_Generator_Box_StaticSelect_Model';
        }
        if (!isset($this->_settings['boxName'])) {
            $this->_settings['boxName'] = null;
        }
        parent::_init();
    }

    protected function _formatConfig($parentData, $key)
    {
        $ret = array(
            'componentId' => $parentData->componentId . $this->_idSeparator . $this->getGeneratorKey(),
            'dbId' => $parentData->dbId . $this->_idSeparator . $this->getGeneratorKey(),
            'parent' => $parentData,
            'isPage' => false,
            'isPseudoPage' => false,
            'inherit' => false
        );
        $ret['box'] = $this->getGeneratorKey();

        $id = $parentData->dbId.'-'.$this->getGeneratorKey();
        $component = $this->_getModel()->fetchColumnByPrimaryId('component', $id);
        if (!$component || !isset($this->_settings['component'][$component])) {
            $cmps = $this->_settings['component'];
            $ret['componentClass'] = array_shift($cmps);
        } else {
            $ret['componentClass'] = $this->_settings['component'][$component];
        }
        return $ret;
    }

    public function getChildData($parentData, $select = array())
    {
        Kwf_Benchmark::count('GenStaticSelect::getChildData');

        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }
        if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $continue = false;
            foreach ($select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES) as $componentClass) {
                if (in_array($componentClass, $this->getChildComponentClasses())) {
                    $continue = true;
                }
            }
            if (!$continue) return array();
        }
        if (!$parentData) {
            if (!$select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
                throw new Kwf_Exception_NotYetImplemented();
            }
            $selectClasses = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
            $possibleClasses = $this->getChildComponentClasses();
            if (in_array(array_shift($possibleClasses), $selectClasses)) {
                throw new Kwf_Exception("You can't search for component which is first (=default) in StaticSelect");
            }
            $searchFor = array();
            foreach ($selectClasses as $c) {
                $searchFor[] = array_search($c, $possibleClasses);
            }
            $s = new Kwf_Model_Select();
            $s->whereEquals('component', $searchFor);
            $s->where(new Kwf_Model_Select_Expr_Like('component_id', '%-'.$this->getGeneratorKey()));
            $rows = $this->_getModel()->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, array('columns'=>array('component_id')));
            $parentDatas = array();
            foreach ($rows as $row) {
                $id = substr($row['component_id'], 0, -(strlen($this->getGeneratorKey())+1));
                $s = new Kwf_Component_Select();
                $s->copyParts(array(Kwf_Component_Select::IGNORE_VISIBLE, Kwf_Component_Select::WHERE_SUBROOT), $select);
                $d = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, $s);
                if ($d) {
                    $parentDatas[] = $d;
                }
            }
        } else {
            $parentDatas = array(
                $parentData
            );
        }

        $ret = array();
        foreach ($parentDatas as $parentData) {
            $data = $this->_createData($parentData, $this->getGeneratorKey(), $select);
            if (!$data) continue;

            if ($select->hasPart('whereId')) {
                if ('-' . $data->id != $select->getPart('whereId')) continue;
            }
            if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
                $selectClasses = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
                if (!in_array($data->componentClass, $selectClasses)) {
                    continue;
                }
            }
            $ret[] = $data;
        }
        return $ret;
    }

    public function getBoxes()
    {
        return array($this->getGeneratorKey());
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['box'] = true;
        $ret['staticSelect'] = true;
        return $ret;
    }

    public function getPagePropertiesForm()
    {
        return new Kwf_Component_Generator_Box_StaticSelect_PagePropertiesForm($this);
    }

    public function getStaticChildComponentIds()
    {
        return array($this->_idSeparator.$this->getGeneratorKey());
    }

    public function duplicateChild($source, $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }

        $sourceRow = $this->_getModel()->getRow($source->dbId);

        if ($sourceRow) { //if not row exists that's ok, it's also not needed in the duplicated one
            $targetId = $parentTarget->dbId.$this->_idSeparator.$source->id;
            $targetRow = $this->_getModel()->getRow($targetId);
            if ($targetRow) { $targetRow->delete(); }
            $sourceRow->duplicate(array(
                'component_id' => $targetId,
            ));
        }

        return parent::duplicateChild($source, $parentTarget, $progressBar);
    }
}
