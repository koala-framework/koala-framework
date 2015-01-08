<?php
class Kwc_Advanced_CodeSyntaxHighlight_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextArea('code'))
            ->setAllowTags(true)
            ->setFieldLabel(trlKwf('Code'))
            ->setHeight(225)
            ->setWidth(450);

        $values = array();
        $geshi = new GeSHi();
        foreach (new DirectoryIterator($geshi->language_path) as $i) {
            if ($i->isDir()) continue;
            if (substr($i, -4) != '.php') continue;
            $i = substr($i->__toString(), 0, -4);
            $values[$i] = $i;
        }
        asort($values);
        $this->add(new Kwf_Form_Field_Select('language'))
            ->setFieldLabel(trlKwf('Language'))
            ->setValues($values);


        $this->add(new Kwf_Form_Field_Checkbox('line_numbers'))
            ->setFieldLabel(trlKwf('Line Numbers'));
    }
}
