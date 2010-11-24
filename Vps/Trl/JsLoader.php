<?php
class Vps_Trl_JsLoader
{
    public function trlLoad($contents, $language)
    {
        $elements = Vps_Trl::getInstance()->parse($contents, 'js');
        $trl = Vps_Trl::getInstance();
        foreach ($elements as $i=>$trlelement) {
            $values = array();
            if (!isset($trlelement['error'])) {
                if ($trlelement['source'] == Vps_Trl::SOURCE_VPS) {
                    $mode = "Vps";
                    $source = Vps_Trl::SOURCE_VPS;
                } else  {
                    $mode = '';
                    $source = Vps_Trl::SOURCE_WEB;
                }

                //TODO: vereinfachen
                if ($trlelement['type'] == 'trl') {
                    $values['before'] = $trlelement['before'];
                    $values['tochange'] = $trlelement['text'];
                    $method = $trlelement['type'];
                    $values['now'] = $trl->$method($values['tochange'], array(), $source, $language);
                    $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                    $values['now'] = str_replace($method.$mode, "trl", $values['now']);

                } else if ($trlelement['type'] == 'trlc') {
                    $values['context'] = $trlelement['context'];
                    $values['before'] = $trlelement['before'];
                    $values['tochange'] = $trlelement['text'];
                    $method = $trlelement['type'];
                    $values['now'] = $trl->$method($values['context'],$values['tochange'], array(), $source, $language);
                    $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                    $values['now'] = str_replace($method.$mode, 'trl', $values['now']);
                    $values['now'] = str_replace('\''.$values['context'].'\', ', '', $values['now']);
                    $values['now'] = str_replace('"'.$values['context'].'", ', '', $values['now']);

                } else if ($trlelement['type'] == 'trlp') {
                    $values['before'] = $trlelement['before'];
                    $values['single'] = $trlelement['text'];
                    $values['plural'] = $trlelement['plural'];

                    $newValues = Vps_Trl::getInstance()->getTrlpValues(null, $values['single'],
                                                $values['plural'], $trlelement['source'], $language);

                    $method = $trlelement['type'];
                    $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['before']);
                    $values['now'] = str_replace($values['single'], $newValues['single'], $values['now']);
                    $values['now'] = str_replace($method.$mode, 'trlp', $values['now']);


                } else if ($trlelement['type'] == 'trlcp') {

                    $values['before'] = $trlelement['before'];
                    $values['context'] = $trlelement['context'];
                    $values['single'] = $trlelement['text'];
                    $values['plural'] = $trlelement['plural'];

                    $newValues = Vps_Trl::getInstance()->getTrlpValues($values['context'],
                                $values['single'], $values['plural'], $trlelement['source'], $language );

                    $method = 'trlcp'.$mode;
                    $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['before']);
                    $values['now'] = str_replace($values['single'], $newValues['single'], $values['now']);
                    $values['now'] = str_replace("\"".$values['context']."\",", "", $values['now']);
                    $values['now'] = str_replace('\''.$values['context'].'\',', "", $values['now']);
                    $values['now'] = str_replace($method, 'trlp', $values['now']);
                }
                $contents = str_replace($values['before'], $values['now'], $contents);
            }
        }
        return $contents;
    }

}