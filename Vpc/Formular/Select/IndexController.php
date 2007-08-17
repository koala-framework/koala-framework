<?php
class Vpc_Formular_Select_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'ComboBox',
                  'fieldLabel' => 'Typ',
                  'hiddenName' => 'type',
                  'mode'       => 'local',
                  'store'      => array('data' => array(array('select', 'ComboBox'),
                                                        array('radio', 'Radio-Buttons'),
                                                        array('radio_horizontal', 'Radio-Buttons horizontal')),
                                       ),
                  'editable'   => false,
                  'triggerAction'=>'all'),
    );
    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Select_IndexModel';

    public function indexAction()
    {
        $controllerUrl = $this->getRequest()->getPathInfo();
        $controllerUrl = str_replace('jsonIndex/', '', $controllerUrl);
        $controllerUrl = str_replace('index/', '', $controllerUrl);
        $cfg['controllerUrl'] = $controllerUrl;
        $cfg['optionsControllerUrl'] = str_replace('_Index', '_Options', $controllerUrl);
        $this->view->ext('Vpc.Formular.Select.Index', $cfg);
    }
}
