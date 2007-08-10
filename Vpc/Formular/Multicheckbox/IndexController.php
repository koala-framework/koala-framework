<?php
class Vpc_Formular_Multicheckbox_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'Checkbox',
                  'fieldLabel' => 'Horizontal',
                  'name'       => 'horizontal')
    );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Multicheckbox_IndexModel';

	public function indexAction()
	{
		$controllerUrl = $this->getRequest()->getPathInfo();
		$controllerUrl = str_replace('jsonIndex/', '', $controllerUrl);
		$controllerUrl = str_replace('index/', '', $controllerUrl);
		$cfg['controllerUrl'] = $controllerUrl;
		$cfg['checkboxesControllerUrl'] = str_replace('_Index', '_Checkboxes', $controllerUrl);
		$this->view->ext('Vpc.Formular.Multicheckbox.Index', $cfg);
	}


}