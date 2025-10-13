<table class="min-w-full text-left text-sm">
  <thead class="text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wide dark:bg-gray-900">
    <tr>
      <th class="px-4 py-3 font-semibold">Nr</th>
      <?php foreach ($columns as $col): ?>
        <th class="px-4 py-3 font-semibold border-l border-2-gray-300 dark:border-gray-600">
          <?= htmlspecialchars($col['label']) ?>
        </th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody class="divide-y">
    <?php foreach ($rows as $i => $r): ?>
      <tr class="transition <?= $i % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700/50 hover:dark:bg-gray-500/50' : 'bg-gray-150 dark:bg-gray-800/50 
       hover:dark:bg-gray-500/50' ?>">
        <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200 w-[20px]">
          <?= htmlspecialchars((string) ($i + 1)) ?>
        </td>
        <?php foreach ($columns as $col): ?>
          <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">
            <?= htmlspecialchars((string) ($r[$col['field']] ?? '')) ?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>