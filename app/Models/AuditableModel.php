<?php

namespace App\Models;

use App\Libraries\AuditLogger;
use CodeIgniter\Model;

abstract class AuditableModel extends Model
{
    protected $beforeUpdate = ['beforeUpdateCallback'];
    protected $afterInsert = ['afterInsertCallback'];
    protected $afterUpdate = ['afterUpdateCallback'];
    protected $beforeDelete = ['beforeDeleteCallback'];
    protected $afterDelete = ['afterDeleteCallback'];

    protected array $fieldLabels = [];
    protected string $logKey = '';

    /**
     * @var list<string>
     */
    protected array $ignoredAuditFields = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * @var list<array<string, array<string, mixed>>>
     */
    private array $auditUpdateSnapshots = [];

    /**
     * @var list<array{snapshots: array<string, array<string, mixed>>, purge: bool}>
     */
    private array $auditDeleteSnapshots = [];

    protected function beforeUpdateCallback(array $data): array
    {
        $ids = $this->normalizeAuditIds($data['id'] ?? null);

        if ($ids !== []) {
            $this->auditUpdateSnapshots[] = $this->fetchSnapshots($ids);
        }

        return $data;
    }

    protected function afterInsertCallback(array $data): array
    {
        if (($data['result'] ?? false) !== true) {
            return $data;
        }

        $rowId = $data['id'] ?? null;

        if ($rowId !== 0 && $rowId !== null && $rowId !== '') {
            $this->auditLogger()->logChange(
                'insert',
                $this->resolveLogKey(),
                $rowId,
                null,
                $this->filterAuditValues($this->fetchSnapshot($rowId))
            );
        }

        return $data;
    }

    protected function afterUpdateCallback(array $data): array
    {
        $snapshots = array_pop($this->auditUpdateSnapshots) ?? [];

        if (($data['result'] ?? false) !== true) {
            return $data;
        }

        foreach ($snapshots as $rowId => $oldValues) {
            $newValues = $this->fetchSnapshot($rowId);
            [$changedOldValues, $changedNewValues] = $this->buildUpdateDiff($oldValues, $newValues);

            if ($changedOldValues === null && $changedNewValues === null) {
                continue;
            }

            $this->auditLogger()->logChange(
                'update',
                $this->resolveLogKey(),
                $rowId,
                $changedOldValues,
                $changedNewValues
            );
        }

        return $data;
    }

    protected function beforeDeleteCallback(array $data): array
    {
        $ids = $this->normalizeAuditIds($data['id'] ?? null);

        if ($ids !== []) {
            $this->auditDeleteSnapshots[] = [
                'snapshots' => $this->fetchSnapshots($ids),
                'purge' => (bool) ($data['purge'] ?? false),
            ];
        }

        return $data;
    }

    protected function afterDeleteCallback(array $data): array
    {
        $payload = array_pop($this->auditDeleteSnapshots) ?? ['snapshots' => [], 'purge' => false];

        if (($data['result'] ?? false) !== true) {
            return $data;
        }

        foreach ($payload['snapshots'] as $rowId => $oldValues) {
            $newValues = $payload['purge'] || ! $this->useSoftDeletes ? null : $this->fetchSnapshot($rowId);

            $this->auditLogger()->logChange(
                'delete',
                $this->resolveLogKey(),
                $rowId,
                $this->filterAuditValues($oldValues),
                $this->filterAuditValues($newValues)
            );
        }

        return $data;
    }

    protected function auditLogger(): AuditLogger
    {
        return new AuditLogger();
    }

    /**
     * @param list<int|string>|null $ids
     *
     * @return list<int|string>
     */
    private function normalizeAuditIds(?array $ids): array
    {
        if ($ids === null) {
            return [];
        }

        return array_values(array_filter($ids, static fn($id): bool => $id !== null && $id !== ''));
    }

    private function fetchSnapshots(array $ids): array
    {
        $snapshots = [];

        foreach ($ids as $id) {
            $snapshot = $this->fetchSnapshot($id);

            if ($snapshot !== null) {
                $snapshots[(string) $id] = $snapshot;
            }
        }

        return $snapshots;
    }

    private function fetchSnapshot(int|string $id): ?array
    {
        return $this->db->table($this->table)
            ->where($this->primaryKey, $id)
            ->get()
            ->getRowArray();
    }

    private function resolveLogKey(): string
    {
        return $this->logKey !== '' ? $this->logKey : $this->table;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function filterAuditValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $trackedFields = $this->trackedAuditFields();
        $filtered = [];

        foreach ($trackedFields as $field) {
            if (array_key_exists($field, $values)) {
                $filtered[$field] = $values[$field];
            }
        }

        return $filtered;
    }

    /**
     * @return list<string>
     */
    private function trackedAuditFields(): array
    {
        $fields = $this->fieldLabels !== [] ? array_keys($this->fieldLabels) : $this->allowedFields;

        return array_values(array_filter(
            $fields,
            fn(string $field): bool => ! in_array($field, $this->ignoredAuditFields, true)
        ));
    }

    /**
     * @return array{0: ?array<string, mixed>, 1: ?array<string, mixed>}
     */
    private function buildUpdateDiff(array $oldValues, ?array $newValues): array
    {
        $filteredOldValues = $this->filterAuditValues($oldValues) ?? [];
        $filteredNewValues = $this->filterAuditValues($newValues) ?? [];
        $changedOldValues = [];
        $changedNewValues = [];

        foreach ($this->trackedAuditFields() as $field) {
            $oldExists = array_key_exists($field, $filteredOldValues);
            $newExists = array_key_exists($field, $filteredNewValues);
            $oldValue = $oldExists ? $filteredOldValues[$field] : null;
            $newValue = $newExists ? $filteredNewValues[$field] : null;

            if ($oldExists === $newExists && $oldValue === $newValue) {
                continue;
            }

            if ($oldExists) {
                $changedOldValues[$field] = $oldValue;
            }

            if ($newExists) {
                $changedNewValues[$field] = $newValue;
            }
        }

        return [
            $changedOldValues === [] ? null : $changedOldValues,
            $changedNewValues === [] ? null : $changedNewValues,
        ];
    }
}
