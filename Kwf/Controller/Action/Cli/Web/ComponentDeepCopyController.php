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
        Kwf_Util_MemoryLimit::set(4096);
        set_time_limit(0);

        $source = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('source'), array('ignoreVisible'=>true));
        if (!$source) throw new Kwf_Exception_Client("source not found");
        $target = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('target'), array('ignoreVisible'=>true));
        if (!$target) throw new Kwf_Exception_Client("target not found");

        Kwf_Events_ModelObserver::getInstance()->disable(); //This would be slow as hell. But luckily we can be sure that for the new (duplicated) components there will be no view cache to clear.

        Kwf_Registry::get('db')->beginTransaction();

        echo "counting pages...";
        $steps = Kwc_Admin::getInstance($source->componentClass)->getDuplicateProgressSteps($source);
        echo " ".$steps."\n";

        $ad = new Zend_ProgressBar_Adapter_Console();
        $ad->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_BAR, Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT, Zend_ProgressBar_Adapter_Console::ELEMENT_ETA));
        $progressBar = new Zend_ProgressBar($ad, 0, $steps);

        Kwc_Admin::getInstance($source->componentClass)->duplicate($source, $target, $progressBar);

        Kwf_Util_Component::afterDuplicate($source, $target);

        $progressBar->finish();

        Kwf_Registry::get('db')->commit();

        exit;
    }

    private function _getChildIds($id)
    {
        static $pageParentIds;
        if (!isset($pageParentIds)) {
            $pageParentIds = array();
            $sql = "SELECT id, parent_id FROM kwf_pages";
            if ($this->_getParam('startId')) $sql .= ' WHERE id>=' . $this->_getParam('startId');
            foreach (Kwf_Registry::get('db')->query($sql)->fetchAll() as $row) {
                $pageParentIds[$row['id']] = $row['parent_id'];
            }
        }
        $ret = array();
        foreach ($pageParentIds as $pageId=>$parentId) {
            if ($parentId == $id) {
                $ret[] = $pageId;
                $ret = array_merge($ret, $this->_getChildIds($pageId));
            }
        }
        return $ret;
    }

    //if component-deep-copy stopped for some reason use this action to delete a half copied domain
    //attention: components with dbIdShortcut are NOT handled correctly
    public function cleanAction()
    {
        set_time_limit(0);

        if (!$this->_getParam('id')) throw new Kwf_Exception_Client("required parameter --id");
        $db = Zend_Registry::get('db');
        $tables = array();
        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
            $hasComponentId = false;
            if ($table == 'kwf_pages') {
                $tables[] = $table;
            } else {
                foreach ($db->query("SHOW FIELDS FROM $table")->fetchAll() as $field) {
                    if ($field['Field'] == 'component_id') {
                        $tables[] = $table;
                    }
                }
            }
        }

        $ids = $this->_getChildIds($this->_getParam('id'));
        array_unshift($ids, $this->_getParam('id'));
        foreach ($ids as $id) {
            foreach ($tables as $table) {
                if ($table == 'kwf_pages') {
                    $column = 'id';
                } else {
                    $column = 'component_id';
                }
                $sql = "DELETE FROM $table ".
                    "WHERE $column='$id' ".
                       "OR $column LIKE '".str_replace('_', '\_', $id)."\_%' ".
                       "OR $column LIKE '".str_replace('_', '\_', $id)."-%'";
                echo $sql."\n";
                //echo ".";
                if ($this->_getParam('force')) $db->query($sql);
            }
        }
        if (!$this->_getParam('force')) echo "\nadd parameter --force to actually execute queries\n";
        exit;
    }

    public function testAction()
    {
        $source = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('source'), array('ignoreVisible'=>true));
        if (!$source) throw new Kwf_Exception_Client("source not found");
        $parentTarget = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('target'), array('ignoreVisible'=>true));
        if (!$parentTarget) throw new Kwf_Exception_Client("target not found");


        Kwf_Events_ModelObserver::getInstance()->disable(); //This would be slow as hell. But luckily we can be sure that for the new (duplicated) components there will be no view cache to clear.

        echo "counting pages...";
        $steps = Kwf_Util_Component::getDuplicateProgressSteps($source);
        echo " ".$steps."\n";

        $ad = new Zend_ProgressBar_Adapter_Console();
        $ad->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_BAR, Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT, Zend_ProgressBar_Adapter_Console::ELEMENT_ETA));
        $progressBar = new Zend_ProgressBar($ad, 0, $steps);

        $target = Kwf_Util_Component::duplicate($source, $parentTarget, $progressBar);

        Kwf_Util_Component::afterDuplicate($source, $target);

        $progressBar->finish();

        exit;
    }
}
