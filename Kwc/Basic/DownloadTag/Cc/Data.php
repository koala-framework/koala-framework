<?php
class Kwc_Basic_DownloadTag_Cc_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            //not own_download but still output thru own url to be ablte to call own isValidMediaOutput
            $row = $this->chained->getComponent()->getRow();
            if (!$row) return null;
            $fRow = $row->getParentRow('File');
            if (!$fRow) return null;
            $filename = $row->filename;
            if (!$filename) {
                $filename = $fRow->filename;
            }
            $filename .= '.'.$fRow->extension;
            return Kwf_Media::getUrl($this->componentClass, $this->componentId, 'default', $filename);
        } else if ($var == 'rel') {
            return $this->chained->rel;
        } else {
            return parent::__get($var);
        }
    }
}
