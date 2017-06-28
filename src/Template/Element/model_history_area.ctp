<?php
use Cake\Routing\Router;
?>
<div class="model-history-wrapper">
    <div class="row">
        <div class="<?= $columnClass ?>">
            <div
                class="model-history-area"
                data-model="<?= $model ?>"
                data-foreignKey="<?= $foreignKey ?>"
                data-limit="<?= $limit ?>"
                data-page="<?= $page ?>"
                data-show-filter="<?= (int)$showFilterBox ?>"
                data-show-comment="<?= (int)$showCommentBox ?>"
                data-include-associated="<?= (int)$includeAssociated ?>"
            >
                <?php if ($showFilterBox): ?>
                    <?= $this->element('ModelHistory.form/filter_box', compact('searchableFields', 'contexts')) ?>
                <?php endif; ?>
                <?php if ($showCommentBox): ?>
                    <?= $this->element('ModelHistory.form/comment_box') ?>
                <?php endif; ?>
                <?php if (empty($modelHistory)): ?>
                    <div class="alert alert-info"><?=  __d('model_history', 'no_history'); ?></div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?= __d('model_history', 'table.heading.fields') ?></th>
                                    <th class="actions">
                                        <div class="pull-right">
                                            <?= __d('model_history', 'table.heading.actions') ?>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <?= $this->element('ModelHistory.model_history_rows', compact('modelHistory', 'showPrevEntriesButton', 'showNextEntriesButton', 'page', 'model')) ?>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
