<div class="<?=$this->rootElementClass?>">
    <a href="<?=$this->data->row->data->url?>" target="_blank">
        <?=$this->highlightTerms($this->queryParts, $this->linkText);?>
    </a>
    <span class="preview">
        <?=$this->highlightTerms($this->queryParts, $this->data->row->content);?>
    </span>
</div>


