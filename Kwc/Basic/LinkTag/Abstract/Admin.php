<?php
class Kwc_Basic_LinkTag_Abstract_Admin extends Kwc_Admin
{
    public final function getLinkTagForms()
    {
        throw new Kwf_Exception('deprecated, use getCardForms()');
    }
}
