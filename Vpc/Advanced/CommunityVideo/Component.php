<?php
/**
 * For playing videos from community video services like YouTube or Vimeo
 */
class Vpc_Advanced_CommunityVideo_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Community Video'),
            'componentIcon' => new Vps_Asset('film'),
            'ownModel'     => 'Vpc_Advanced_CommunityVideo_Model'
        ));
        $ret['assets']['dep'][] = 'SwfObject';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $url = $this->getRow()->url;
        $urlParts = parse_url($url);

        if (preg_match('/youtube\.com$/i', $urlParts['host'])) {
            $url = str_replace('/watch?v=', '/v/', $url);
        } else if (preg_match('/vimeo\.com$/i', $urlParts['host'])) {
            $clipId = substr($urlParts['path'], 1);
            $url = 'http://vimeo.com/moogaloop.swf?clip_id='.$clipId.'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1';
        }

        $ret['flashUrl'] = $url;
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->url) return true;
        return false;
    }
}
