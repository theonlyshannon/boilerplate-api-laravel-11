<?php

namespace App\Http\Controllers\Api;

use App\Helpers\HashidsHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExampleStoreRequest;
use App\Http\Requests\ExampleUpdateRequest;
use App\Http\Resources\ExampleResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\ExampleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExampleController extends Controller
{
    protected $exampleRepository;

    public function __construct(ExampleRepositoryInterface $exampleRepository)
    {
        $this->exampleRepository = $exampleRepository;

        $this->middleware('permission:example-list', ['only' => ['index', 'getAllPaginated', 'getAllActive', 'show']]);
        $this->middleware('permission:example-create', ['only' => ['store']]);
        $this->middleware('permission:example-edit', ['only' => ['update']]);
        $this->middleware('permission:example-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $request->merge([
            'search' => $request->has('search') ? $request->search : null,
            'limit' => $request->has('limit') ? $request->limit : null,
        ]);

        $request = $request->validate([
            'search' => 'nullable|string',
            'limit' => 'nullable|integer|min:1',
        ]);

        try {
            $examples = $this->exampleRepository->getAll(
                search: $request['search'],
                limit: $request['limit'],
                execute: true
            );

            return ResponseHelper::jsonResponse(true, 'Success', ExampleResource::collection($examples), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request->merge([
            'search' => $request->has('search') ? $request->search : null,
            'rowsPerPage' => $request->has('rowsPerPage') ? $request->rowsPerPage : null,
        ]);

        $request = $request->validate([
            'search' => 'nullable|string',
            'rowsPerPage' => 'required|integer',
        ]);

        try {
            $examples = $this->exampleRepository->getAllPaginated(
                search: $request['search'],
                rowsPerPage: $request['rowsPerPage']
            );

            return ResponseHelper::jsonResponse(true, 'Success', PaginateResource::make($examples, ExampleResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function show($id)
    {
        try {
            $example = $this->exampleRepository->getById(
                id: HashidsHelper::decodeId($id),
                withTrashed: false
            );

            return ResponseHelper::jsonResponse(true, 'Success', new ExampleResource($example), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function store(ExampleStoreRequest $request)
    {
        $request = $request->validated();

        try {

            $example = $this->exampleRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Data Example berhasil ditambahkan.', new ExampleResource($example), 201);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function update(ExampleUpdateRequest $request, $id)
    {
        $request = $request->validated();

        try {

            $example = $this->exampleRepository->update(
                data: $request,
                id: HashidsHelper::decodeId($id),
            );

            return ResponseHelper::jsonResponse(true, 'Data Example berhasil diubah.', new ExampleResource($example), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function destroy($id)
    {
        try {
            $example = $this->exampleRepository->delete(HashidsHelper::decodeId($id));

            return ResponseHelper::jsonResponse(true, 'Data Example berhasil dihapus.', new ExampleResource($example), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
