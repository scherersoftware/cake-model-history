<?php
use Cake\Utility\Inflector;
?>

<?php foreach($modelHistory as $historyEntry): ?>
    <tr>
        <td class="action-label <?= $this->ModelHistory->actionClass($historyEntry->action) ?>" data-toggle="popover" data-content="<?= $this->ModelHistory->historyText($historyEntry) ?>">
            <?= $this->ModelHistory->historyBadge($historyEntry) ?>
            <?= $historyEntry->created->format('d.m.Y, H:i') ?>
            <?= isset($historyEntry->user) ? '| ' . $historyEntry->user->full_name : '' ?>
            <?php if (!empty($historyEntry->context)): ?>
                 | <?= $this->element('ModelHistory.model_history_context_' . $historyEntry->context['type'], compact('historyEntry')) ?>
            <?php endif; ?>
        </td>
        <td class="fields">
            <?= $this->ModelHistory->getLocalizedFields($historyEntry) ?> |
            <?= $this->CkTools->displayStructuredData($historyEntry->data, [
                'type' => 'table',
                'class' => 'fields-table',
                'expanded' => false,
                'callback' => function ($value) use ($historyEntry) {
                    $ownHistoryEntry = clone $historyEntry;
                    $ownHistoryEntry->data = [
                        $value => true
                    ];
                    return $this->ModelHistory->getLocalizedFields($ownHistoryEntry);
                }
            ]) ?>
        </td>
        <td><a href="#" class="btn btn-xs btn-primary">Diff</a></td>
    </tr>
<?php endforeach; ?>
<?php
