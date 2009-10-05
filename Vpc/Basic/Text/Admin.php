<?php
class Vpc_Basic_Text_Admin extends Vpc_Admin
{
    public function setup()
    {
        //TODO: kann das ned einfach für alle unterkomponenten gemacht werden?!
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        if ($generators['child']['component']['link']) {
            Vpc_Admin::getInstance($generators['child']['component']['link'])->setup();
        }
        if ($generators['child']['component']['image']) {
            Vpc_Admin::getInstance($generators['child']['component']['image'])->setup();
        }
        if ($generators['child']['component']['download']) {
            Vpc_Admin::getInstance($generators['child']['component']['download'])->setup();
        }

        $fields['content'] = 'text NOT NULL';
        $this->createFormTable('vpc_basic_text', $fields);
    }

    public function duplicate($source, $target)
    {
        if (!$source->getComponent()->getModel()->getRow($source->dbId)) {
            //falls es nur eine nicht-vorhandene standard-row gibt müssen wir gar nichts tun
            return;
        }

        $idMap = array();

        foreach ($source->getChildComponents(array('inherit' => false)) as $c) {
            $newChild = $c->generator->duplicateChild($c, $target);
            if ($c->generator instanceof Vpc_Basic_Text_Generator) {
                $idMap[$c->dbId] = $newChild;
            }
        }
        $content = '';
        foreach ($source->getComponent()->getRow()->getContentParts() as $p) {
            if (is_string($p)) {
                $content .= $p;
            } else if ($p['type'] == 'image') {
                $imageComponent = $idMap[$source->dbId.'-'.substr($p['type'], 0, 1).$p['nr']]->getComponent();
                $dimension = $imageComponent->getImageDimensions();
                $content .= "<img src=\"".$imageComponent->getImageUrl()."\" ".
                            "width=\"$dimension[width]\" ".
                            "height=\"$dimension[height]\" />";
            } else if (($p['type'] == 'link' || $p['type'] == 'download')) {
                $content .= "<a href=\"".$idMap[$source->dbId.'-'.substr($p['type'], 0, 1).$p['nr']]->dbId."\">";
            }
        }

        $source->getComponent()->getRow()->duplicate(array(
            'component_id' => $target->dbId,
            'content'      => $content
        ));
    }
}
