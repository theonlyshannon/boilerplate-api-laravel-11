<?php

namespace App\Interfaces;

interface ExampleRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
    );

    public function getAllPaginated(
        ?string $search,
        int $rowsPerPage
    );

    public function getById(int $id, bool $withTrashed);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);
}