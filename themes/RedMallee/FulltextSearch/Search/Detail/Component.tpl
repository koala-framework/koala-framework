<div class="<?=$this->cssClass?>">
    <a href="<?=$this->data->row->data->url?>">
        <?=$this->highlightTerms($this->queryParts, $this->data->row->data->name);?>
    </a>
    <div class="path"><?=$this->pagePath?></div>
    <span class="preview"><?=$this->highlightTerms($this->queryParts, $this->data->row->content);?></span>
</div>
