<?php
class Vpc_Basic_LinkTag_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');

        reset($classes);
        $cards = $this->add(new Vps_Form_Container_Cards('link_class', trlVps('Linktype')))
            ->setDefaultValue(key($classes));

        foreach ($classes as $name => $class) {
            $formname = str_replace('_Component', '_Form', $class);
            $form = new $formname($class, $class);
            $form->setComponentIdTemplate('{0}-1');
            $form->setAutoHeight(true);
            $form->setBaseCls('x-plain');

            $card = $cards->add();
            $card->setTitle($name);
            $card->setName($class);
            $card->add($form);
        }
    }


    public function validate($postData)
    {
        $linktype = $postData[$this->fields['link_class']->getFieldName()];
        foreach ($this->fields['CardLayout']->getChildren() as $child) {
            if ($linktype != $child->getName()) {
                unset($this->fields['CardLayout']->fields[$child->getName()]);
            }
        }
        return parent::validate($postData);
    }
}
