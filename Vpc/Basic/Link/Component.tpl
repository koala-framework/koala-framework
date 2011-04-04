<?=$this->component($this->linkTag)?>

    <?=$this->mailEncodeText($this->text)?>

<?if ($this->hasContent($this->linkTag)) {?>
</a>
<?}?>
