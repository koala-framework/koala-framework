<?php
class Kwc_Trl_StaticTextsPlaceholder_Translate_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['placeholder']['trlTest'] = trlStatic('Sichtbar');
        $ret['placeholder']['trlcTest'] = trlcStatic('time', 'Am');
        $ret['placeholder']['trlpTest1'] = trlpStatic('Antwort', 'Antworten', 1);
        $ret['placeholder']['trlpTest2'] = trlpStatic('Antwort', 'Antworten', 2);
        $ret['placeholder']['trlcpTest1'] = trlcpStatic('test', 'Antwort', 'Antworten', 1);
        $ret['placeholder']['trlcpTest2'] = trlcpStatic('test', 'Antwort', 'Antworten', 2);

        $ret['placeholder']['trlKwfTest'] = trlKwfStatic('Visible');
        $ret['placeholder']['trlcKwfTest'] = trlcKwfStatic('time', 'On');
        $ret['placeholder']['trlpKwfTest1'] = trlpKwfStatic('reply', 'replies', 1);
        $ret['placeholder']['trlpKwfTest2'] = trlpKwfStatic('reply', 'replies', 2);
        $ret['placeholder']['trlcpKwfTest1'] = trlcpKwfStatic('test', 'reply', 'replies', 1);
        $ret['placeholder']['trlcpKwfTest2'] = trlcpKwfStatic('test', 'reply', 'replies', 2);

        return $ret;
    }
}
