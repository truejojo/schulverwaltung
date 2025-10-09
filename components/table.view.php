<table class="min-w-full text-left text-sm">
      <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wide">
        <tr>
          <?php foreach ($columns as $col): ?>
          <th class="px-4 py-3 font-semibold">
            <?= htmlspecialchars($col['label']) ?>
          </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
        <?php foreach ($rows as $r): ?>
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
          <?php foreach ($columns as $col): ?>
          <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">
            <?= htmlspecialchars((string)($r[$col['field']] ?? '')) ?>
          </td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>