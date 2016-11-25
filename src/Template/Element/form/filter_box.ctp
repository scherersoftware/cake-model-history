<?php
use ModelHistory\Model\Entity\ModelHistory;
?>
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
                                'options' => $searchableFields,
                                'empty' => __d('model_history', 'select.choose')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->input('search.context_type', [
                            'type' => 'select',
                            'label' => __d('model_history', 'search.context_type'),
                            'options' => ModelHistory::getContextTypes(),
                            'empty' => __d('model_history', 'select.choose')
                        ]) ?>
                    </div>
                    <?php if (!empty($contexts)): ?>
                        <div class="col-md-6">
                            <?= $this->Form->input('search.context_slug', [
                                'type' => 'select',
                                'label' => __d('model_history', 'search.context_slug'),
                                'options' => $contexts,
                                'empty' => __d('model_history', 'select.choose')
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->input('search.date.from', [
                            'type' => 'date',
                            'label' => __d('model_history', 'search.date_from'),
                            'empty' => true
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->input('search.date.to', [
                            'type' => 'date',
                            'label' => __d('model_history', 'search.date_to'),
                            'empty' => true
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->submit(__d('model_history', 'submit_filter'), [
                            'class' => 'btn btn-xs btn-primary submit-btn'
                        ]); ?>
                        <?= $this->Form->button(__d('model_history', 'reset_filter'), [
                            'class' => 'btn btn-xs reset-btn'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    <?= $this->Form->end(); ?>
</div>
