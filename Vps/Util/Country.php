<?php
//TODO zusammenführen mit Vps_Util_Model_Countries
class Vps_Util_Country {
    
    const NAME = 'name';
    const CONTINENT = 'continent';
    const CAPITAL = 'capital';
    
    public static function getValue($key, $what = self::NAME)
    {
        $language = Vps_Trl::getInstance()->getTargetLanguage();
        $masterFile = VPS_PATH . '/Vps/Form/Field/SelectCountry/countries.xml';
        $cacheId = "countries_{$language}_{$what}";
        
        $cache = Vps_Cache::factory(
            'File',
            'File',
            array(
               'master_file' => $masterFile,
               'lifetime' => null,
               'automatic_serialization' => true
            ),
            array(
                'cache_dir' => 'application/cache/config'
            )
        );
        $result = $cache->load($cacheId);
        if(!$result) {
            $xml = simplexml_load_file($masterFile);
            $result = array();
            $x = 0;
            foreach ($xml->country as $country) {
                $value = null;
                foreach ($country as $k => $c) {
                    if ($k == $what) {
                        $attributes = $c->attributes();
                        if (isset($attributes['language']) && $attributes['language'] == $language) {
                            $result[(string)$country->iso2] = (string)$c;
                        }
                    }
                }
            }
            $cache->save($result, $cacheId);
        }
        if (isset($result[$key])) return $result[$key];
        return null;
    }
}