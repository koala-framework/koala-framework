<?php
class Vpc_Trl_StaticTextsPlaceholder_TrlModelWeb extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'data' => array(
                array('id'=>'1', 'en' => 'Visible', 'de' => 'Sichtbar'),
                array('id'=>'2', 'context' => 'time', 'en' => 'On', 'de' => 'Am'),
                array('id'=>'3', 'en' => 'reply', 'en_plural' => 'replies', 'de' => 'Antwort', 'de_plural' => 'Antworten'),
                array('id'=>'4', 'context' => 'test', 'en' => 'reply', 'en_plural' => 'replies', 'de' => 'Antwort', 'de_plural' => 'Antworten'),
            ),
            'uniqueIdentifier' => 'Vpc_Trl_StaticTextsPlaceholder_TrlModelWeb',
            'columns' => array('id', 'en', 'de', 'en_plural', 'de_plural', 'context')
        );
        parent::__construct($config);
    }
}
