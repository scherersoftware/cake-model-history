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
                    <tr>
                        <td colspan="3" class="comment-row">
                            <?= $this->Form->create(); ?>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="data" id="data "placeholder="Enter comment...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-success" type="submit">Save</button>
                                    </span>
                                </div>
                            <?= $this->Form->end(); ?>
                        </td>
                    </tr>
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
