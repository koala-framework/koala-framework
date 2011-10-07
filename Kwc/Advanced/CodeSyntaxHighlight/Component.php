<?php
class Vpc_Advanced_CodeSyntaxHighlight_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Code Syntax Highlighted');
        $ret['componentIcon'] = new Vps_Asset('page_code');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        require_once 'geshi.php';

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
            $ret['html'] = '<code>'.htmlspecialchars($row->code).'</code>';
        }
        return $ret;
    }

}
