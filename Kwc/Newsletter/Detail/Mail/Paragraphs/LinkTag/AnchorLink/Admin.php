<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_AnchorLink_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return trlKwf('Anchor ').$data->getComponent()->getRow()->anchor;
    }
}
