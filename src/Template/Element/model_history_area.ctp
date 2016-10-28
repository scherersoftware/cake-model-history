<?php
use Cake\Routing\Router;
?>
<div class="row">
    <div class="col-md-12">
        <div class="model-history-area table-responsive">
            <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Felder</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach($modelHistory as $historyEntry): ?>
                        <tr>
                            <td class="action-label success" data-toggle="popover" data-content="Hinzugefügt">
                                <?= $this->ModelHistory->historyBadge($historyEntry) ?>
                                <?= $historyEntry->created->format('d.m.Y, H:i') ?><br />
                                <?= isset($historyEntry->user) ? $historyEntry->user->full_name : 'Anonymous' ?><br />
                                <!-- <?php if (!empty($historyEntry->context)): ?>
                                <?php endif; ?> -->
                                <!-- <a href="#">Passwort vergessen</a> -->
                            </td>
                            <td class="fields">
                                Geänderte Felder:<br /> <?= join(', ', array_keys($historyEntry->data)) ?><br /><br />
                                <?= $this->CkTools->displayStructuredData($historyEntry->data, [
                                    'type' => 'table',
                                    'class' => 'fields-table',
                                    'expanded' => false
                                ]) ?>
                            </td>
                            <td><a href="#" class="btn btn-primary">Diff</a></td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
