<table class="table">
    <thead>
        <tr>
            <th>Revision</th>
            <th>Date</th>
            <th>User</th>
            <th>Action</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($entity->model_history as $entry): ?>
            <tr>
                <td><?= $entry->revision ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?php debug($entity->model_history) ?>