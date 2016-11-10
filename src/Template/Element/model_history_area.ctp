<?php
use Cake\Routing\Router;
use ModelHistory\Model\Entity\ModelHistory;
?>
<div class="row">
    <div class="col-md-12">
        <div class="model-history-area table-responsive">
            <div class="model-history-filter">
                <?= $this->Form->create(); ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <?php if (!empty($searchableFields)): ?>
                                    <div class="col-md-6">
                                        <?= $this->Form->input('filter.fields', [
                                            'type' => 'select',
                                            'label' => __d('model_history', 'filter.fields'),
                                            'options' => $searchableFields
                                        ]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $this->Form->input('search.context_type', [
                                        'type' => 'select',
                                        'label' => __d('model_history', 'search.context_type'),
                                        'options' => ModelHistory::getContextTypes()
                                    ]) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $this->Form->input('search.context_slug', [
                                        'type' => 'text',
                                        'label' => __d('model_history', 'search.context_slug')
                                    ]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $this->Form->input('search.date_from', [
                                        'type' => 'date',
                                        'label' => __d('model_history', 'search.date_from'),
                                        'empty' => true
                                    ]) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $this->Form->input('search.date_to', [
                                        'type' => 'date',
                                        'label' => __d('model_history', 'search.date_to'),
                                        'empty' => true
                                    ]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $this->Form->submit(__d('model_history', 'submit_filter'), [
                                        'class' => 'btn btn-xs btn-primary'
                                    ]); ?>
                                </div>
                            </div>
                        </div>
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
