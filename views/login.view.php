<div class="max-w-xl mx-auto my-10 px-4">
  <h1 class="text-3xl font-bold text-center mb-8 text-gray-900 dark:text-gray-100">
    <?= htmlspecialchars($title ?? 'Login') ?>
  </h1>

  <div class="p-6 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
    <form action="login.php" method="POST" class="space-y-5">
      <div>
        <label for="email" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Email</label>
        <input class="w-full p-2.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400" type="text"
          name="email" id="email" autocomplete="email">
      </div>

      <div>
        <label for="password" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Passwort</label>
        <input class="w-full p-2.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900
                 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400" type="password"
          name="password" id="password" autocomplete="current-password">
      </div>

      <div class="pt-2">
        <button type="submit" class="w-full py-2.5 px-4 rounded-md border-2 border-orange-400/60
         bg-white dark:bg-gray-900
         text-orange-700 dark:text-orange-300
         hover:bg-orange-50 dark:hover:bg-gray-800
         active:bg-orange-100 dark:active:bg-gray-700
         focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 dark:focus:ring-offset-gray-900
         font-medium tracking-wide transition-colors">
          Login
        </button>
      </div>
    </form>
  </div>
</div>