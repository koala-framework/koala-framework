<?php
/**
 * For playing videos from community video services like YouTube or Vimeo
 */
class Kwc_Advanced_CommunityVideo_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Media.Community Video'),
            'ownModel'     => 'Kwc_Advanced_CommunityVideo_Model',
            'extConfig' => 'Kwf_Component_Abstract_ExtConfig_Form'
        ));
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'url';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $ret['row'];
        $ret['url'] = self::getVideoUrl($row);
        if ($row->size === 'fullWidth') {
            $ret['config'] = array(
                'fullWidth' => true,
                'ratio' => $row->dimensions
            );
        }
        return $ret;
    }

    public static function getVideoUrl($row)
    {
        $url = $row->url;
        if (!empty($url)) {
            $urlParts = parse_url($url);
            if ($urlParts && !empty($urlParts['host'])) {
                if (preg_match('/youtube\.com$/i', $urlParts['host'])) {
                    parse_str($urlParts['query'], $queryParts);
                    $url = '//www.youtube.com/embed/'.$queryParts['v'];
                    if (!$row->show_similar_videos) {
                        $url .= '?rel=0';
                    } else {
                        $url .= '?rel=1';
                    }
                    if ($row->autoplay) {
                        $url .= '&autoplay=1';
                    }

                } else if (preg_match('/vimeo\.com$/i', $urlParts['host'])) {
                    $clipId = substr($urlParts['path'], 1);
                    $url = '//player.vimeo.com/video/'.$clipId.'?api=1&player_id=vimeoPlayer';

                    if ($row->autoplay) {
                        $url .= '&autoplay=1';
                    }
                }
            }
        }
        return $url;
    }

    public function hasContent()
    {
        if ($this->getRow()->url) return true;
        return false;
    }
}
