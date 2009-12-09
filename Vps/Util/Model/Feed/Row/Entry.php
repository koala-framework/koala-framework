<?php
class Vps_Util_Model_Feed_Row_Entry extends Vps_Model_Row_Data_Abstract
{
    public function __construct($config)
    {
        $xml = $config['xml'];
        $feed = $config['feed'];

        $data = array();
        $data['title'] = (string)$xml->title;

        if ($feed->format == Vps_Util_Model_Feed_Row_Feed::FORMAT_RSS) {
            $data['link'] = (string)$xml->link;
            if (!$data['link']) {
                $isPermaLink = (string)$xml->guid['isPermaLink'];
                if ($isPermaLink == 'true' || $isPermaLink == '1') {
                    $data['link'] = (string)$xml->guid;
                }
            }
            $data['description'] = (string)$xml->description;
            if ($xml->pubDate) {
                $date = (string)$xml->pubDate;
            } else {
                $date = (string)$xml->children('http://purl.org/dc/elements/1.1/')->date;
            }
        } else {
            $links = array();
            foreach ($xml->link as $l) {
                $href = (string)$l['href'];
                if (!$href) continue;
                //es kann mehrere links geben, am liebsten ist uns ein alternate text/html
                //aber wir nehmen auch was anderes
                $quality = 0;
                if ((string)$l['type'] == 'text/html') $quality++;
                if ((string)$l['rel'] == 'alternate') $quality++;
                $links[$href] = $quality;
            }
            arsort($links);
            $links = array_keys($links);
            if (count($links)) {
                $data['link'] = $links[0];
            } else {
                $data['link'] = null;
            }
            $data['description'] = '';
            foreach ($xml->content as $i) {
                if (!$data['description'] || $i['type'] == 'html') {
                    $data['description'] = (string)$i;
                }
            }
            if (!$data['description']) {
                $data['description'] = (string)$xml->summary;
            }
            $date = (string)$xml->updated;
        }
        if (!$date) {
            $date = null;
        } else {
            $date = strtotime($date);
            if (!$date) {
                $date = null;
            } else {
                $date = date('Y-m-d H:i:s', $date);
            }
        }
        $data['date'] = $date;

        if ($xml->id) {
            $data['id'] = (string)$xml->id;
        } else if ($xml->guid) {
            $data['id'] = (string)$xml->guid;
        } else {
            $data['id'] = $data['link'];
        }

        $config['data'] = $data;

        parent::__construct($config);
    }
}
