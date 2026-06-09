<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mostafax\AnalyticsSuite\DTOs\ReportTemplateDTO;

interface ReportBuilderRepositoryInterface
{
    public function find(int|string $id): ?ReportTemplateDTO;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function listForUser(int|string $userId): Collection;

    public function create(array $data): ReportTemplateDTO;

    public function update(int|string $id, array $data): ReportTemplateDTO;

    public function delete(int|string $id): bool;

    public function clone(int|string $id, string $newName): ReportTemplateDTO;

    public function export(int|string $id): array;

    public function import(array $definition): ReportTemplateDTO;
}
