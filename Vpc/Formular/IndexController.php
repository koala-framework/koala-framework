<?php
class Vpc_Formular_IndexController extends Vpc_Paragraphs_IndexController
{
   protected $_columns = array(
            array('dataIndex' => 'component_class',
                  'header'    => 'Komponente',
                  'width'     => 300),
            array('dataIndex' => 'visible',
                  'header'    => 'Sichtbar',
                  'width'     => 50,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'name',
                  'header'    => 'Bezeichnung',
                  'width'     => 150,
                  'editor'    => 'TextField',
                  ),
            array('dataIndex' => 'mandatory',
                  'header'    => 'Verpflichtend',
                  'width'     => 80,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'no_cols',
                  'header'    => 'noCols',
                  'width'     => 50,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'page_id',
                  'header'    => 'page_id',
                  'type'      => 'string',
                  'width'     => 50,
                  'hidden'   =>  true,
                  ),
            array('dataIndex' => 'id',
                  'header'    => 'id',
                  'width'     => 50,
                  'hidden'   =>  false,
                  )

            );
    protected $_tableName = 'Vpc_Formular_IndexModel';
    protected $_jsClass = 'Vpc.Formular.Index';

    public function init()
    {
        parent::init();
        $c = Vpc_Setup_Abstract::getAvailableComponents(VPS_PATH . 'Vpc/Formular/');
        $this->_components = array();
        foreach ($c as $key => $val) {
            if ($key != 'Formular.Formular') {
                $key = str_replace('Formular.', '', $key);
                $this->_components[$key] = $val;
            }
        }
    }

}