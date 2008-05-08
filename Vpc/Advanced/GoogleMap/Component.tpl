<div class="vpcGoogleMap">

    <div class="text"><?php $this->component($this->text) ?><br /></div>
    <div class="options"><?= $this->options ?></div>

    <div class="container"></div>

    <form action="#" class="fromAddress">
        <input type="text" class="textBefore"/>
        <input type="submit" value="<?= trlVps('Show Route') ?>" class="submitOn"/>
    </form>

    <div class="mapDirSuggestParent">
        <b><?= trlVps('Suggestions') ?></b>
        <ul class="mapDirSuggest"></ul>
    </div>

    <div class="mapDir"></div>
</div>

