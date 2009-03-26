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
            $data['description'] = (string)$xml->description;
            if ($xml->pubDate) {
                $date = (string)$xml->pubDate;
            } else {
                $date = (string)$xml->children('http://purl.org/dc/elements/1.1/')->date;
            }
        } else {
            $data['link'] = (string)$xml->link['href'];
            $data['description'] = (string)$xml->summary;
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
        $config['data'] = $data;

        parent::__construct($config);
    }
}
