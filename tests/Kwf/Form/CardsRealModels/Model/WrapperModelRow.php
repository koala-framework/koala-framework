<?
class Vps_Form_CardsRealModels_Model_WrapperModelRow extends Vps_Model_Db_Row
{
    protected function _getSiblingRows()
    {
        $rows = parent::_getSiblingRows();
        return array($this->type => $rows[$this->type]);
    }

    public function getSilblingRow()
    {
        $rows = parent::_getSiblingRows();
        return $rows[$this->type];
    }
}
