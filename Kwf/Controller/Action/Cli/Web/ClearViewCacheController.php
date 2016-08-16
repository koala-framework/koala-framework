<?php
class Kwf_Controller_Action_Cli_Web_ClearViewCacheController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears view cache";
    }

    public function indexAction()
    {
        Kwf_Util_MemoryLimit::set(512);

        $tables = Kwf_Registry::get('db')->listTables();
        if (!in_array('cache_component', $tables)) {
            echo "Database doesn't contain cache_component, nothing to clear.\n";
            exit;
        }

        $update = array();
        if ($this->_getParam('all')) {
        }
        if ($this->_getParam('dbId')) {
            $update['db_id'] = $this->_getParam('dbId');
        }
        if ($this->_getParam('id')) {
            $update['component_id'] = $this->_getParam('id');
        }
        if ($this->_getParam('expandedId')) {
            $update['expanded_component_id'] = $this->_getParam('expandedId');
        }
        if ($this->_getParam('type')) {
            $update['type'] = $this->_getParam('type');
        }
        if ($this->_getParam('class')) {
            $c = $this->_getParam('class');
            if (strpos($c, '%') === false) {
                $whereClass = array($c);
                foreach (Kwc_Abstract::getComponentClasses() as $cls) {
                    if (in_array($c, Kwc_Abstract::getSetting($cls, 'parentClasses'))) {
                        $whereClass[] = $cls;
                    }
                }
                $update['component_class'] = $whereClass;
            } else {
                $update['component_class'] = $this->_getParam('class');
            }
        }
        if (!$this->_getParam('all') && !$this->_getParam('dbId') && !$this->_getParam('id') && !$this->_getParam('expandedId') && !$this->_getParam('type') && !$this->_getParam('class')) {
            throw new Kwf_Exception_Client("required parameter: --all, --id, --dbId, --expandedId, --type or --class");
        }

        $select = Kwf_Component_Cache::getInstance()->buildSelectForDelete(array($update));

        $model = Kwf_Component_Cache::getInstance()->getModel();
        $entries = $model->countRows($select);
        if (!$entries) {
            echo "No active view cache entries found; nothing to do.\n";
            exit;
        }

        if (!$this->_getParam('force')) {
            echo "Will delete $entries view cache entries. Continue? [Y/n]\n";
            $stdin = fopen('php://stdin', 'r');
            $input = trim(strtolower(fgets($stdin, 2)));
            fclose($stdin);
            if (!($input == '' || $input == 'j' || $input == 'y')) {
                exit(1);
            }
        }

        echo "Deleting view cache...\n";
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(
            Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
            Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
            Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT
        ));
        $c->setTextWidth(50);
        Kwf_Component_Cache::getInstance()->deleteViewCache(array($update), $c);
        echo "done\n";

        if ($this->_getParam('clear')) {
            echo "Clearing table...";
            $model->deleteRows($select);
            echo "done\n";
        }
        exit;
    }

    public static function getHelpOptions()
    {
        return array(
        );
    }
}
