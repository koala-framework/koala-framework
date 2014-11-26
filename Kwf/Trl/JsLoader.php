<?php
/**
 * @package Trl
 * @internal
 */
class Kwf_Trl_JsLoader
{
    public function trlLoad($contents, $parsedElements, $language)
    {
        foreach ($this->getReplacements($parsedElements, $language) as $value) {
            $contents = str_replace($value['search'], $value['replace'], $contents);
        }
        return $contents;
    }

    public function getReplacements($parsedElements, $language)
    {
        $ret = array();
        $trl = Kwf_Trl::getInstance();
        foreach ($parsedElements as $i=>$trlelement) {
            $values = array();
            if (!isset($trlelement['error'])) {
                if ($trlelement['source'] == Kwf_Trl::SOURCE_KWF) {
                    $mode = "Kwf";
                    $source = Kwf_Trl::SOURCE_KWF;
                } else  {
                    $mode = '';
                    $source = Kwf_Trl::SOURCE_WEB;
                }

                //TODO: vereinfachen
                if ($trlelement['type'] == 'trl') {
                    $values['before'] = $trlelement['before'];
                    $values['tochange'] = $trlelement['text'];
                    $method = $trlelement['type'].$mode;
                    $values['now'] = $trl->$method($values['tochange'], array(), $language);
                    $values['now'] = str_replace("'", "\'", $values['now']);
                    $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                    $values['now'] = str_replace($method, "trl", $values['now']);

                } else if ($trlelement['type'] == 'trlc') {
                    $values['context'] = $trlelement['context'];
                    $values['before'] = $trlelement['before'];
                    $values['tochange'] = $trlelement['text'];
                    $method = $trlelement['type'].$mode;
                    $values['now'] = $trl->$method($values['context'],$values['tochange'], array(), $language);
                    $beforeWithoutContext = preg_replace('#[\'"]'.$values['context'].'[\'"], ?#', '', $values['before']);
                    $values['now'] = str_replace($values['tochange'], $values['now'], $beforeWithoutContext);
                    $values['now'] = str_replace($method, 'trl', $values['now']);

                } else if ($trlelement['type'] == 'trlp') {
                    $values['before'] = $trlelement['before'];
                    $values['single'] = $trlelement['text'];
                    $values['plural'] = $trlelement['plural'];

                    $newValues = Kwf_Trl::getInstance()->getTrlpValues(null, $values['single'],
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

                    $newValues = Kwf_Trl::getInstance()->getTrlpValues($values['context'],
                                $values['single'], $values['plural'], $trlelement['source'], $language );

                    $beforeWithoutContext = preg_replace('#[\'"]'.$values['context'].'[\'"], ?#', '', $values['before']);
                    $method = 'trlcp'.$mode;
                    $values['now'] = str_replace($values['plural'], $newValues['plural'], $beforeWithoutContext);
                    $values['now'] = str_replace($values['single'], $newValues['single'], $values['now']);
                    $values['now'] = str_replace($method, 'trlp', $values['now']);
                }
                $ret[] = array('search'=>$values['before'], 'replace'=>$values['now']);
            }
        }
        return $ret;
    }

}
