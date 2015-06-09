<?php
class Kwf_Util_Model_Row_Welcome extends Kwf_Model_Db_Row
{
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->isDirty('login_kwf_upload_id')) {
            Kwf_Media::clearCache('Kwf_Util_Model_Welcome', $this->id, 'LoginImage');
            Kwf_Media::clearCache('Kwf_Util_Model_Welcome', $this->id, 'LoginImageLarge');
        }
        if ($this->isDirty('kwf_upload_id')) {
            Kwf_Media::clearCache('Kwf_Util_Model_Welcome', $this->id, 'WelcomeImage');
        }
    }
}
