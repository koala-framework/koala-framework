<?php
class Kwc_Basic_LinkTag_Form extends Kwc_Abstract_Cards_Form
{
    protected function _init()
    {
        parent::_init();
        $cards = $this->fields->first();
        $gen = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $gen['child']['component'];
        $cards->getCombobox()
            ->setData(new Kwc_Basic_LinkTag_Form_Data($classes));
        $cards->getCombobox()->getData()->cards = $cards->fields;
    }
}

class Kwc_Basic_LinkTag_Form_Data extends Kwc_Abstract_Cards_Form_ComboBoxData
{
    public $cards;
    public function load($row)
    {
        $ret = parent::load($row);
        foreach ($this->cards as $card) {
            $n = $card->getName();
            if (strpos($n, '_') && $row->component == substr($n, 0, strpos($n, '_'))) {
                if ($card->fields->first()->getIsCurrentLinkTag($row)) {
                    $ret['value'] = $n;
                    return $ret;
                }
            }
        }
        $ret['value'] = $row->component;
        return $ret;
    }

    public function save(Kwf_Model_Row_Interface $row, $data)
    {
        if (strpos($data, '_')!==false) {
            $data = substr($data, 0, strpos($data, '_'));
        }
        $row->component = $data;
    }
}