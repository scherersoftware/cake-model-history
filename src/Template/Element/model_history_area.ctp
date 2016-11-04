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
                    <?= $this->element('ModelHistory.model_history_rows', compact('modelHistory')) ?>
                    <?php if ($showMoreEntriesButton): ?>
                        <tr>
                            <td colspan="3">
                                <a href="#" class="load-more-history" data-page="<?= $page ?>" data-model="<?= $model ?>" data-id="<?= $id ?>" data-limit="<?= $limit ?>">Mehr Einträge laden...</a>
                            </tr>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
