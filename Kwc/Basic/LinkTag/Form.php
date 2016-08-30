<?php
class Kwc_Basic_LinkTag_Form extends Kwc_Abstract_Cards_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setCls('kwc-basic-linktag-form');
        $cards = $this->fields->first();
        $gen = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $gen['child']['component'];
        $cards->getCombobox()
            ->setXtype('kwc.basic.linktag.combobox')
            ->setData(new Kwc_Basic_LinkTag_Form_Data($classes));
        $cards->getCombobox()->getData()->cards = $cards->fields;

        $fs = $this->add(new Kwf_Form_Container_FieldSet('SEO'));
            $fs->setCls('kwc-basic-linktag-seo');
            $fs->setCollapsible(true);
            $fs->setCollapsed(true);

        $fs->add(new Kwf_Form_Field_TextField('title_text', 'Link Title')) //no trl
            ->setWidth(300)
            ->setHelpText(trlKwf('Optional. Description of the link or the link target. Some browsers show the text as a tooltip when the mouse pointer is hovering the link.'));
    }
}

class Kwc_Basic_LinkTag_Form_Data extends Kwf_Data_Table
{
    public $cards;
    public function load($row, array $info = array())
    {
        foreach ($this->cards as $card) {
            $n = $card->getName();
            if (strpos($n, '_') && $row->component == substr($n, 0, strpos($n, '_'))) {
                if ($card->fields->first()->getIsCurrentLinkTag($row)) {
                    return $n;
                }
            }
        }
        return $row->component;
    }

    public function save(Kwf_Model_Row_Interface $row, $data)
    {
        if (strpos($data, '_')!==false) {
            $data = substr($data, 0, strpos($data, '_'));
        }
        $row->component = $data;
    }
}
