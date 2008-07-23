<?php
class Vpc_Basic_Image_Enlarge_Form extends Vpc_Basic_Image_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        if (Vpc_Abstract::getSetting($class, 'hasSmallImageComponent')) {
            $image = Vpc_Abstract_Form::createChildComponentForm($class, '-imageEnlarge');
            $image->fields->getByName('vps_upload_id')->setFileFieldLabel(trlVps('File (optional)'));
            $this->add(new Vps_Form_Container_FieldSet(trlVps('Small Image')))
                ->add($image);
        }
    }
    protected function _getIdByParentRow($parentRow)
    {
        $id = $this->getId();
        if ($this->getIdTemplate()) {
            if (!$parentRow) {
                throw new Vps_Exception("Form has an idTemplate set - so getRow required a parentRow as first argument");
            }
            $pk = $parentRow->getModel()->getPrimaryKey();
            $id = $parentRow->$pk;
            if (!$id) {
                return null;
            }
            $id = str_replace('{0}', $id, $this->getIdTemplate());
            if (preg_match_all('#{([a-z0-9_]+)}#', $id, $m)) {
                foreach ($m[1] as $i) {
                    if (!isset($parentRow->$i)) {
                        throw new Vps_Exception("Column '$i' as specified in idTemplate doesn't exist in parentRow");
                    }
                    $id = str_replace('{'.$i.'}', $parentRow->$i, $id);
                }
            }
        }
        return $id;
    }
}
