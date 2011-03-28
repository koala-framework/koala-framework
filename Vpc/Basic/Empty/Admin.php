<?php
class Vpc_Basic_Empty_Admin extends Vpc_Admin
{
    // wird verwendet, wenn Empty als Linktag in einer List verwendet wird (für das Feld Linkziel)
    public function componentToString(Vps_Component_Data $data)
    {
        return trlVps('Empty');
    }
}
