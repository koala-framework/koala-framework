<?php
//requires easybook/geshi package
class Kwc_Advanced_CodeSyntaxHighlight_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Code Syntax Highlighted');
        $ret['componentIcon'] = 'page_code';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['rootElementClass'] = ' kwfUp-webListNone';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        $row = $this->getRow();
        if ($row->language) {
            $geshi = new GeSHi($row->code, $row->language);
            if ($row->line_numbers) {
                $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
            } else {
                $geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS);
            }
            $ret['html'] = $geshi->parse_code();
        } else {
            $ret['html'] = '<code>'.Kwf_Util_HtmlSpecialChars::filter($row->code).'</code>';
        }
        return $ret;
    }

}
