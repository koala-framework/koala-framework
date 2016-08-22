<?php
class Kwf_Controller_Action_Cli_Web_ComponentCollectGarbageController extends Kwf_Controller_Action
{
    public static function getHelp()
    {
        return "collect component garbage, execute once a day";
    }

    public function indexAction()
    {
        $model = Kwf_Component_Cache_Mysql::getInstance()->getModel();
        $includesModel = Kwf_Component_Cache_Mysql::getInstance()->getModel('includes');

        $s = new Kwf_Model_Select();
        $s->whereEquals('deleted', true);
        $s->where(new Kwf_Model_Select_Expr_Lower('microtime', (time()-3*24*60*60)*1000));
        $options = array(
            'columns' => array('component_id')
        );
        if ($this->_getParam('debug')) {
            echo "querying for garbage in cache_component...\n";
        }
        foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, $options) as $row) {
            if ($this->_getParam('debug')) {
                echo "deleting ".$row['component_id']."\n";
            }
            $s = new Kwf_Model_Select();
            $s->whereEquals('component_id', $row['component_id']);
            $model->deleteRows($s);

            $s = new Kwf_Model_Select();
            $s->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Equal('component_id', $row['component_id']),
                new Kwf_Model_Select_Expr_Like('target_id', $row['component_id'].':%'),
            )));
            $includesModel->deleteRows($s);
        }

        exit;
    }
}
