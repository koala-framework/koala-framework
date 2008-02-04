<?php
class Vpc_Basic_LinkTag_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);

        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');

        $this->add(new Vps_Auto_Field_Select('link_class', 'Linktype'))
            ->setValues(array_flip($classes))
            ->setId('LinkClass');

        $layout = new Vps_Auto_Container('CardLayout');
        $layout->setLayout('card');
        $layout->setId('CardsContainer');
        $layout->setBaseCls('x-plain');
        foreach ($classes as $name => $class) {
            $formname = str_replace('_Component', '_Form', $class);
            $form = new $formname($class);
            $form->setComponentIdTemplate('{0}-1');
            $form->setAutoHeight(true);
            $form->setBaseCls('x-plain');
            $form->setProperty('id', $class);
            $layout->add($form);
        }
        $this->add($layout);
    }

    public function prepareSave($parentRow, $postData)
    {
        $linktype = $postData[$this->fields['link_class']->getFieldName()];
        foreach ($this->fields['CardLayout']->getChildren() as $child) {
            if ($linktype != $child->getName()) {
                unset($this->fields['CardLayout']->fields[$child->getName()]);
            }
        }
        parent::prepareSave($parentRow, $postData);
    }
}
