<?php
class Kwf_User_UnionRow extends Kwf_Model_Union_Row
{
    public function writeLog($messageType)
    {
        $row = $this->getSourceRow();
        if (method_exists($row, 'writeLog')) {
            $row->writeLog($messageType);
        }
    }
}
