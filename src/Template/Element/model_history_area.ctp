<?php
use Cake\Routing\Router;
?>
<div class="model-history-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="model-history-area table-responsive" data-model="<?= $model ?>" data-foreignKey="<?= $foreignKey ?>" data-limit="<?= $limit ?>" data-page="<?= $page ?>">
                <?= $this->element('ModelHistory.form/filter_box', compact('searchableFields')) ?>
                <?= $this->element('ModelHistory.form/comment_box') ?>
                <?php if (empty($modelHistory)): ?>
                    <div class="alert alert-info"><?=  __d('model_history', 'no_history'); ?></div>
                <?php else: ?>
                    <table class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Felder</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <?= $this->element('ModelHistory.model_history_rows', compact('modelHistory', 'showPrevEntriesButton', 'showNextEntriesButton', 'page')) ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
