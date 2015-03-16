<?= $this->Flash->render() ?>
<div class="model-history form">
    <?= $this->Form->create(null, ['horizontal' => true, 'novalidate']); ?>
        <div class="input-group">
            <input type="text" class="form-control" name="data" id="data "placeholder="Enter comment...">
            <span class="input-group-btn">
                <button class="btn btn-success" type="submit">Save</button>
            </span>
        </div>
    <?= $this->Form->end() ?>
</div>
<hr>
<?php foreach($modelHistory as $entry): ?>
    <div class="media">
        <a class="pull-left" href="#">
            <img class="media-object img-circle" src="//placehold.it/28x28" alt="">
        </a>
        <div class="media-body">
            <?= $this->ModelHistory->historyText($entry) ?><br>
            <?php if (isset($entry->data['comment'])) : ?>
                <?php foreach ($entry->data as $data) : ?>
                   <p><?= $data ?></p>
                 <?php endforeach; ?>
             <?php endif; ?>
            <small class="text-muted"><?= h($entry->created) ?></small>
        </div>
    </div>
<?php endforeach; ?>
<ul class="pager">
    <?= $this->Paginator->prev('<<') ?>
    <?= $this->Paginator->next('>>') ?>
<ul>