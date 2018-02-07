<?php
class Kwc_Newsletter_Detail_QueueLogData extends Kwf_Data_Table
{
    public function load($row, array $info = array())
    {
        $name = $this->getField();

        $model = Kwf_Model_Abstract::getInstance($row->recipient_model);
        if ($model->hasColumn($name)) {
            $recipientRow = $model->getRow($row->recipient_id);
            if ($recipientRow) return $recipientRow->{$name};
        }

        return null;
    }
}
