<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <h3 class="panel-title pull-left"><?= __d('model_history', 'model_history'); ?></h3>
        <div class="pull-right">
            <a class="btn btn-xs model-history-toggle-btn"><i class="fa fa-plus"></i></a>
        </div>
    </div>
    <div class="panel-body model-history-list" data-repository="<?= $data['repository'] ?>" data-id="<?= $data['id'] ?>" data-comment-box="<?= $data['comment-box'] ?>">
    </div>
</div>
