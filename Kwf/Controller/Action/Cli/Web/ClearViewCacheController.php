<?php
class Kwf_Controller_Action_Cli_Web_ClearViewCacheController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears view cache";
    }

    public function indexAction()
    {
        $select = new Kwf_Model_Select();
        if ($this->_getParam('all')) {
        }
        if ($this->_getParam('dbId')) {
            $select->where(new Kwf_Model_Select_Expr_Like('db_id', $this->_getParam('dbId')));
        }
        if ($this->_getParam('id')) {
            $select->where(new Kwf_Model_Select_Expr_Like('component_id', $this->_getParam('id')));
        }
        if ($this->_getParam('expandedId')) {
            $select->where(new Kwf_Model_Select_Expr_Like('expanded_component_id', $this->_getParam('expandedId')));
        }
        if ($this->_getParam('class')) {
            $select->where(new Kwf_Model_Select_Expr_Like('component_class', $this->_getParam('class')));
        }
        if (!$this->_getParam('all') && !$this->_getParam('dbId') && !$this->_getParam('id') && !$this->_getParam('expandedId') && !$this->_getParam('class')) {
            throw new Kwf_Exception_Client("required parameter: --all, --id, --dbId, --expandedId or --class");
        }

        $model = Kwf_Component_Cache::getInstance()->getModel();
        $entries = $model->countRows($select);
        if (!$entries) {
            echo "No active view cache entries found; nothing to do.\n";
            exit;
        }

        echo "Will delete $entries view cache entries. Continue? [Y/n]\n";
        $stdin = fopen('php://stdin', 'r');
        $input = trim(strtolower(fgets($stdin, 2)));
        fclose($stdin);
        if (!($input == '' || $input == 'j' || $input == 'y')) {
            exit(1);
        }

        echo "Deleting view cache...";
        Kwf_Component_Cache::getInstance()->deleteViewCache($select);
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
        $types = Kwf_Util_ClearCache::getInstance()->getTypes();
        return array(
            array(
                'param'=> 'type',
                'value'=> $types,
                'valueOptional' => true,
                'help' => 'what to clear'
            ),
            array(
                'param'=> 'server',
                'help' => 'server',
                'allowBlank' => true
            )
        );
    }
}
