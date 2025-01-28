<?php

namespace App\Repositories;

use App\Interfaces\ExampleRepositoryInterface;
use App\Models\Example;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExampleRepository implements ExampleRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    ) {
        $query = Example::withTrashed()->where(function ($query) use ($search) {
            $query->withoutTrashed();

            if ($search) {
                $query->search($search);
            }
        });

        $query->orderBy('name', 'asc');

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        } else {
            return $query;
        }
    }

    public function getAllPaginated(?string $search, int $rowsPerPage)
    {
        $query = $this->getAll(
            search: $search,
            limit: null,
            execute: false
        );

        return $query->paginate($rowsPerPage);
    }

    public function getById(int $id, bool $withTrashed)
    {
        $query = Example::where('id', '=', $id);

        if ($withTrashed) {
            $query = $query->withTrashed();
        }

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $example = new Example();
            // Add your columns here
            $example->save();

            DB::commit();

            return $example;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function update(array $data, int $id)
    {
        DB::beginTransaction();

        try {
            $example = Example::find($id);
            // Add your columns here
            $example->save();

            DB::commit();

            return $example;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $example = Example::find($id);
            $example->delete();

            DB::commit();

            return $example;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    private function saveImage($image)
    {
        if ($image) {
            return $image->store('assets/example/images', 'public');
        } else {
            return null;
        }
    }

    private function updateImage($oldImage, $newImage)
    {
        if ($newImage) {
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            return $newImage->store('assets/example/images', 'public');
        } else {
            return $oldImage;
        }
    }
}