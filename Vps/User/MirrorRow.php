<?php
class Vps_User_MirrorRow extends Vps_Model_MirrorCache_Row
{
    protected function _getInsertSourceRow()
    {
        if (empty($this->webcode) && !is_null($this->webcode)) {
            $allModel = Vps_Model_Abstract::getInstance('Vps_User_All_Model');
            $allRow = $allModel->getRow($allModel->select()
                ->whereEquals('email', $this->email)
                ->whereEquals('webcode', '')
                ->whereEquals('deleted', 0)
            );
            if ($allRow) {
                $relationModel = Vps_Model_Abstract::getInstance('Vps_User_Relation_Model');
                $relRow = $relationModel->createRow();
                $relRow->user_id = $allRow->id;
                $relRow->locked = 0;
                $relRow->save();

                $model = $this->getModel()->getSourceModel();
                $row = $model->getRow($model->select()
                    ->whereEquals('email', $this->email)
                    ->whereEquals('webcode', '')
                    ->whereEquals('deleted', 0)
                );

                if ($row) {
                    $this->created = $row->created;
                    $this->deleted = $row->deleted;
                    $this->locked = $row->locked;
                    $this->password = $row->password;
                    $this->password_salt = $row->password_salt;
                    return $row;
                }
            }
        }
        return parent::_getInsertSourceRow();
    }

    protected function _callObserver($fn)
    {
    }
}
