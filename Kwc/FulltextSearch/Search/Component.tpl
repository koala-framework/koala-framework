<div class="<?=$this->cssClass;?>">
    <form action="<?=$this->data->url;?>">
        <input class="query" name="query" value="<?=htmlspecialchars($this->queryString);?>" />
        <input class="submit" type="submit" value="<?=$this->data->trlKwf('Search');?>" />
    </form>
    <? if ($this->error) { ?>
        <p><?=$this->error?></p>
    <? } else if ($this->hits) { ?>
        <div class="resultText webSearchSuccess">
            <p>
                <?=$this->data->trlKwf('<strong>Found Results</strong> (in {0} seconds)', round($this->queryTime, 2));?>
                <?=$this->data->trlKwf('{0}-{1} of about {2}', array($this->numStart, $this->numEnd, $this->hitCount));?>
            </p>
        </div>
        <?=$this->component($this->paging);?>
        <ul class="resultList">
            <? $h=0; foreach ($this->hits as $hit) { ?>
                <?
                $class = '';
                if($h==0) $class = 'first ';
                if($h==count($this->hits)-1) $class = 'last ';
                ?>
                <li class="<?=trim($class);?>">
                    <?=$this->componentLink($hit['data'],$this->highlightTerms($this->queryParts, $hit['data']->name));?>
                    <span class="preview"><?=$this->highlightTerms($this->queryParts, $hit['content']);?></span>
                </li>
            <? $h++; } ?>
        </ul>
        <?=$this->component($this->paging)?>
    <? } else if (trim($this->queryString)) { ?>
        <div class="resultText webSearchError">
            <p><?=$this->data->trlKwf('No results found for <strong>"{0}"</strong>',htmlspecialchars($this->queryString));?></p>
        </div>
    <? } else { ?>
        <div class="resultText webSearchError">
            <p><?=$this->data->trlKwf('Please enter a search term');?></p>
        </div>
    <? } ?>
    <div class="webSearchHelper">
        <p><?=$this->placeholder['helpFooter']?></p>
    </div>
</div>