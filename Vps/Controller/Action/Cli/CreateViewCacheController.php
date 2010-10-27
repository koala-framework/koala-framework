<?php
class Vps_Controller_Action_Cli_CreateViewCacheController extends Vps_Controller_Action_Cli_Abstract
{
    private $_processed = array();
    public static function getHelp()
    {
        return "create view caches for all components";
    }

    public function indexAction()
    {
        if (Zend_Registry::get('config')->debug->componentCache->disable) {
            throw new Vps_ClientException("Macht wenig sinn mit deaktiviertem view-cache - oder?");
        }
//         $this->_doIt(Vps_Component_Data_Root::getInstance());
        $this->_doIt(Vps_Component_Data_Root::getInstance()->getPageByUrl('/forum'));
//         $this->_doIt(Vps_Component_Data_Root::getInstance()->getPageByUrl('/forum/16_stell_dich_vor/39_bin_auch_neu_hier/82/bearbeiten'));
        echo "====>done\n";
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _doIt($component)
    {
//         Vps_Component_Generator_Abstract::clearInstances();

        if (in_array($component->componentId, $this->_processed)) return; //wg. unique boxen unterseiten
        $this->_processed[] = $component->componentId;
        echo date("H:i:s").' ';
        echo round(memory_get_usage()/(1024*2))."M ";
//         echo $component->componentId . ': ';
        if ($component instanceof Vps_Component_Data_Root) {
            echo "(root)\n";
        } else {
            echo $component->url."\n";
        }

        $p = $component->getRecursiveChildComponents(array('flags'=>array('processInput'=>true), 'page'=>false));
        if (Vpc_Abstract::getFlag($component->componentClass, 'processInput')) {
            $p[] = $component;
        }

        $accessDenied = false;
        foreach ($p as $i) {
            $i = $i->getComponent();
            if (method_exists($i, 'processInput')) {
                try {
                    $i->processInput(array());
                } catch (Vps_Exception_AccessDenied $e) {
                    $accessDenied = true;
                }
            }
        }
        if (!$accessDenied) {
            try {
                Vps_View_Component::renderComponent($component);
            } catch (Vps_Exception_AccessDenied $e) {
            }
        }

        $limitCount = 100;
        $limitStart = 0;
        while (1) {
            $select = new Vps_Component_Select();
            $select->limit($limitStart, $limitCount);
            $pages = $component->getChildPages($select);
            foreach ($pages as $c) {
                $this->_doIt($c);
            }
            if (count($pages) >= $limitCount) {
                $limitStart += $limitCount;
            } else {
                break;
            }
        }
    }
}
