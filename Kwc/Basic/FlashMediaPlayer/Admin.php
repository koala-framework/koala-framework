<?php
class Kwc_Basic_FlashMediaPlayer_Admin extends Kwc_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $upload = $data->getComponent()->getRow()->getParentRow('FileMedia');
        if (!$upload) return '';
        return $upload->filename.'.'.$upload->extension;
    }
}
