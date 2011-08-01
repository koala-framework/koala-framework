<?php
class Vpc_Basic_LinkTag_Form extends Vpc_Abstract_Cards_Form
{
    protected function _init()
    {
        parent::_init();
        $cards = $this->fields->first();
        $cards->getCombobox()
            ->setData(new Vpc_Basic_LinkTag_Form_Data());
        $cards->getCombobox()->getData()->cards = $cards->fields;
    }
}

class Vpc_Basic_LinkTag_Form_Data extends Vps_Data_Abstract
{
    public $cards;
    public function load($row)
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

    public function save(Vps_Model_Row_Interface $row, $data)
    {
        if (strpos($data, '_')!==false) {
            $data = substr($data, 0, strpos($data, '_'));
        }
        $row->component = $data;
    }
}