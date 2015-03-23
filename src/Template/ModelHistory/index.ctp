<?php if (empty($entityContactPersons)): ?>
    <div class="alert alert-info"><?= __d('model_history', 'no_history'); ?></div>
<?php else: ?>
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
                            <?php if (isset($entry->data['comment'])) : ?>
                                <?php foreach ($entry->data as $data) : ?>
                                    <p><?= $data ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <ul class="pager" data-page="<?= $this->Paginator->current('ModelHisotry') ?>">
        <?= $this->Paginator->prev('<<') ?>
        <?= $this->Paginator->next('>>') ?>
    <ul>
<?php endif; ?>