<div class="<?=$this->cssClass?>">
    <h4><?=$this->data->trlKwf('Articles required for reading');?></h4>
    <?=$this->data->trlKwf('{0} articles are not read but required for reading - please read!', array($this->count));?>
    <?=$this->componentLink($this->data, $this->data->trlKwf('Mark as read'), array('get' => array('read' => $this->article->id)))?>
    <?=$this->component($this->article)?>
</div>