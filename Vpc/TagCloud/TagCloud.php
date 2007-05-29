<?php
class Vpc_TagCloud extends Vpc_Abstract
{
    public function getTemplateVars($mode)
    {
        $db = $this->getDao()->getDb('beyars');
        $select = $db->select()
            ->from(array('wr' => 'index_words_ranking'),
                   array('word_id', 'ROUND(fullWeight) AS fullWeight', 'anzahlVorkommen'))
            ->join(array('w' => 'index_words'),
                   'w.id = wr.word_id',
                   array('word'))
            ->order(array('wr.fullWeight DESC'))
            ->limit(100);

        $stmt = $db->query($select);
        $smRows = $stmt->fetchAll();

    $sortArr = $returnVars = array();
    
    // Styles in IndexWordRank_cloud.html
    $styles = array
      (	1 => 'wordrank_1',
        2 => 'wordrank_2',
        3 => 'wordrank_2',
        4 => 'wordrank_3',
        5 => 'wordrank_3'
        
      );
    
      $min_weight_durch = 1000000;
      $max_weight_durch = 0;
      
    for($i=0; $i<count($smRows); $i++)
    {
      $weight_durch = $smRows[$i]["fullWeight"] / $smRows[$i]["anzahlVorkommen"];
      $smRows[$i]["weight_durch"] = $weight_durch;
      
      if($min_weight_durch > $weight_durch) $min_weight_durch = $weight_durch;
      if($max_weight_durch < $weight_durch) $max_weight_durch = $weight_durch;
      
      $sortArr[$i] = $smRows[$i]["word"];
    }
    
    $oneRangePart = ceil(($max_weight_durch - $min_weight_durch) / count($styles));
    
    if(is_array($sortArr) && !empty($sortArr))
    {
      asort($sortArr);
      foreach($sortArr as $i => $word)
      {
        // Style setzen
        $st_i = 1;
        while($smRows[$i]["weight_durch"] > $min_weight_durch + ($st_i * $oneRangePart) )
        {
          $st_i++;
        }
        $smRows[$i]["style"] = $styles[$st_i];
        $smRows[$i]["a_word"] = rawurlencode($smRows[$i]["word"]);
        
        $returnVars[] = $smRows[$i];
      }
    }

        $ret = parent::getTemplateVars($mode);
        $ret['data'] = $returnVars;
         $ret['template'] = 'TagCloud.html';
         return $ret;
    }
}
