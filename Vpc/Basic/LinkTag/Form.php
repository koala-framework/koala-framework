<?php
class Vpc_Basic_LinkTag_Form extends Vps_Auto_Vpc_Form
{
    private $_class;
    public function __construct($class, $pageId = null, $componentKey = null)
    {
        $this->_class = $class;
        parent::__construct($class, $pageId, $componentKey);
        
        $classes = Vpc_Abstract::getSetting($class, 'linkClasses');

        $this->add(new Vps_Auto_Field_Select('link_class', 'Linktype'))
            ->setValues($classes)
            ->setId('LinkClass');

        $layout = new Vps_Auto_Container('CardLayout');
        $layout->setLayout('card');
        $layout->setId('CardsContainer');
        foreach ($classes as $class => $name) {
            $formname = str_replace('_Component', '_Form', $class);
            $form = new $formname($class, $pageId, $componentKey . '-1');
            $form->setAutoHeight(true);
            $form->setProperty('id', $class);
            $layout->add($form);
        }
        $this->add($layout);
    }
    
    public function prepareSave($parentRow, $postData)
    {
        $linktype = $postData[$this->_class . '_link_class'];
        foreach ($this->fields['CardLayout']->getChildren() as $child) {
            if ($linktype != $child->getName()) {
                unset($this->fields['CardLayout']->fields[$child->getName()]);
            }
        }
        parent::prepareSave($parentRow, $postData);
    }
}