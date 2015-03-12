<?= $this->Flash->render() ?>
<div class="input-group comment-box">
    <input type="text" class="form-control comment-box-content" placeholder="<?= __d('model_history', 'save')?>">
    <span class="input-group-btn">
        <button class="btn btn-success comment-box-save" type="button">save</button>
    </span>
</div><!-- /input-group -->
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
<ul class="pager">
    <?= $this->Paginator->prev('<<') ?>
    <?= $this->Paginator->next('>>') ?>
<ul>