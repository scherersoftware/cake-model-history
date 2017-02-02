<?php
$slug = $historyEntry->context_slug;
if (!empty($historyEntry->context) && isset($historyEntry->context['namespace'])) {
    $class = new $historyEntry->context['namespace'];
    $typeDescriptions = $class::typeDescriptions();
    if (isset($typeDescriptions[$slug])) {
        $slug = $typeDescriptions[$slug];
    }
}
?>
<?= $slug ?>
