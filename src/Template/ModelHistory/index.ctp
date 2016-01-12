<?php if ($modelHistory->count() == 0) : ?>
    <div class="alert alert-info"><?=  __d('model_history', 'no_history'); ?></div>
<?php else: ?>
    <?= $this->Flash->render() ?>
    <div class="model-history form">
			<?= $this->Form->create(null); ?>
            <div class="input-group">
                <input type="text" class="form-control" name="data" id="data "placeholder="Enter comment...">
                <span class="input-group-btn">
                    <button class="btn btn-success" type="submit">Save</button>
                </span>
            </div>
        <?= $this->Form->end() ?>
        <hr>
    </div>
    <ul class="timeline">
        <?php foreach($modelHistory as $entry): ?>
            <li>
                <?= $this->ModelHistory->historyBadge($entry) ?>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4 class="timeline-title"><?= $this->ModelHistory->historyText($entry) ?><br></h4>
                        <small class="text-muted"><i class="fa fa-clock-o"></i> <?= h($entry->created) ?></small>
                    </div>
                    <div class="timeline-body">
                        <p>
                        <?php foreach ($entry->data as $field => $data) : ?>
                            <p>
                                <?= h($field) ?>: 
                                <?php if (is_array($data)): ?>
                                    <?= print_r($data, true) ?>
                                <?php else: ?>
                                    <?= h($data) ?>
                                <?php endif; ?>
                            </p>
                        <?php endforeach; ?>
                        </p>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <ul class="pager timeline-pager" data-page="<?= $this->Paginator->current('ModelHistory') ?>">
        <?= $this->Paginator->prev('<<') ?>
        <?= $this->Paginator->next('>>') ?>
    </ul>
<?php endif; ?>