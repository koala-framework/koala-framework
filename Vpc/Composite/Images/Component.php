<?php
class Vpc_Composite_Images_Component extends Vpc_Abstract
{
    const NAME = 'Standard.Images';
    public $images = array();
    protected $_settings = array(
        'imageClass'        => 'Vpc_Basic_Image_Component',
        'imageSettings'     => array()
    );
    protected $_tablename = 'Vpc_Composite_Images_Model';

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['images'] = array();
        foreach ($this->images as $c) {
            $return['images'][] = $c->getTemplateVars();
        }
        $return['template'] = 'Composite/Images.html';
        return $return;
    }

    protected function _init()
    {
        $table = $this->getTable('Vpc_Composite_Images_Model');
        $where = array(
            'page_id = ?' => $this->getDbId(),
            'component_key = ?' => $this->getComponentKey()
        );
        if (!$this->showInvisible()) {
            $where['visible = ?'] = 1;
        }
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Component');
        foreach ($table->fetchAll($where) as $row) {
            $this->images[$row->id] = $this->createComponent($imageClass, $row->id, $this->getSetting('imageSettings'));
        }
    }

    public function getChildComponents()
    {
        return $this->images;
    }

}