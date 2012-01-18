<?php
class Kwc_Basic_None_Admin extends Kwc_Admin
{
    // wird verwendet, wenn None als Linktag in einer List verwendet wird (für das Feld Linkziel)
    public function componentToString(Kwf_Component_Data $data)
    {
        return trlKwf('None');
    }
}
