<?php
class Vpc_Formular_Select_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_fields = array(
            array('type'       => 'ComboBox',
                  'fieldLabel' => 'Typ',
                  'hiddenName' => 'type',
                  'mode'       => 'local',
                  'store'      => array('data' => array(array('radio', 'Radio-Buttons'),
                                                        array('radio_horizontal', 'Radio-Buttons horizontal'),
                                                        array('select', 'Select-Feld')),
                                       ),
                  'editable'   => false,
                  'triggerAction'=>'all'),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Größe des Select-Feldes',
                  'name'       => 'size',
                  'width'      => 60)
    );
    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Select_Model';

    public function indexAction()
    {
        $controllerUrl = $this->getRequest()->getPathInfo();
        $controllerUrl = str_replace('jsonIndex/', '', $controllerUrl);
        $controllerUrl = str_replace('index/', '', $controllerUrl);
        $cfg['controllerUrl'] = $controllerUrl;
        $cfg['optionsControllerUrl'] = str_replace('_Component', '_Options', $controllerUrl);
        $this->view->ext('Vpc.Formular.Select.Panel', $cfg);
    }
}
