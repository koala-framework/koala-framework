<?php
class Kwc_Basic_Empty_Admin extends Kwc_Admin
{
    // wird verwendet, wenn Empty als Linktag in einer List verwendet wird (für das Feld Linkziel)
    public function componentToString(Kwf_Component_Data $data)
    {
        return trlKwf('Empty');
    }
}
