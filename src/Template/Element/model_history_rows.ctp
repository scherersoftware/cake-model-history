<?php foreach($modelHistory as $historyEntry): ?>
    <tr>
        <td class="action-label <?= $this->ModelHistory->actionClass($historyEntry->action) ?>" data-toggle="popover" data-content="<?= $this->ModelHistory->historyText($historyEntry) ?>">
            <?= $this->ModelHistory->historyBadge($historyEntry) ?>
            <?= $historyEntry->created->format('d.m.Y, H:i') ?> |
            <?= isset($historyEntry->user) ? $historyEntry->user->full_name : 'Anonymous' ?>
            <?php if (!empty($historyEntry->context)): ?>
                 | <a href="#">Passwort vergessen</a>
            <?php endif; ?>
        </td>
        <td class="fields">
            <?= join(', ', array_keys($historyEntry->data)) ?> |
            <?= $this->CkTools->displayStructuredData($historyEntry->data, [
                'type' => 'table',
                'class' => 'fields-table',
                'expanded' => false
            ]) ?>
        </td>
        <td><a href="#" class="btn btn-xs btn-primary">Diff</a></td>
    </tr>
<?php endforeach; ?>
