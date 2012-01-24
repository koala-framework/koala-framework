<?php
class Kwf_Controller_Action_Cli_Web_ComponentDeepCopyController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "recursively duplicate component data";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'source',
                'value'=> 'source componentId',
                'valueOptional' => false,
            ),
            array(
                'param'=> 'target',
                'value'=> 'target componentId',
                'valueOptional' => false,
            )
        );
    }
    public function indexAction()
    {
        set_time_limit(0);

        $parentSource = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('source'), array('ignoreVisible'=>true));
        if (!$parentSource) throw new Kwf_Exception_Client("source not found");
        $parentTarget = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('target'), array('ignoreVisible'=>true));
        if (!$parentTarget) throw new Kwf_Exception_Client("target not found");
        

        Kwf_Component_ModelObserver::getInstance()->disable(); //This would be slow as hell. But luckily we can be sure that for the new (duplicated) components there will be no view cache to clear.

        echo "counting pages...";
        $steps = 0;
        foreach ($parentSource->getChildComponents(array('ignoreVisible'=>true)) as $source) {
            $steps += Kwf_Util_Component::getDuplicateProgressSteps($source);
        }
        echo " ".$steps."\n";

        $ad = new Zend_ProgressBar_Adapter_Console();
        $ad->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_BAR, Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT, Zend_ProgressBar_Adapter_Console::ELEMENT_ETA));
        $progressBar = new Zend_ProgressBar($ad, 0, $steps);

        foreach ($parentSource->getChildComponents(array('ignoreVisible'=>true)) as $source) {
            Kwf_Util_Component::duplicate($source, $parentTarget, $progressBar);
        }

        $progressBar->finish();

        exit;
    }
}
