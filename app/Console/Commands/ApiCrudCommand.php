<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ApiCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:apiv1 {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD files: model, migration, controller, store request, update request';

    public function handle()
    {
        $this->info('Creating magic... 🪄');

        $this->createModel();

        $this->createController();

        $this->createRequests();

        $this->modifyMigration();

        $this->createResource();

        $this->modifyRepository();
        $this->info('Repository created successfully! ✅');

        $this->createFactory();;

        $this->addRoutes();

        $this->comment('Api Make Successful');
    }

    protected function createModel()
    {
        $name = $this->argument('name');
        $this->call('make:model', ['name' => $name, '-m' => true]);

        $modelPath = app_path("Models/{$name}.php");
        $modelContent = <<<EOT
            <?php

            namespace App\Models;

            use Illuminate\Database\Eloquent\Factories\HasFactory;
            use Illuminate\Database\Eloquent\Model;
            use Illuminate\Database\Eloquent\SoftDeletes;
            use App\Traits\UUID;

            class {$name} extends Model
            {
                use HasFactory, UUID, SoftDeletes;

                protected \$fillable = [
                    // Add your columns here
                ];
            }
            EOT;

        file_put_contents($modelPath, $modelContent);
    }

    protected function createRequests()
    {
        $name = $this->argument('name');
        $this->call('make:request', ['name' => "{$name}StoreRequest"]);
        $this->call('make:request', ['name' => "{$name}UpdateRequest"]);

        $storeRequestPath = app_path("Http/Requests/{$name}StoreRequest.php");
        $storeRequestContent = <<<EOT
            <?php

            namespace App\Http\Requests;

            use Illuminate\Foundation\Http\FormRequest;

            class Store{$name}Request extends FormRequest
            {
                /**
                 * Get the validation rules that apply to the request.
                 *
                 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
                 */
                public function rules()
                {
                    return [
                        // Add your validation rules here
                    ];
                }

                public function attributes()
                {
                    return [
                        // Add your attributes here
                    ];
                }

                public function messages()
                {
                    return [
                        // Add your messages here
                    ];
                }
            }
            EOT;

        file_put_contents($storeRequestPath, $storeRequestContent);

        $updateRequestPath = app_path("Http/Requests/{$name}UpdateRequest.php");
        $updateRequestContent = <<<EOT
            <?php

            namespace App\Http\Requests;

            use Illuminate\Foundation\Http\FormRequest;

            class Update{$name}Request extends FormRequest
            {
                /**
                 * Get the validation rules that apply to the request.
                 *
                 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
                 */
                public function rules()
                {
                    return [
                        // Add your validation rules here
                    ];
                }

                public function attributes()
                {
                    return [
                        // Add your attributes here
                    ];
                }

                public function messages()
                {
                    return [
                        // Add your messages here
                    ];
                }
            }
            EOT;

        file_put_contents($updateRequestPath, $updateRequestContent);
    }

    protected function createController()
    {
        $name = $this->argument('name');
        $this->call('make:controller', ['name' => "{$name}StoreRequest"]);

        $controllerPath = app_path("Http/Controllers/Api/{$name}Controller.php");

        $controllerContent =
            <<<'EOT'
            <?php

            namespace App\Http\Controllers\Api;

            use App\Helpers\HashidsHelper;
            use App\Helpers\ResponseHelper;
            use App\Http\Controllers\Controller;
            use App\Http\Requests\__namePascalCase__StoreRequest;
            use App\Http\Requests\__namePascalCase__UpdateRequest;
            use App\Http\Resources\__namePascalCase__Resource;
            use App\Http\Resources\PaginateResource;
            use App\Interfaces\__namePascalCase__RepositoryInterface;
            use Illuminate\Http\Request;
            use Illuminate\Support\Str;

            class __namePascalCase__Controller extends Controller
            {
                protected $__nameCamelCase__Repository;

                public function __construct(__namePascalCase__RepositoryInterface $__nameCamelCase__Repository)
                {
                    $this->__nameCamelCase__Repository = $__nameCamelCase__Repository;

                    $this->middleware('permission:__nameKebabCase__-list', ['only' => ['index', 'getAllPaginated', 'getAllActive', 'show']]);
                    $this->middleware('permission:__nameKebabCase__-create', ['only' => ['store']]);
                    $this->middleware('permission:__nameKebabCase__-edit', ['only' => ['update']]);
                    $this->middleware('permission:__nameKebabCase__-delete', ['only' => ['destroy']]);
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
                        $__nameCamelCasePlurals__ = $this->__nameCamelCase__Repository->getAll(
                            search: $request['search'],
                            limit: $request['limit'],
                            execute: true
                        );

                        return ResponseHelper::jsonResponse(true, 'Success', __namePascalCase__Resource::collection($__nameCamelCasePlurals__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function getAllActive(Request $request)
                {
                    $request = $request->validate([
                        'include_id' => 'nullable|integer|exists:__nameSnakeCasePlurals__,id',
                    ]);

                    try {
                        $includeId = isset($request['include_id']) ? $request['include_id'] : null;

                        $__nameCamelCasePlurals__ = $this->__nameCamelCase__Repository->getAllActive($includeId);

                        return ResponseHelper::jsonResponse(true, 'Success', __namePascalCase__Resource::collection($__nameCamelCasePlurals__), 200);
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
                        $__nameCamelCasePlurals__ = $this->__nameCamelCase__Repository->getAllPaginated(
                            search: $request['search'],
                            rowsPerPage: $request['rowsPerPage']
                        );

                        return ResponseHelper::jsonResponse(true, 'Success', PaginateResource::make($__nameCamelCasePlurals__, __namePascalCase__Resource::class), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function show($id)
                {
                    try {
                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->getById(
                            id: HashidsHelper::decodeId($id),
                            withTrashed: false
                        );

                        return ResponseHelper::jsonResponse(true, 'Success', new __namePascalCase__Resource($__nameCamelCase__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function store(__namePascalCase__StoreRequest $request)
                {
                    $request = $request->validated();

                    try {

                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->create($request);

                        return ResponseHelper::jsonResponse(true, 'Data __nameProperCase__ berhasil ditambahkan.', new __namePascalCase__Resource($__nameCamelCase__), 201);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function update(__namePascalCase__UpdateRequest $request, $id)
                {
                    $request = $request->validated();

                    try {

                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->update(
                            data: $request,
                            id: HashidsHelper::decodeId($id),
                        );

                        return ResponseHelper::jsonResponse(true, 'Data __nameProperCase__ berhasil diubah.', new __namePascalCase__Resource($__nameCamelCase__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function destroy($id)
                {
                    try {
                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->delete(HashidsHelper::decodeId($id));

                        return ResponseHelper::jsonResponse(true, 'Data __nameProperCase__ berhasil dihapus.', new __namePascalCase__Resource($__nameCamelCase__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }
            }
            EOT;

        $controllerContent = str_replace('__namePascalCase__', $name, $controllerContent);
        $controllerContent = str_replace('__nameCamelCase__', Str::camel($name), $controllerContent);
        $controllerContent = str_replace('__nameSnakeCase__', Str::snake($name), $controllerContent);
        $controllerContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $controllerContent);
        $controllerContent = str_replace('__nameKebabCase__', Str::kebab($name), $controllerContent);
        $controllerContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $controllerContent);

        file_put_contents($controllerPath, $controllerContent);
    }

    protected function modifyMigration()
    {
        $name = $this->argument('name');
        $this->call('make:migration', ['name' => "create_{$name}_table"]);
        $name = Str::snake($name);
        $name = Str::plural($name);
        $migration = database_path('migrations/'.date('Y_m_d_His').'_create_'.$name.'_table.php');

        $migrationContent = <<<EOT
            <?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration
            {
                /**
                 * Run the migrations.
                 */
                public function up()
                {
                    Schema::create('{$name}', function (Blueprint \$table) {
                        \$table->id();
                        // Add your columns here

                        \$table->softDeletes();
                        \$table->timestamps();
                    });
                }

                /**
                 * Reverse the migrations.
                 */
                public function down()
                {
                    Schema::dropIfExists('{$name}');
                }
            };
            EOT;

        file_put_contents($migration, $migrationContent);
    }

    protected function createResource()
    {
        $name = $this->argument('name');
        $this->call('make:resource', ['name' => "{$name}Resource"]);
        $resource = app_path("Http/Resources/{$name}Resource.php");

        $resourceContent = <<<EOT
            <?php

            namespace App\Http\Resources;

            use Illuminate\Http\Resources\Json\JsonResource;
            use App\Helpers\HashidsHelper; // Menggunakan HashidsHelper yang sudah ada

            class {$name}Resource extends JsonResource
            {
                /**
                 * Transform the resource into an array.
                 *
                 * @param  \Illuminate\Http\Request  \$request
                 * @return array<string, mixed>
                 */
                public function toArray(\$request)
                {
                    return [
                        'id' => HashidsHelper::encodeId(\$this->id),
                        // Add your columns here
                        'created_at' => \$this->created_at,
                        'updated_at' => \$this->updated_at,
                        'deleted_at' => \$this->deleted_at,
                    ];
                }
            }
            EOT;

        file_put_contents($resource, $resourceContent);
    }

    protected function createFactory()
    {
        $name = $this->argument('name');
        $this->call('make:factory', ['name' => "{$name}Factory"]);
        $factory = database_path("factories/{$name}Factory.php");

        $factoryContent = <<<EOT
            <?php

            namespace Database\Factories;

            use Illuminate\Database\Eloquent\Factories\Factory;
            use Illuminate\Support\Str;

            class {$name}Factory extends Factory
            {
                /**
                 * Define the model's default state.
                 *
                 * @return array<string, mixed>
                 */
                public function definition(): array
                {
                    return [
                        // Define your default state here
                    ];
                }
            }
            EOT;

        file_put_contents($factory, $factoryContent);
    }

    protected function modifyRepository()
    {
        $name = $this->argument('name');
        $interfacePath = app_path("Interfaces/{$name}RepositoryInterface.php");
        $repositoryPath = app_path("Repositories/{$name}Repository.php");

        $interfaceContent = $this->generateInterfaceContent($name);

        $repositoryContent = $this->generateRepositoryContent($name);

        file_put_contents($interfacePath, $interfaceContent);
        file_put_contents($repositoryPath, $repositoryContent);

        $this->updateRepositoryServiceProvider($name);
    }

    protected function generateInterfaceContent($name)
    {
        $interfaceContent = <<<'EOT'
    <?php

    namespace App\Interfaces;

    interface __namePascalCase__RepositoryInterface
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

        public function getAllActive(
            ?string $search,
            ?int $includeId = null
        );

        public function getById(int $id, bool $withTrashed);

        public function isAvailable(int $id): bool;

        public function create(array $data);

        public function update(array $data, int $id);

        public function delete(int $id);
    }
    EOT;

        $interfaceContent = str_replace('__namePascalCase__', $name, $interfaceContent);
        $interfaceContent = str_replace('__namePascalCasePlurals__', Str::studly(Str::plural($name)), $interfaceContent);
        $interfaceContent = str_replace('__nameCamelCase__', Str::camel($name), $interfaceContent);
        $interfaceContent = str_replace('__nameSnakeCase__', Str::snake($name), $interfaceContent);
        $interfaceContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $interfaceContent);
        $interfaceContent = str_replace('__nameKebabCase__', Str::kebab($name), $interfaceContent);
        $interfaceContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $interfaceContent);

        return $interfaceContent;
    }

    protected function generateRepositoryContent($name)
    {
        $repositoryContent = <<<'EOT'
    <?php

    namespace App\Repositories;

    use App\Interfaces\__namePascalCase__RepositoryInterface;
    use App\Models\__namePascalCase__;
    use Exception;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Storage;

    class __namePascalCase__Repository implements __namePascalCase__RepositoryInterface
    {
        public function getAll(
            ?string $search,
            ?int $limit,
            bool $execute
        ) {
            $query = __namePascalCase__::withTrashed()->where(function ($query) use ($search) {
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

        public function getAllActive(
            ?string $search,
            ?int $includeId = null
        ) {
            $query = $this->getAll(
                search: $search,
                limit: null,
                execute: false
            );

            if ($includeId) {
                $query = $query->orWhere('id', '=', $includeId);
            }

            return $query->get();
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
            $query = __namePascalCase__::where('id', '=', $id);

            if ($withTrashed) {
                $query = $query->withTrashed();
            }

            return $query->first();
        }

        public function create(array $data)
        {
            DB::beginTransaction();

            try {
                $__nameCamelCase__ = new __namePascalCase__();
                // Add your columns here
                $__nameCamelCase__->save();

                DB::commit();

                return $__nameCamelCase__;
            } catch (\Exception $e) {
                DB::rollBack();

                throw new Exception($e->getMessage());
            }
        }

        public function update(array $data, int $id)
        {
            DB::beginTransaction();

            try {
                $__nameCamelCase__ = __namePascalCase__::find($id);
                // Add your columns here
                $__nameCamelCase__->save();

                DB::commit();

                return $__nameCamelCase__;
            } catch (\Exception $e) {
                DB::rollBack();

                throw new Exception($e->getMessage());
            }
        }

        public function delete(int $id)
        {
            DB::beginTransaction();

            try {
                $__nameCamelCase__ = __namePascalCase__::find($id);
                $__nameCamelCase__->delete();

                DB::commit();

                return $__nameCamelCase__;
            } catch (\Exception $e) {
                DB::rollBack();

                throw new Exception($e->getMessage());
            }
        }

    }
    EOT;

        $repositoryContent = str_replace('__namePascalCase__', $name, $repositoryContent);
        $repositoryContent = str_replace('__nameCamelCase__', Str::camel($name), $repositoryContent);
        $repositoryContent = str_replace('__nameSnakeCase__', Str::snake($name), $repositoryContent);
        $repositoryContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $repositoryContent);
        $repositoryContent = str_replace('__nameKebabCase__', Str::kebab($name), $repositoryContent);
        $repositoryContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $repositoryContent);

        return $repositoryContent;
    }

    protected function updateRepositoryServiceProvider($name)
    {
        $repositoryServiceProvider = app_path('Providers/RepositoryServiceProvider.php');
        $repositoryServiceProviderContent = file_get_contents($repositoryServiceProvider);

        $replacement = "\$this->app->bind(\App\Interfaces\\{$name}RepositoryInterface::class, \App\Repositories\\{$name}Repository::class);\n    }\n";

        $pattern = '/public function register\(\)\s*{([^}]*)}/s';
        $repositoryServiceProviderContent = preg_replace($pattern, "public function register() {\n$1$replacement", $repositoryServiceProviderContent, 1);

        file_put_contents($repositoryServiceProvider, $repositoryServiceProviderContent);
    }

    protected function addRoutes()
    {
        $name = $this->argument('name');

        $name = Str::kebab($name);
        $routes = base_path('routes/api.php');

        $routeContent = "\nRoute::Apiresource('{$name}', App\Http\Controllers\Web\Api\\{$this->argument('name')}Controller::class);";

        file_put_contents($routes, $routeContent, FILE_APPEND);
    }

    protected function createTest()
    {
        $name = $this->argument('name');
        $test = base_path("tests/Feature/{$name}APITest.php");
        $testContent =
            <<<'EOT'
            <?php

            namespace Tests\Feature;

            use App\Enum\UserRoleEnum;
            use App\Models\User;
            use App\Models\__namePascalCase__;
            use Illuminate\Support\Arr;
            use Illuminate\Support\Facades\Storage;
            use Illuminate\Support\Str;
            use Tests\TestCase;

            class __namePascalCase__APITest extends TestCase
            {
                public function setUp(): void
                {
                    parent::setUp();

                    Storage::fake('public');
                }

                // 1-1
                public function test___nameSnakeCase___api_call_index_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCasePlurals__ = __namePascalCase__::factory(3)->create();

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__');

                    $response->assertSuccessful();

                    $resultCount = 0;
                    foreach ($response['data'] as $data) {
                        foreach ($__nameCamelCasePlurals__ as $__nameCamelCase__) {
                            if ($data['id'] == $__nameCamelCase__->id) {
                                $resultCount++;
                            }
                        }
                    }
                    $this->assertEquals($resultCount, count($__nameCamelCasePlurals__));
                }

                // 1-2-1
                public function test___nameSnakeCase___api_call_get_all_active_without_param_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active');

                    $response->assertSuccessful();
                }

                // 1-2-2
                public function test___nameSnakeCase___api_call_get_all_active_with_existing_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Active
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => $__nameCamelCase__->id]);

                    $response->assertSuccessful();

                    $responseHas__namePascalCase__ = false;
                    foreach ($response['data'] as $data) {
                        if ($data['id'] == $__nameCamelCase__->id) {
                            $responseHas__namePascalCase__ = true;
                        }
                    }
                    $this->assertTrue($responseHas__namePascalCase__);

                    // Inactive
                    $__nameCamelCase__ = __namePascalCase__::factory()->create([
                        'is_active' => false
                    ]);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => $__nameCamelCase__->id]);

                    $response->assertSuccessful();

                    $responseHas__namePascalCase__ = false;
                    foreach ($response['data'] as $data) {
                        if ($data['id'] == $__nameCamelCase__->id) {
                            $responseHas__namePascalCase__ = true;
                        }
                    }
                    $this->assertTrue($responseHas__namePascalCase__);

                    // Soft Deleted
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => $__nameCamelCase__->id]);

                    $response->assertSuccessful();

                    $__nameCamelCase__->delete();

                    $responseHas__namePascalCase__ = false;
                    foreach ($response['data'] as $data) {
                        if ($data['id'] == $__nameCamelCase__->id) {
                            $responseHas__namePascalCase__ = true;
                        }
                    }
                    $this->assertTrue($responseHas__namePascalCase__);
                }

                // 1-2-3
                public function test___nameSnakeCase___api_call_get_all_active_with_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => 0]);

                    $this->assertNotEquals(200, $response->getStatusCode());

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => -1]);

                    $this->assertNotEquals(200, $response->getStatusCode());

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => Str::random(5)]);

                    $this->assertNotEquals(200, $response->getStatusCode());
                }

                // 1-3-1
                public function test___nameSnakeCase___api_call_show_with_valid_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id);

                    $response->assertSuccessful();

                    $__nameCamelCase__ = Arr::except($__nameCamelCase__->toArray(), ['created_at', 'updated_at']);

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);
                }

                // 1-3-2
                public function test___nameSnakeCase___api_call_show_with_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/0');

                    $response->assertStatus(404);
                }

                // 1-4-1
                public function test___nameSnakeCase___api_call_check_availability_with_valid_param_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Active
                    $__nameCamelCase__ = __namePascalCase__::factory()->create(
                        ['is_active' => true]
                    );

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/'.$__nameCamelCase__->id);

                    $response->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id, 'is_active' => true]);

                    // Inactive
                    $__nameCamelCase__ = __namePascalCase__::factory()->create(
                        ['is_active' => false]
                    );

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/'.$__nameCamelCase__->id);

                    $response->assertStatus(404);

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id, 'is_active' => false]);
                }

                // 1-4-2
                public function test___nameSnakeCase___api_call_check_availability_with_invalid_param_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/'.Str::random(5));

                    $response->assertStatus(404);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/0');

                    $response->assertStatus(404);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/-1');

                    $response->assertStatus(404);
                }

                // 2-1-1
                public function test___nameSnakeCase___api_call_create_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);
                }

                // 2-1-2
                public function test___nameSnakeCase___api_call_create_with_existing_code_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $existing__namePascalCase__ = __namePascalCase__::factory()->create();

                    $new__namePascalCase__ = __namePascalCase__::factory()->make([
                        'code' => $existing__namePascalCase__->code,
                    ])->toArray();

                    $response = $this->json('POST', '/api/v1/__nameKebabCase__', $new__namePascalCase__);

                    $response->assertStatus(422);
                }

                // 2-1-3
                public function test___nameSnakeCase___api_call_create_without_required_fields_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Code
                    $__nameCamelCase__ = __namePascalCase__::factory()->make(['code' => null])->toArray();

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertStatus(422);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__['code']);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertStatus(422);
                }

                // 2-1-4
                public function test___nameSnakeCase___api_call_create_without_nullable_fields_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Remarks
                    $__nameCamelCase__ = __namePascalCase__::factory()->make(['remarks' => null])->toArray();

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__['remarks']);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);

                    // Is Active
                    $__nameCamelCase__ = __namePascalCase__::factory()->make(['is_active' => null])->toArray();

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $__nameCamelCase__['is_active'] = true;

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__['is_active']);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $__nameCamelCase__['is_active'] = true;

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);
                }

                // 2-1-5
                public function test___nameSnakeCase___api_call_create_with_empty_array_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = [];

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertStatus(422);
                }

                // 3-1-1
                public function test___nameSnakeCase___api_call_update_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make()->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__Update);
                }

                // 3-1-2
                public function test___nameSnakeCase___api_call_update_with_existing_code_in_same___nameSnakeCase___with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $existing__namePascalCase__ = __namePascalCase__::factory()->create();

                    $new__namePascalCase__ = __namePascalCase__::factory()->make([
                        'code' => $existing__namePascalCase__->code,
                    ])->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$existing__namePascalCase__->id, $new__namePascalCase__);

                    $response->assertSuccessful();

                    $new__namePascalCase__ = Arr::except($new__namePascalCase__, ['created_at', 'updated_at']);
                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $new__namePascalCase__);
                }

                // 3-1-3
                public function test___nameSnakeCase___api_call_update_with_existing_code_in_another___nameSnakeCase___with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $existing__namePascalCase__ = __namePascalCase__::factory()->create();

                    $new__namePascalCase__ = __namePascalCase__::factory()->create();

                    $update__namePascalCase__ = __namePascalCase__::factory()->make([
                        'code' => $existing__namePascalCase__->code,
                    ]);

                    $api = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$new__namePascalCase__->id, $update__namePascalCase__->toArray());

                    $api->assertStatus(422);
                }

                // 3-1-4
                public function test___nameSnakeCase___api_call_update_without_required_fields_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Code
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make(['code' => null])->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertStatus(422);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__Update['code']);

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertStatus(422);

                    // Is Active
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make(['is_active' => null])->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertStatus(422);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__Update['is_active']);

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertStatus(422);
                }

                // 3-1-5
                public function test___nameSnakeCase___api_call_update_without_nullable_fields_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Remarks
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make(['remarks' => null])->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertSuccessful();

                    $__nameCamelCase__Update = Arr::except($__nameCamelCase__Update, ['created_at', 'updated_at']);
                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__Update);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__Update['remarks']);

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertSuccessful();

                    $__nameCamelCase__Update = Arr::except($__nameCamelCase__Update, ['created_at', 'updated_at']);
                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__Update);
                }

                // 3-1-6
                public function test___nameSnakeCase___api_call_update_with_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();

                    $api = $this->json('PUT', 'api/v1/__nameKebabCase__/'. Str::random(5), $__nameCamelCase__);

                    $api->assertStatus(404);
                }

                // 3-1-7
                public function test___nameSnakeCase___api_call_update_with_empty_array_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $api = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, []);

                    $api->assertStatus(422);
                }

                // 3-2-1
                public function test___nameSnakeCase___api_call_update_active_status_with_valid_param_and_valid_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create([
                        'is_active' => true
                    ]);

                    // Active
                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => true]);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id, 'is_active' => true]);

                    // Inactive
                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => false]);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id, 'is_active' => false]);
                }

                // 3-2-2
                public function test___nameSnakeCase___api_call_update_active_status_with_valid_param_and_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.Str::random(5), ['is_active' => mt_rand(0, 1)]);

                    $api->assertStatus(404);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/0', ['is_active' => mt_rand(0, 1)]);

                    $api->assertStatus(404);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/-1', ['is_active' => mt_rand(0, 1)]);

                    $api->assertStatus(404);
                }

                // 3-2-3
                public function test___nameSnakeCase___api_call_update_active_status_with_invalid_param_and_valid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create([
                        'is_active' => true
                    ]);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => null]);

                    $api->assertStatus(422);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => '']);

                    $api->assertStatus(422);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => Str::random(5)]);

                    $api->assertStatus(422);
                }

                // 3-2-4
                public function test___nameSnakeCase___api_call_update_active_status_with_invalid_param_and_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.Str::random(5), ['is_active' => null]);

                    $api->assertStatus(422);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/0', ['is_active' => '']);

                    $api->assertStatus(422);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/1', ['is_active' => Str::random(5)]);

                    $api->assertStatus(422);
                }

                // 4-1
                public function test___nameSnakeCase___api_call_delete_with_valid_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $response = $this->json('DELETE', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id);

                    $response->assertSuccessful();

                    $this->assertSoftDeleted('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id]);
                }

                // 4-2
                public function test___nameSnakeCase___api_call_delete_with_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('DELETE', 'api/v1/__nameKebabCase__/'.Str::random(5));

                    $response->assertStatus(404);
                }
            }
            EOT;
        $testContent = str_replace('@name', $name.'Test', $testContent);
        $testContent = str_replace('__namePascalCase__', $name, $testContent);
        $testContent = str_replace('__namePascalCasePlurals__', Str::studly(Str::plural($name)), $testContent);
        $testContent = str_replace('__nameCamelCase__', Str::camel($name), $testContent);
        $testContent = str_replace('__nameSnakeCase__', Str::snake($name), $testContent);
        $testContent = str_replace('__nameSnakeCasePlurals__', Str::snake(Str::plural($name)), $testContent);
        $testContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $testContent);
        $testContent = str_replace('__nameKebabCase__', Str::kebab($name), $testContent);
        $testContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $testContent);

        file_put_contents($test, $testContent);
    }

}
