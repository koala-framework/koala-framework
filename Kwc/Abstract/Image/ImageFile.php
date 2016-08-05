<?php
class Kwc_Abstract_Image_ImageFile extends Kwf_Form_Field_File
{
    public function __construct($fieldname = null, $fieldLabel = null)
    {
        parent::__construct($fieldname, $fieldLabel);
        $this->setXtype('kwc.imagefile');
        $this->setAllowOnlyImages(true);
    }

    public function load($row, $postData = array())
    {
        $ret = parent::load($row, $postData);
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible' => true, 'limit' => 1));
        if ($component) { //component can be non-existent if it's in a not selected card
            if (is_instance_of($component->componentClass, 'Kwc_Abstract_Image_Component')) {
                $contentWidth = $component->getComponent()->getMaxContentWidth();
            } else {
                $contentWidth = $component->getComponent()->getContentWidth();
            }
            $ret[$this->getFieldName()]['contentWidth'] = $contentWidth;
        }
        return $ret;
    }
}
