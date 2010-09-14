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
                $data['link'] = (string)$xml->guid;
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
                if ((string)$l['type'] == 'text/html') $quality+=2;
                if ((string)$l['type'] == 'application/atom+xml') $quality--;
                if (!(string)$l['type']) $quality++;
                if ((string)$l['rel'] == 'alternate') $quality++;
                if ((string)$l['rel'] == 'enclosure') $quality--;
                if (substr((string)$l['type'], 0, 6) == 'image/') $quality--;
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
                if (!$data['description'] || $i['type'] == 'html' || $i['type'] == 'xhtml') {
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
        } else if ($xml->guid && $xml->guid != $feed->link) {
            $data['id'] = (string)$xml->guid;
        } else {
            $data['id'] = $data['link'];
        }

        $data['author_name'] = null;
        if (isset($xml->children('http://posterous.com/help/rss/1.0')->author->displayName)) {
            $data['author_name'] = (string)$xml->children('http://posterous.com/help/rss/1.0')->author->displayName;
        } else if (isset($xml->author->name)) {
            $data['author_name'] = (string)$xml->author->name;
        } else if (isset($xml->author)) {
            $data['author_name'] = (string)$xml->author;
        } else if (isset($xml->children('http://purl.org/dc/elements/1.1/')->creator)) {
            $data['author_name'] = (string)$xml->children('http://purl.org/dc/elements/1.1/')->creator;
        }

        if ($xml->children('http://purl.org/rss/1.0/modules/content/')->encoded) {
            $data['content_encoded'] = (string)$xml->children('http://purl.org/rss/1.0/modules/content/')->encoded;
        }

        $data['media_image'] = null;
        if ($xml->children('http://search.yahoo.com/mrss/')->content) {
            $attributes = $xml->children('http://search.yahoo.com/mrss/')->content->attributes();
            if (substr((string)$attributes['type'], 0, 6) == 'image/') {
                $data['media_image'] = (string)$attributes['url'];
                $data['media_image_width'] = (string)$attributes['width'];
                $data['media_image_height'] = (string)$attributes['height'];
            }
        }
        if ($xml->children('http://search.yahoo.com/mrss/')->thumbnail) {
            $attributes = $xml->children('http://search.yahoo.com/mrss/')->thumbnail->attributes();
            $data['media_thumbnail'] = (string)$attributes['url'];
            $data['media_thumbnail_width'] = (string)$attributes['width'];
            $data['media_thumbnail_height'] = (string)$attributes['height'];
        }
        if (!$data['media_image']) {
            foreach ($xml->link as $link) {
                if ((string)$link['rel'] == 'enclosure' && substr((string)$link['type'], 0, 6)=='image/') {
                    $data['media_image'] = (string)$link['href'];
                    break;
                }
            }
        }
        if (!$data['media_image']) {
            foreach ($xml->enclosure as $enclosure) {
                if (substr((string)$enclosure['type'], 0, 6)=='image/') {
                    $data['media_image'] = (string)$enclosure['url'];
                    break;
                }
            }
        }

        $config['data'] = $data;

        parent::__construct($config);
    }
}
