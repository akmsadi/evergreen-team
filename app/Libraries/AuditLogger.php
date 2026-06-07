<?php

namespace App\Libraries;

use App\Models\AdminHistoryModel;

class AuditLogger
{
    private AdminHistoryModel $history;
    /**
     * Auto-managed timestamp columns are noise in change history.
     * The history row's own created_at already tells us when the action happened.
     *
     * @var list<string>
     */
    private array $ignoredColumns = ['created_at', 'updated_at'];

    public function __construct()
    {
        $this->history = new AdminHistoryModel();
    }

    public function logChange(string $action, string $tableName, int|string|null $rowId, ?array $oldValues, ?array $newValues): void
    {
        if (is_cli()) {
            return;
        }

        $adminId = session()->get('admin_id');

        if ($adminId === null) {
            return;
        }

        $request = service('request');
        [$oldValues, $newValues] = $this->buildColumnDiff($action, $oldValues, $newValues);

        if ($oldValues === null && $newValues === null) {
            return;
        }

        $this->history->insert([
            'admin_id' => (int) $adminId,
            'admin_username' => (string) session()->get('admin_username'),
            'action' => $action,
            'table_name' => $tableName,
            'row_id' => (string) ($rowId ?? ''),
            'old_values' => $this->encodeValues($oldValues),
            'new_values' => $this->encodeValues($newValues),
            'request_method' => method_exists($request, 'getMethod') ? strtoupper($request->getMethod()) : null,
            'request_path' => method_exists($request, 'getPath') ? $request->getPath() : null,
            'created_at' => date('Y-m-d H:i:s'),
        ], false);
    }

    public function logBulkDelete(string $tableName, array $rows): void
    {
        foreach ($rows as $row) {
            $this->logChange('delete', $tableName, $row['id'] ?? null, $row, null);
        }
    }

    private function encodeValues(?array $values): ?string
    {
        if ($values === null) {
            return null;
        }

        return json_encode($values, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);
    }

    /**
     * @return array{0: ?array<string, mixed>, 1: ?array<string, mixed>}
     */
    private function buildColumnDiff(string $action, ?array $oldValues, ?array $newValues): array
    {
        $filteredOldValues = $this->filterValues($oldValues);
        $filteredNewValues = $this->filterValues($newValues);

        if ($action === 'insert') {
            return [null, $filteredNewValues === [] ? null : $filteredNewValues];
        }

        if ($action === 'delete' && $newValues === null) {
            return [$filteredOldValues === [] ? null : $filteredOldValues, null];
        }

        $changedOldValues = [];
        $changedNewValues = [];
        $keys = array_values(array_unique(array_merge(array_keys($filteredOldValues), array_keys($filteredNewValues))));

        foreach ($keys as $key) {
            $oldValueExists = array_key_exists($key, $filteredOldValues);
            $newValueExists = array_key_exists($key, $filteredNewValues);
            $oldValue = $oldValueExists ? $filteredOldValues[$key] : null;
            $newValue = $newValueExists ? $filteredNewValues[$key] : null;

            if ($oldValueExists === $newValueExists && $oldValue === $newValue) {
                continue;
            }

            if ($oldValueExists) {
                $changedOldValues[$key] = $oldValue;
            }

            if ($newValueExists) {
                $changedNewValues[$key] = $newValue;
            }
        }

        return [
            $changedOldValues === [] ? null : $changedOldValues,
            $changedNewValues === [] ? null : $changedNewValues,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function filterValues(?array $values): array
    {
        if ($values === null) {
            return [];
        }

        foreach ($this->ignoredColumns as $ignoredColumn) {
            unset($values[$ignoredColumn]);
        }

        return $values;
    }
}
