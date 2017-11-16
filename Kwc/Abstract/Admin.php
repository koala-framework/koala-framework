<?php
class Kwc_Abstract_Admin extends Kwf_Component_Abstract_Admin
{
    public function getDuplicateProgressSteps($source)
    {
        $ret = 0;
        $s = array('ignoreVisible'=>true);
        foreach ($source->getChildComponents($s) as $c) {
            if ($c->generator->hasSetting('inherit') && $c->generator->getSetting('inherit')) {
                if ($c->generator->hasSetting('unique') && $c->generator->getSetting('unique')) {
                    continue;
                }
            }
            if ($c->generator->getGeneratorFlag('pageGenerator')) {
                continue;
            }
            $ret += $c->generator->getDuplicateProgressSteps($c);
        }
        return $ret;
    }

    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
        $contexts = Kwf_Component_Layout_Abstract::getInstance($target->componentClass)->getContexts($target);
        $supportedContexts = Kwf_Component_Layout_Abstract::getInstance($target->componentClass)->getSupportedContexts();
        if ($contexts && $supportedContexts) {
            foreach ($contexts as $context) {
                if (!in_array($context, $supportedContexts)) {
                    throw new Kwf_Component_Exception_IncompatibleContexts("Duplicating component in incompatible context");
                }
            }
        }


        Kwf_Component_LogDuplicateModel::getInstance()->import(
            Kwf_Model_Abstract::FORMAT_ARRAY,
            array(
                array('source_component_id' => $source->dbId, 'target_component_id' => $target->dbId)
            )
        );

        if ($source->getComponent()->getOwnModel() && $source->dbId != $target->dbId) {
            $this->_duplicateOwnRow($source, $target);
        }

        $s = array('ignoreVisible'=>true);
        foreach ($source->getChildComponents($s) as $c) {
            if ($c->generator->hasSetting('inherit') && $c->generator->getSetting('inherit') &&
                $c->generator->hasSetting('unique') && $c->generator->getSetting('unique') &&
                $source->componentId != $c->parent->componentId
            ) {
                continue;
            } else if (!$c->generator->hasSetting('inherit') &&
                !Kwf_Component_Generator_Abstract::hasInstance($target->componentClass, $c->generator->getGeneratorKey())
            ) {
                continue;
            } else if ($c->generator->getGeneratorFlag('pageGenerator')) {
                continue;
            }
            $c->generator->duplicateChild($c, $target, $progressBar);
        }
    }

    protected function _duplicateOwnRow($source, $target)
    {
        $newRow = null;
        $model = $source->getComponent()->getOwnModel();
        $row = $model->getRow($source->dbId);
        if ($row) {
            $targetRow = $model->getRow($target->dbId);
            if ($targetRow) { $targetRow->delete(); }
            $newRow = $row->duplicate(array(
                'component_id' => $target->dbId
            ));
        }
        return $newRow;
    }

    /**
     * Called when duplication of a number of components finished
     */
    public function afterDuplicate($rootSource, $rootTarget)
    {
        parent::afterDuplicate($rootSource, $rootTarget);
    }

    public function makeVisible($source)
    {
        foreach ($source->getChildComponents(array('inherit' => false, 'ignoreVisible'=>true)) as $c) {
            $c->generator->makeChildrenVisible($c);
        }
    }

    public function getCardForms()
    {
        $ret = array();
        $title = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($this->_class, 'componentName'));
        $title = str_replace('.', ' ', $title);
        $ret['form'] = array(
            'form' => Kwc_Abstract_Form::createComponentForm($this->_class, 'child'),
            'title' => $title,
        );
        return $ret;
    }

    public function getPagePropertiesForm($config)
    {
        return null;
    }

    public function exportContent(Kwf_Component_Data $data)
    {
        return array();
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {

    }
}
