<?php
class Vpc_Formular_SelectMultiple_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'Checkbox',
                  'fieldLabel' => 'Anzahl der angezeigten Reihen',
                  'name'       => 'rows'
            )
    );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_SelectMultiple_IndexModel';
    protected $_primaryKey = 'id';

   	public function indexAction()
	{

		$controllerUrl = $this->getRequest()->getPathInfo();
		$controllerUrl = str_replace('jsonIndex/', '', $controllerUrl);
		$controllerUrl = str_replace('index/', '', $controllerUrl);
		$cfg['controllerUrl'] = $controllerUrl;
		$cfg['optionsControllerUrl'] = $controllerUrl . 'Options/';
		$this->view->ext('Vpc.Formular.SelectMultiple.Index', $cfg);
	}


}
