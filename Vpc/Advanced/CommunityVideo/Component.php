<?php
/**
 * For playing videos from community video services like YouTube or Vimeo
 */
class Vpc_Advanced_CommunityVideo_Component extends Vpc_Abstract_Flash_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Flash.Community Video'),
            'ownModel'     => 'Vpc_Advanced_CommunityVideo_Model',
            'extConfig' => 'Vps_Component_Abstract_ExtConfig_Form'
        ));
        return $ret;
    }

    protected function _getFlashData()
    {
        $ret = parent::_getFlashData();

        $url = $this->getRow()->url;
        if (!empty($url)) {
            $urlParts = parse_url($url);

            if ($urlParts && !empty($urlParts['host'])) {
                if (preg_match('/youtube\.com$/i', $urlParts['host'])) {
                    $url = str_replace('/watch?v=', '/v/', $url);
                    if (!$this->getRow()->show_similar_videos) {
                        if (strpos($url, 'rel=0') === false) {
                            $url .= '&rel=0';
                        }
                    }
                } else if (preg_match('/vimeo\.com$/i', $urlParts['host'])) {
                    $clipId = substr($urlParts['path'], 1);
                    $url = 'http://vimeo.com/moogaloop.swf?clip_id='.$clipId.'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1';
                }
            }
        }

        $ret['url'] = $url;
        $ret['width'] = $this->getRow()->width;
        $ret['height'] = $this->getRow()->height;
        $ret['params'] = array(
            'allowfullscreen' => 'true'
        );
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->url) return true;
        return false;
    }
}
