<?php /** @var bool $isAuthenticated */ ?>

<div
  class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid gap-8 justify-items-center place-items-center min-h-[60vh]">
  <h1 class="text-gray-600 dark:text-gray-300 text-4xl lg:text-5xl text-center"><?php echo $title; ?></h1>

  <?php if (!$isAuthenticated): ?>
  <div class="rounded-lg bg-gray-100 dark:bg-gray-800 shadow text-center w-full max-w-md mx-auto">
    <a href="login.php" class="
        block w-full h-full p-5 rounded-md border-2 border-orange-400/60
        bg-white dark:bg-gray-900
        text-orange-700 dark:text-orange-300
        hover:bg-orange-50 dark:hover:bg-gray-800 hover:underline
        active:bg-orange-100 dark:active:bg-gray-700
        focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 dark:focus:ring-offset-gray-900
        font-large uppercase tracking-wider transition-colors">
      Zum Login-Bereich
    </a>
  </div>
  <?php endif; ?>

  <!-- Nur für user die eingeloggt sind!!! -->
  <?php if ($isAuthenticated): ?>
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($entities as $entity): ?>
    <a href="<?php echo $entity['href']; ?>" class="p-5 rounded-lg bg-gray-100 dark:bg-gray-800 shadow">
      <h3 class="font-semibold mb-1"><?php echo $entity['title']; ?></h3>
      <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo $entity['description']; ?></p>
    </a>
    <?php endforeach; ?>

  </div>
  <?php endif; ?>
</div>