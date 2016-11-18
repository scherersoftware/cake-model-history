<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>    </button>
    <h4 class="modal-title">
        <?= __d('model_history', 'diff.title') ?>
    </h4>
</div>
<div class="modal-body">
    <div id="diff-output">
        <?php if (empty($diffOutput)): ?>
            <div class="alert alert-info"><?=  __d('model_history', 'no_diff'); ?></div>
        <?php else: ?>
            <table class="table">
                <tr>
                    <th><?= __d('model_history', 'diff.fieldname') ?></th>
                    <th><?= __d('model_history', 'diff.old_value') ?></th>
                    <th><?= __d('model_history', 'diff.new_value') ?></th>
                </tr>
                <?php foreach ($diffOutput as $type => $content): ?>
                    <?php if ($type == 'changed'): ?>
                        <?php foreach ($content as $fieldName => $data): ?>
                            <tr class="changed">
                                <td><?= $fieldName ?></td>
                                <td><?= $data['old'] ?></td>
                                <td><?= $data['new'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($type == 'changedBefore'): ?>
                        <?php foreach ($content as $fieldName => $data): ?>
                            <tr class="changed-before">
                                <td><?= $fieldName ?></td>
                                <td><?= $data['old'] ?></td>
                                <td><?= $data['new'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($type == 'unchanged'): ?>
                        <?php foreach ($content as $fieldName => $data): ?>
                            <tr class="unchanged">
                                <td><?= $fieldName ?></td>
                                <td class="italic"><?= __d('model_history', 'no_historical_data') ?></td>
                                <td><?= $data ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>
