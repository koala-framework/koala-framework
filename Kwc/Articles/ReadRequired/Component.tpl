<div class="<?=$this->cssClass?>">
    <h4><?=$this->data->trl('Lesepflichtige Artikel');?></h4>
    Sie haben <?=$this->count?> ungelesene aber lesepflichtige Artikel - Bitte lesen!
    <?=$this->componentLink($this->data, 'Als gelesen markieren', array('get' => array('read' => $this->article->id)))?>
    <?=$this->component($this->article)?>
</div>