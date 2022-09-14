<?php
class Kwc_Paragraphs_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $ret = array();
        $ret['paragraphs'] = array();
        foreach ($data->getChildComponents($this->_getParagraphsSelect()) as $paragraph) {
            $ret['paragraphs'][] = $paragraph;
        }
        return $ret;
    }

    protected function _getParagraphsSelect()
    {
        return array(
            'generator' => 'paragraphs',
        );
    }
}
