<?php
class Kwc_Root_CategoryGenerator extends Kwf_Component_Generator_Table
{
    protected $_hasNumericIds = false;
    protected $_inherits = true;

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'folder';
        return $ret;
    }

    protected function _formatConfig($parentData, $id)
    {
        $ret = parent::_formatConfig($parentData, $id);
        $ret['name'] = $ret['row']->name;
        return $ret;
    }

    protected function _getParentDataByRow($row, $select = null)
    {
        if (is_instance_of($this->_class, 'Kwc_Root_Component')) {
            return Kwf_Component_Data_Root::getInstance();
        }
        if ($select->hasPart(Kwf_Component_Select::WHERE_SUBROOT)) {
            $subroot = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT);
            $component = $subroot[0];
            while (!Kwc_Abstract::getFlag($component->componentClass, 'subroot')) {
                $component = $component->parent;
            }
            if ($component->componentClass == $this->getClass()) {
                return $component;
            }
            return null;
        }
        return Kwf_Component_Data_Root::getInstance()->getComponentsBySameClass($this->_class);
    }

    public function getChildData($parentData, $select = array())
    {
        if (is_array($select)) $select = new Kwf_Component_Select($select);
        if (!is_instance_of($this->_class, 'Kwc_Root_Component') && $select->hasPart(Kwf_Component_Select::WHERE_SUBROOT)) {
            //abkürzung wenn mehrere domains mit unterschiedlichen component-Klassen
            //im prinzip gleicher code wie in _GetParentDataByRow wenn return null gemacht wird, aber das hier wird früher gemacht
            $subroot = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT);
            $component = $subroot[0];
            while (!Kwc_Abstract::getFlag($component->componentClass, 'subroot')) {
                $component = $component->parent;
            }
            if ($component->componentClass != $this->getClass()) {
                Kwf_Benchmark::count('GenTable::getChildData skipped');
                return array();
            }
        }
        return parent::getChildData($parentData, $select);
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['showInPageTreeAdmin'] = true;
        $ret['showInLinkInternAdmin'] = true;
        return $ret;
    }

    public function duplicateChild($source, $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        if ($progressBar) $progressBar->next();

        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }
        $id = $source->id;
        $target = $parentTarget->getChildComponent(array('id'=>$id, 'ignoreVisible'=>true));
        if (!$target) {
            return null;
        }
        Kwc_Admin::getInstance($source->componentClass)->duplicate($source, $target, $progressBar);
        return $target;
    }


    public function getDuplicateProgressSteps($source)
    {
        $ret = 1;
        $ret += Kwc_Admin::getInstance($source->componentClass)->getDuplicateProgressSteps($source);
        return $ret;
    }

    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $row = $cmp->row;
        $ret['name'] = $row->name;
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        if (isset($data['name'])) {
            $row = $cmp->row;
            $row->name = $data['name'];
            $row->save();
        }

    }
}
