<?php
class Kwc_Directories_Item_Detail_Form extends Kwf_Form
{
    public function __construct($name, $detailClass = null, $directoryClass = null)
    {
        $this->setClass($detailClass);
        $this->setDirectoryClass($directoryClass);
        parent::__construct($name);
    }

    protected function _createChildComponentForm($id, $name = null)
    {
        if (substr($id, 0, 1)!='-' && substr($id, 0, 1)!='_') {
            throw new Kwf_Exception("'-' or '_' is missing in id");
        }
        if (!$name) $name = substr($id, 1);
        $idTemplate = '{0}'.$id;

        $childComponentClass = null;
        $detailClass = $this->getClass();
        foreach (Kwc_Abstract::getSetting($detailClass, 'generators') as $generatorKey => $generatorData) {
            $generator = Kwf_Component_Generator_Abstract::getInstance($detailClass, $generatorKey);
            if ($generator->getIdSeparator() != substr($id, 0, 1)) continue;
            $childComponentClass = $generator->getComponentByKey($id);
            if ($childComponentClass) {
                break;
            }
        }
        if (!$childComponentClass) {
            throw new Kwf_Exception("No child component with id '$id' for '$detailClass' found.");
        }
        $form = Kwc_Abstract_Form::createComponentForm($childComponentClass, $name);
        if (!$form) return null;
        $detailGen = Kwf_Component_Generator_Abstract::getInstance($this->getDirectoryClass(), 'detail');
        if ($detailGen->hasSetting('dbIdShortcut')) {
            $dbIdShortcut = $detailGen->getSetting('dbIdShortcut');
            $form->setIdTemplate($dbIdShortcut.$idTemplate);
        } else {
            if (!$detailGen->getModel()->hasColumn('component_id')) {
                throw new Kwf_Exception("Model '".get_class($detailGen->getModel())."' doesn't have component_id column");
            }
            $form->setIdTemplate('{component_id}'.$detailGen->getIdSeparator().$idTemplate);
        }
        return $form;
    }
}
