<?php
class Kwc_Basic_Text_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $ret = array();

        $parser = new Kwc_Basic_Text_ApiContentHtmlParser();
        $ret['content'] = $parser->parse($data);

        return $ret;
    }
}
