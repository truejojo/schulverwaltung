<?php
function goHomeLink(string $label): void {
  echo '<a href="index.php"
      class="text-lg inline-block px-3 bg-transparent text-gray-500 dark:text-gray-300 hover:underline">' . htmlspecialchars($label) . '</a>';
}