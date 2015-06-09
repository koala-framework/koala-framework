<?php
class Kwc_Advanced_Youtube_Update_20150309Legacy00001 extends Kwf_Update
{
    public function postUpdate()
    {
        parent::postUpdate();

        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Advanced_Youtube_Component', array('ignoreVisible' => true));
        foreach ($components as $c) {
            $row = $c->getComponent()->getRow();
            if (!$row->size) {
                if (!$row->videoWidth || $row->videoWidth == '100%') {
                    $row->size = 'fullWidth';
                } else {
                    $row->size = 'custom';
                    $row->video_width = $row->videoWidth;
                }
                unset($row->videoWidth);
                $row->save();
            }
        }
    }
}

