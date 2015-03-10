<?php if (empty($modelHistory)): ?>
    <div class="alert alert-info"><?= __('model_history.no_history') ?></div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('user.lastname', __('contact_person.name')) ?></th>
                <th><?= $this->Paginator->sort('action', __('model_history.action')) ?></th>
                <th><?= $this->Paginator->sort('data', __('model_history.data')) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modelHistory as $history): ?>
                <tr>
                    <td><?= h($history->user->full_name) ?></td>
                    <td><?= h($history->action) ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    </div>
<?php endif; ?>


