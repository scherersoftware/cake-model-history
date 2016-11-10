<?php
use Cake\Routing\Router;
?>
<div class="row">
    <div class="col-md-12">
        <div class="model-history-area table-responsive">
            <div class="model-history-filter">
                <?= $this->Form->create(); ?>
                    <div class="row">
                        <?php if (!empty($searchableFields)): ?>
                            <div class="col-md-6">
                                <?= $this->Form->input('filter_fields', [
                                    'type' => 'select',
                                    'label' => __d('model_history', 'filter.fields'),
                                    'options' => $searchableFields
                                ]) ?>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6"></div>
                    </div>
                <?= $this->Form->end(); ?>
            </div>
            <div class="model-history form">
                <?= $this->Flash->render() ?>
                <?= $this->Form->create(); ?>
                    <div class="input-group">
                        <input type="text" class="form-control" name="data" id="data "placeholder="Enter comment..." data-model="<?= $model ?>" data-foreignKey="<?= $id ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-success" type="submit">Save</button>
                        </span>
                    </div>
                <?= $this->Form->end(); ?>
            </div>
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
                    <?= $this->element('ModelHistory.model_history_rows', compact('modelHistory', 'showPrevEntriesButton', 'showNextEntriesButton', 'page', 'model', 'id', 'limit')) ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
