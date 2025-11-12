<?php
// Ziel-URL ermitteln
$href = '#';
$label = 'Neu';
if (isset($create) && is_array($create) && !empty($create['href'])) {
  $href  = (string)$create['href'];
  $label = $create['label'] ?? $label;
} else {
  // aus aktueller Listenseite ableiten: subjects.php -> actions/subjects/add.php
  $script = basename($_SERVER['PHP_SELF'] ?? '');
  if (preg_match('/^([a-z_]+)\.php$/i', $script, $m)) {
    $slug = $m[1];
    $href = "actions/{$slug}/add.php";
  }
}
?>
<a href="<?= htmlspecialchars($href) ?>"
  class="w-8 h-8 flex items-center justify-center ml-auto font-bold text-cyan-400  border border-cyan-400 rounded cursor-pointer hover:bg-cyan-50 dark:hover:bg-cyan-900/30 transition"
  title="<?= htmlspecialchars($label) ?>">+</a>