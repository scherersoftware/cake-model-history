<ul>
    <?php foreach ($modelHistory as $history): ?>
        <div class="media">
            <a class="pull-left" href="#">
                <img class="media-object img-circle" src="//placehold.it/28x28" alt="">
            </a>
            <div class="media-body">
                <?= $this->ModelHistory->historyText($history) ?><br>
                <small class="text-muted"><?= h($history->created) ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</ul>