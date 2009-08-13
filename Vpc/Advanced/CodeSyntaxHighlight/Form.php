<?php
class Vpc_Advanced_CodeSyntaxHighlight_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextArea('code'))
            ->setFieldLabel(trlVps('Code'))
            ->setHeight(225)
            ->setWidth(450);

        $values = array();
        require_once 'geshi.php';
        $geshi = new GeSHi();
        foreach (new DirectoryIterator($geshi->language_path) as $i) {
            if ($i->isDir()) continue;
            if (substr($i, -4) != '.php') continue;
            $i = substr($i->__toString(), 0, -4);
            $values[$i] = $i;
        }
        asort($values);
        $this->add(new Vps_Form_Field_Select('language'))
            ->setFieldLabel(trlVps('Language'))
            ->setValues($values);


        $this->add(new Vps_Form_Field_Checkbox('line_numbers'))
            ->setFieldLabel(trlVps('Line Numbers'));
    }
}
