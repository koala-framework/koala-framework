<?p
/
 * @package V
 * @subpackage Componen
 
class Vpc_TagCloud_Component extends Vpc_Abstra

    public function getTemplateVars
   
        $db = $this->getDao()->getDb('beyars'
        $select = $db->select
            ->from(array('wr' => 'index_words_ranking'
                   array('word_id', 'ROUND(fullWeight) AS fullWeight', 'anzahlVorkommen'
            ->join(array('w' => 'index_words'
                   'w.id = wr.word_id
                   array('word'
            ->order(array('wr.fullWeight DESC'
            ->limit(100

        $stmt = $db->query($select
        $smRows = $stmt->fetchAll(

    $sortArr = $returnVars = array(

    // Styles in IndexWordRank_cloud.ht
    $styles = arr
      (	1 => 'wordrank_1
        2 => 'wordrank_2
        3 => 'wordrank_2
        4 => 'wordrank_3
        5 => 'wordrank_

      

      $min_weight_durch = 100000
      $max_weight_durch = 

    for($i=0; $i<count($smRows); $i+
   
      $weight_durch = $smRows[$i]["fullWeight"] / $smRows[$i]["anzahlVorkommen"
      $smRows[$i]["weight_durch"] = $weight_durc

      if($min_weight_durch > $weight_durch) $min_weight_durch = $weight_durc
      if($max_weight_durch < $weight_durch) $max_weight_durch = $weight_durc

      $sortArr[$i] = $smRows[$i]["word"
   

    $oneRangePart = ceil(($max_weight_durch - $min_weight_durch) / count($styles)

    if(is_array($sortArr) && !empty($sortArr
   
      asort($sortArr
      foreach($sortArr as $i => $wor
     
        // Style setz
        $st_i = 
        while($smRows[$i]["weight_durch"] > $min_weight_durch + ($st_i * $oneRangePart)
       
          $st_i+
       
        $smRows[$i]["style"] = $styles[$st_i
        $smRows[$i]["a_word"] = rawurlencode($smRows[$i]["word"]

        $returnVars[] = $smRows[$i
     
   

        $ret = parent::getTemplateVars($mode
        $ret['data'] = $returnVar
         $ret['template'] = 'TagCloud.html
         return $re
   

