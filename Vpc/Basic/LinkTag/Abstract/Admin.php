<?php
class Vpc_Basic_LinkTag_Abstract_Admin extends Vpc_Admin
{
    public final function getLinkTagForms()
    {
        throw new Vps_Exception('deprecated, use getCardForms()');
    }
}
