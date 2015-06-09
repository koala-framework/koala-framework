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
        $ret['url'] = self::getVideoUrl($row->url, $row);
        $ret['config'] = array(
            'ratio' => $row->ratio,
        );
        if ($row->size === 'fullWidth') {
            $ret['config']['fullWidth'] = true;
        }
        return $ret;
    }

    public static function getVideoUrl($url, $settingsRow)
    {
        $ret = false;
        $url = $settingsRow->url;
        if (!empty($url)) {
            $urlParts = parse_url($url);
            if ($urlParts && !empty($urlParts['host'])) {
                if (preg_match('/youtube\.com$/i', $urlParts['host'])) {
                    parse_str($urlParts['query'], $queryParts);
                    if (isset($queryParts['v'])) {
                        $ret = '//www.youtube.com/embed/'.$queryParts['v'];
                        if (!$settingsRow->show_similar_videos) {
                            $ret .= '?rel=0';
                        } else {
                            $ret .= '?rel=1';
                        }
                        if ($settingsRow->autoplay) {
                            $ret .= '&autoplay=1';
                        }

                        $ret.= '&wmode=transparent';
                    }
                } else if (preg_match('/vimeo\.com$/i', $urlParts['host'])) {
                    $clipId = substr($urlParts['path'], 1);
                    $ret = '//player.vimeo.com/video/'.$clipId.'?api=1&player_id=vimeoPlayer';

                    if ($settingsRow->autoplay) {
                        $ret .= '&autoplay=1';
                    }
                }
            }
        }
        return $ret;
    }

    public function hasContent()
    {
        $row = $this->getRow();
        return (bool)self::getVideoUrl($row->url, $row);
    }
}
