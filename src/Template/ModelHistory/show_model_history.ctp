<ul>
    <?php foreach ($modelHistory as $history): ?>
            <li>
                <?= $this->ModelHistory->historyText($history) ?>
            </li>
    <?php endforeach; ?>
</ul>
