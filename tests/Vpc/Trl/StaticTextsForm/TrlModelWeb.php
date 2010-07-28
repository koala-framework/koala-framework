<?php
class Vpc_Trl_StaticTextsForm_TrlModelWeb extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'data' => array(
                array('id'=>'1', 'en' => 'Firstname', 'de' => 'Vorname'),
                array('id'=>'2', 'en' => 'Lastname', 'de' => 'Nachname'),
                array('id'=>'3', 'en' => 'Company', 'de' => 'Firma')
            ),
            'uniqueIdentifier' => 'Vpc_Trl_StaticTextsForm_TrlModelWeb',
            'columns' => array('id', 'en', 'de', 'en_plural', 'de_plural', 'context')
        );
        parent::__construct($config);
    }
}
