<?php
if (!isset($title) || $title === '') {
  $title = "Schulverwaltung";
}
$isAuthenticated = $isAuthenticated ?? (isset($_SESSION['email']));
$current = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="de" class="h-full">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <!-- Basis-URL für alle relativen Links -->
  <base href="/grundlagen/schulverwaltung/">
  <script>
  // Verhindert Flash: vor Tailwind Klassen setzen
  (function() {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    }
  })();
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
  tailwind.config = {
    darkMode: 'class'
  };
  </script>
  <title><?= htmlspecialchars($title); ?></title>
</head>

<body
  class="flex flex-col min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
  <header class="border-b-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">

      <a href="index.php"
        class="p-2 rounded-md bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">

        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
          stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M2 9l10-5 10 5-10 5L2 9z" />
          <path d="M6 11.5v4c0 .3.2.6.5.8 1.9 1 3.9 1 6 0 .3-.1.5-.4.5-.8v-4" />
        </svg>
      </a>
      <nav class="flex items-center">
        <a href="index.php" class="mr-4 flex items-center gap-1 hover:underline
                <?= $current === 'index.php' ? 'font-semibold' : '' ?>
                text-gray-600 dark:text-gray-600 hover:text-white dark:hover:text-black">
          <svg
            class="w-5 h-5 text-gray-800 dark:text-gray-400 hover:text-gray-400 dark:hover:text-gray-100 transition-colors"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <path d="M3 11.5 12 4l9 7.5" />
            <path d="M6 10v9h4v-5h4v5h4v-9" />
          </svg>
        </a>

        <?php if (!$isAuthenticated): ?>
        <a href="login.php"
          class="mr-4 hover:underline <?= $current === 'login.php' ? 'font-semibold' : '' ?> flex items-center gap-1 text-gray-700 dark:text-gray-300">
          <!-- Login Icon -->
          <svg class="w-5 h-5 text-orange-500 dark:text-orange-300 hover:text-orange-700 dark:hover:text-orange-200"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <path d="M15 3h4a2 2 0 0 1 2 2v3" />
            <path d="M21 16v3a2 2 0 0 1-2 2h-4" />
            <path d="M3 12h13" />
            <path d="M11 8l4 4-4 4" />
          </svg>
        </a>
        <?php else: ?>
        <form action="logout.php" method="post" class="inline">
          <button type="submit" class="mr-4 hover:underline flex items-center gap-1 text-gray-700 dark:text-gray-300">
            <!-- Logout Icon -->
            <svg class="w-5 h-5 text-orange-500 dark:text-orange-300" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
              aria-hidden="true">
              <path d="M9 3H5a2 2 0 0 0-2 2v3" />
              <path d="M3 16v3a2 2 0 0 0 2 2h4" />
              <path d="M21 12H8" />
              <path d="M13 16l-5-4 5-4" />
            </svg>
          </button>
        </form>
        <?php endif; ?>
      </nav>
      <button id="theme-toggle" type="button"
        class="p-2 rounded-md bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
        aria-label="Theme umschalten">
        <!-- Sonne -->
        <svg id="icon-sun" class="hidden w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
          <path
            d="M10 4.5a.75.75 0 01.75.75v1a.75.75 0 01-1.5 0v-1A.75.75 0 0110 4.5zm0 8.5a3.25 3.25 0 100-6.5 3.25 3.25 0 000 6.5zm5.5-3.25a.75.75 0 01.75-.75h1a.75.75 0 010 1.5h-1a.75.75 0 01-.75-.75zm-6.25 5.5a.75.75 0 01.75.75v1a.75.75 0 01-1.5 0v-1a.75.75 0 01.75-.75zm7.03-2.47a.75.75 0 011.06 1.06l-.71.7a.75.75 0 11-1.06-1.06l.71-.7zM4.62 6.32a.75.75 0 001.06-1.06l-.7-.71A.75.75 0 103.92 5.6l.7.71zm9.9-1.06a.75.75 0 001.06 0l.7-.71a.75.75 0 10-1.06-1.06l-.7.71a.75.75 0 000 1.06zm-9.19 9.19a.75.75 0 010 1.06l-.71.7a.75.75 0 11-1.06-1.06l.71-.7a.75.75 0 011.06 0z" />
        </svg>
        <!-- Mond -->
        <svg id="icon-moon" class="hidden w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
          <path d="M17.293 13.293A8 8 0 016.707 2.707 8.001 8.001 0 1017.293 13.293z" />
        </svg>
      </button>
    </div>
  </header>

  <main class="flex-1">
    <?php require isset($_viewFile) ? $_viewFile : ($template ?? 'index') . '.view.php'; ?>
  </main>
  <!--  -->

  <!-- <footer class="mt-12 py-6 text-sm text-gray-500 dark:text-gray-400 bg-transparent"> -->
  <footer class="border-t-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 py-6 text-sm mt-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      &copy; 2025 Schulverwaltung
    </div>
  </footer>

  <script>
  const btn = document.getElementById('theme-toggle');
  const sun = document.getElementById('icon-sun');
  const moon = document.getElementById('icon-moon');

  function syncIcons() {
    const d = document.documentElement.classList.contains('dark');
    if (d) {
      sun.classList.remove('hidden');
      moon.classList.add('hidden');
    } else {
      sun.classList.add('hidden');
      moon.classList.remove('hidden');
    }
  }
  syncIcons();
  btn.addEventListener('click', () => {
    const r = document.documentElement;
    const isDark = r.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    syncIcons();
  });
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
    if (!localStorage.getItem('theme')) {
      e.matches ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove(
        'dark');
      syncIcons();
    }
  });
  </script>
</body>

</html>