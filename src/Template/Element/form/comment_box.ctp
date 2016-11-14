<div class="model-history-comment">
    <div class="flash">
        <?= $this->Flash->render() ?>
    </div>
    <?= $this->Form->create(); ?>
        <div class="input-group">
            <input type="text" class="form-control" name="data" id="data "placeholder="Enter comment...">
            <span class="input-group-btn">
                <button class="btn btn-success" type="submit">Save</button>
            </span>
        </div>
    <?= $this->Form->end(); ?>
</div>
