<div class="<?=$this->rootElementClass ?>">
    <ul>
        <?php foreach ($this->posts as $post) { ?>
        <li><?= $this->componentLink($post, $post->linktext) ?></li>
        <?php } ?>
    </ul>
</div>
