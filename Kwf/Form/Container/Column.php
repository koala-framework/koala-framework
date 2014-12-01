<?php
/**
 * @package Form
 */
class Kwf_Form_Container_Column extends Kwf_Form_Container_Abstract
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setBaseCls('x2-plain');
        $this->setStyle('margin: 0px 10px;');
    }

    public function getTemplateVars($values, $fieldNamePostfix='', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $style = '';
        if ($this->getWidth()) {
            $style .= 'width: '.$this->getWidth().'px';
        }
        $ret['preHtml'] = '<div style="'.$style.'">';
        $ret['postHtml'] = '</div>';

        static $nr; //TODO: das darf so nicht sein -- bei aenderung bitte chris sagen, da css auf $nr basiert
        $nr++;
        $ret['id'] = 'Column' . $nr;
        return $ret;
    }
}
