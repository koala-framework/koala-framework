<?php
class Vpc_Trl_StaticTextsPlaceholder_Translate_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['placeholder']['trlTest'] = trlStatic('Sichtbar');
        $ret['placeholder']['trlcTest'] = trlcStatic('time', 'Am');
        $ret['placeholder']['trlpTest1'] = trlpStatic('Antwort', 'Antworten', 1);
        $ret['placeholder']['trlpTest2'] = trlpStatic('Antwort', 'Antworten', 2);
        $ret['placeholder']['trlcpTest1'] = trlcpStatic('test', 'Antwort', 'Antworten', 1);
        $ret['placeholder']['trlcpTest2'] = trlcpStatic('test', 'Antwort', 'Antworten', 2);

        $ret['placeholder']['trlVpsTest'] = trlVpsStatic('Visible');
        $ret['placeholder']['trlcVpsTest'] = trlcVpsStatic('time', 'On');
        $ret['placeholder']['trlpVpsTest1'] = trlpVpsStatic('reply', 'replies', 1);
        $ret['placeholder']['trlpVpsTest2'] = trlpVpsStatic('reply', 'replies', 2);
        $ret['placeholder']['trlcpVpsTest1'] = trlcpVpsStatic('test', 'reply', 'replies', 1);
        $ret['placeholder']['trlcpVpsTest2'] = trlcpVpsStatic('test', 'reply', 'replies', 2);

        return $ret;
    }
}
