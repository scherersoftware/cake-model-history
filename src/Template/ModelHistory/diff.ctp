<div id="diff-output">
    <?php if (empty($diffOutput)): ?>
        <div class="alert alert-info"><?=  __d('model_history', 'no_diff'); ?></div>
    <?php else: ?>
        <table class="table">
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
