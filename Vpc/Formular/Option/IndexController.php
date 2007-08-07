<?php
class Vpc_Formular_Option_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'Checkbox',
                  'fieldLabel' => 'horizontal',
                  'name'       => 'horizontal'
            )
    );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Option_IndexModel';
    protected $_primaryKey = 'id';

   	public function indexAction()
	{

		$controllerUrl = $this->getRequest()->getPathInfo();
		$controllerUrl = str_replace('jsonIndex/', '', $controllerUrl);
		$controllerUrl = str_replace('index/', '', $controllerUrl);
		$cfg['controllerUrl'] = $controllerUrl;
		$cfg['optionsControllerUrl'] = str_replace('_Index', '_Options', $controllerUrl);
		$this->view->ext('Vpc.Formular.Option.Index', $cfg);
	}


}
