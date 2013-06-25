<?php
class Vpc_Basic_FlashMediaPlayer_Admin extends Vpc_Abstract_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        $upload = $data->getComponent()->getRow()->getParentRow('FileMedia');
        if (!$upload) return '';
        return $upload->filename.'.'.$upload->extension;
    }
}
