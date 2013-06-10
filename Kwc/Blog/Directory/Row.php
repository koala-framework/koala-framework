<?php
class Kwc_Blog_Directory_Row extends Kwf_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->title;
    }
    protected function _beforeInsert()
    {
        parent::_beforeInsert();
        $this->author_id = Kwf_Registry::get('userModel')->getAuthedUserId();
    }
}
