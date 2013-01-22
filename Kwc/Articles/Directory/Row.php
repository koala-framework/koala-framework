<?php
class Kwc_Articles_Directory_Row extends Kwf_Model_Db_Row
{
    public function markRead()
    {
        $u = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($u) {
            $row = $this->createChildRow('Views');
            $row->user_id = $u->id;
            $row->date = date('Y-m-d H:i:s');
            $row->save();

            $this->views++;
            $this->save();
        }
    }
}
