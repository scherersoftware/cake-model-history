<tbody>
    <?php if ($showPrevEntriesButton): ?>
        <tr>
            <td colspan="3">
                <a href="#" class="load-prev-history" data-page="<?= $page ?>"><?=  __d('model_history', 'load_previous'); ?></a>
            </tr>
        </tr>
    <?php endif; ?>
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
                <?= $this->ModelHistory->getLocalizedFieldnames($historyEntry) ?> |
                <?= $this->CkTools->displayStructuredData($historyEntry->data, [
                    'type' => 'table',
                    'class' => 'fields-table',
                    'expanded' => false,
                    'fieldnameCallback' => function ($fieldname) use ($historyEntry) {
                        $ownHistoryEntry = clone $historyEntry;
                        $ownHistoryEntry->data = [
                            $fieldname => true
                        ];
                        return $this->ModelHistory->getLocalizedFieldnames($ownHistoryEntry);
                    }
                ]) ?>
            </td>
            <td class="actions"><a href="#" data-history-id="<?= $historyEntry->id ?>" class="diff-btn btn btn-xs btn-primary"><?= __d('model_history', 'diff.open_diff') ?></a></td>
        </tr>
    <?php endforeach; ?>
    <?php if ($showNextEntriesButton): ?>
        <tr>
            <td colspan="3">
                <a href="#" class="load-next-history" data-page="<?= $page ?>"><?=  __d('model_history', 'load_next'); ?></a>
            </tr>
        </tr>
    <?php endif; ?>
</tbody>
