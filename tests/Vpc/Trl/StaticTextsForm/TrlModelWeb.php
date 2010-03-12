<?php
class Vpc_Trl_StaticTextsForm_TrlModelWeb extends Vps_Trl_Model_Web
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>'1', 'en' => 'Firstname', 'de' => 'Vorname'),
                array('id'=>'2', 'en' => 'Lastname', 'de' => 'Nachname'),
                array('id'=>'3', 'en' => 'Company', 'de' => 'Firma')
            ),
            'uniqueIdentifier' => 'Vpc_Trl_StaticTextsForm_TrlModelWeb',
            'columns' => array('id', 'en', 'de', 'en_plural', 'de_plural', 'context')
        ));
        parent::__construct($config);
    }
}
