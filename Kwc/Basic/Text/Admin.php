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

        $row = $source->getComponent()->getRow();

        //get existing component ids based on content parts to only duplicate those
        $existingComponentIds = array();
        foreach ($row->getContentParts() as $p) {
            if (!is_string($p) && ($p['type'] == 'image' || $p['type'] == 'link' || $p['type'] == 'download')) {
                $componentId = $row->component_id.'-'.substr($p['type'], 0, 1).$p['nr'];
                $existingComponentIds[] = $componentId;
            }

        }

        $idMap = array();
        foreach ($source->getChildComponents(array('inherit' => false)) as $c) {
            if ($c->generator instanceof Kwc_Basic_Text_Generator) {
                if (in_array($c->dbId, $existingComponentIds)) {
                    $newChild = $c->generator->duplicateChild($c, $target);
                    $idMap[$c->dbId] = $newChild;
                }
            } else {
                $c->generator->duplicateChild($c, $target);
            }
        }

        $content = '';
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


        $targetRow = $source->getComponent()->getOwnModel()->getRow($target->dbId);
        if ($targetRow) { $targetRow->delete(); }
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

    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $ownRow = $cmp->getComponent()->getRow();
        $defaultText = Kwc_Abstract::getSetting($cmp->componentClass, 'defaultText');
        if ($cmp->hasContent() && strip_tags($ownRow->content) != strip_tags($defaultText)) {
            $ret['content'] = $ownRow->content;
        }
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $ownRow = $cmp->getComponent()->getRow();
        if (isset($data['content'])) {
            $ownRow->content = $data['content'];
        }
        $ownRow->save();
    }

}
