<?php
class Kwc_Basic_Text_Admin extends Kwc_Admin
{
    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
        if (!$source->getComponent()->getModel()->getRow($source->dbId)) {
            //falls es nur eine nicht-vorhandene standard-row gibt müssen wir gar nichts tun
            return;
        }
        if ($source->dbId == $target->dbId) {
            return;
        }

        $idMap = array();

        foreach ($source->getChildComponents(array('inherit' => false)) as $c) {
            $newChild = $c->generator->duplicateChild($c, $target);
            if ($c->generator instanceof Kwc_Basic_Text_Generator) {
                $idMap[$c->dbId] = $newChild;
            }
        }
        $content = '';
        $row = $source->getComponent()->getRow();
        foreach ($row->getContentParts() as $p) {
            if (!is_string($p) && ($p['type'] == 'image' || $p['type'] == 'link' || $p['type'] == 'download')) {
                $componentId = $row->component_id.'-'.substr($p['type'], 0, 1).$p['nr'];
            }
            if (is_string($p)) {
                $content .= $p;
            } else if ($p['type'] == 'image' && isset($idMap[$componentId])) {
                $imageComponent = $idMap[$componentId]->getComponent();
                $dimension = $imageComponent->getImageDimensions();
                $content .= "<img src=\"".$imageComponent->getImageUrl()."\" ".
                            "width=\"$dimension[width]\" ".
                            "height=\"$dimension[height]\" />";
            } else if (($p['type'] == 'link' || $p['type'] == 'download') && isset($idMap[$componentId])) {
                $content .= "<a href=\"".$idMap[$componentId]->dbId."\">";
            }
        }

        $source->getComponent()->getRow()->duplicate(array(
            'component_id' => $target->dbId,
            'content'      => $content
        ));
    }

    public function componentToString(Kwf_Component_Data $data)
    {
        $truncate = new Kwf_View_Helper_Truncate;
        $ret = $truncate->truncate($data->getComponent()->getSearchContent(),15);
        return $ret;
    }
}
