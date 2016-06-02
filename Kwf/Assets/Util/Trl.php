<?php
class Kwf_Assets_Util_Trl
{
    //returns replacement used for js trl strings
    //used by Kwf_Assets_Dependency_File_Js and Kwf_Assets_CommonJs_Underscore_TemplateDependency
    public static function getJsReplacement($trlElement)
    {
        $b = $trlElement['before'];
        $fn = substr($b, 0, strpos($b, '('));
        $key = $trlElement['type'].'.'.$trlElement['source'];
        if (isset($trlElement['context'])) $key .= '.'.$trlElement['context'];
        $key .= '.'.str_replace("'", "\\'", $trlElement['text']);
        $replace = '';
        if (preg_match('#^([a-z]+\.)trl#i', $b, $m)) {
            $replace = substr($b, 0, strlen($m[1]));
        }
        if ($trlElement['type'] == 'trlp' || $trlElement['type'] == 'trlcp') {
            $replace .= "_kwfTrlp";
        } else {
            $replace .= "_kwfTrl";
        }
        $replace .= "('$key', ".substr($b, strpos($b, '(')+1);

        unset($trlElement['before']);
        unset($trlElement['linenr']);
        unset($trlElement['error_short']);

        return array(
            'before' => $b,
            'replace' => $replace,
            'trlElement' => (object)$trlElement
        );
    }
}
