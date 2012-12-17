<div class="<?=$this->cssClass?>">
    <?=$this->componentLink($this->data->row->data, $this->highlightTerms($this->queryParts, $this->data->row->data->name));?>
    <span class="preview"><?=$this->highlightTerms($this->queryParts, $this->data->row->content);?></span>
</div>