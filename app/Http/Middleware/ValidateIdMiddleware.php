<?php

namespace App\Http\Middleware;

use App\Repositories\CheckerRepository;
use App\Repositories\ClientPersonInChargeRepository;
use App\Repositories\ClientRepository;
use App\Repositories\DriverRepository;
use App\Repositories\ExplosionRepository;
use App\Repositories\FleetRentalRecordRepository;
use App\Repositories\FleetRepository;
use App\Repositories\FuelAdjustmentCategoryRepository;
use App\Repositories\FuelAdjustmentRepository;
use App\Repositories\FuelConsumptionRepository;
use App\Repositories\FuelOperatorRepository;
use App\Repositories\FuelRefillRepository;
use App\Repositories\FuelRepository;
use App\Repositories\FuelStationRepository;
use App\Repositories\FuelTransactionRepository;
use App\Repositories\HeavyVehicleRepository;
use App\Repositories\IdentificationRepository;
use App\Repositories\MaterialMovementRepository;
use App\Repositories\MiningMaterialRepository;
use App\Repositories\MiningStationRepository;
use App\Repositories\ProjectPersonInChargeRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SolidVolumeEstimateRepository;
use App\Repositories\TechnicalAdminRepository;
use App\Repositories\TruckRepository;
use App\Repositories\UserRepository;
use App\Repositories\VendorRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cases = [
            'role',
            'user',
            'vendor',
            'driver',
            'fleet',
            'truck',
            'heavy_vehicle',
            'mining_material',
            'mining_station',
            'checker',
            'technical_admin',
            'fuel_operator',
            'client',
            'client_person_in_charge',
            'fleet_rental_record',
            'fuel',
            'fuel_station',
            'identification',
            'project',
            'project_person_in_charge',
            'fuel_adjustment_category',
            'fuel_adjustment',
            'fuel_consumption',
            'fuel_refill',
            'fuel_transaction',
            'solid_volume_estimate',
            'material_movement',
            'explosion',
        ];

        foreach ($request->route()->parameters as $key => $param) {
            if (in_array($key, $cases)) {
                if ((string) $param != $param) {
                    return response()->json(['message' => 'Invalid ID'], 404);
                }

                switch ($key) {
                    case 'role':
                        $roleRepository = new RoleRepository;
                        if (! $roleRepository->getById($param, false)) {
                            return response()->json(['message' => 'Role tidak ditemukan.'], 404);
                        }
                        break;
                    case 'user':
                        $userRepository = new UserRepository;
                        if (! $userRepository->getById($param, false)) {
                            return response()->json(['message' => 'User tidak ditemukan.'], 404);
                        }
                        break;
                    case 'vendor':
                        $vendorRepository = new VendorRepository;
                        if (! $vendorRepository->getById($param, false)) {
                            return response()->json(['message' => 'Vendor tidak ditemukan.'], 404);
                        }
                        break;
                    case 'driver':
                        $driverRepository = new DriverRepository;
                        if (! $driverRepository->getById($param, false)) {
                            return response()->json(['message' => 'Driver tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fleet':
                        $fleetRepository = new FleetRepository;
                        if (! $fleetRepository->getById($param, false)) {
                            return response()->json(['message' => 'Kendaraan tidak ditemukan.'], 404);
                        }
                        break;
                    case 'truck':
                        $truckRepository = new TruckRepository;
                        if (! $truckRepository->getById($param, false)) {
                            return response()->json(['message' => 'Kendaraan tidak ditemukan.'], 404);
                        }
                        break;
                    case 'heavy_vehicle':
                        $heavyVehicleRepository = new HeavyVehicleRepository;
                        if (! $heavyVehicleRepository->getById($param, false)) {
                            return response()->json(['message' => 'Kendaraan tidak ditemukan.'], 404);
                        }
                        break;
                    case 'mining_material':
                        $miningMaterialRepository = new MiningMaterialRepository;
                        if (! $miningMaterialRepository->getById($param, false)) {
                            return response()->json(['message' => 'Material Tambang tidak ditemukan.'], 404);
                        }
                        break;
                    case 'mining_station':
                        $miningStationRepository = new MiningStationRepository;
                        if (! $miningStationRepository->getById($param, false)) {
                            return response()->json(['message' => 'Stasiun Penambangan tidak ditemukan.'], 404);
                        }
                        break;
                    case 'checker':
                        $checkerRepository = new CheckerRepository;
                        if (! $checkerRepository->getById($param, false)) {
                            return response()->json(['message' => 'Pemeriksa tidak ditemukan.'], 404);
                        }
                        break;
                    case 'technical_admin':
                        $technicalAdminRepository = new TechnicalAdminRepository;
                        if (! $technicalAdminRepository->getById($param, false)) {
                            return response()->json(['message' => 'Admin Teknis tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fuel_operator':
                        $fuelOperatorRepository = new FuelOperatorRepository;
                        if (! $fuelOperatorRepository->getById($param, false)) {
                            return response()->json(['message' => 'Admin Bahan Bakar tidak ditemukan.'], 404);
                        }
                        break;
                    case 'client':
                        $clientRepository = new ClientRepository;
                        if (! $clientRepository->getById($param, false)) {
                            return response()->json(['message' => 'Pelanggan tidak ditemukan.'], 404);
                        }
                        break;
                    case 'client_person_in_charge':
                        $clientPersonInChargeRepository = new ClientPersonInChargeRepository;
                        if (! $clientPersonInChargeRepository->getById($param, false)) {
                            return response()->json(['message' => 'PIC Pelanggan tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fuel':
                        $fuelRepository = new FuelRepository;
                        if (! $fuelRepository->getById($param, false)) {
                            return response()->json(['message' => 'Bahan Bakar tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fuel_station':
                        $fuelStationRepository = new FuelStationRepository;
                        if (! $fuelStationRepository->getById($param, false)) {
                            return response()->json(['message' => 'Stasiun Bahan Bakar tidak ditemukan.'], 404);
                        }
                        break;
                    case 'identification':
                        $identificationRepository = new IdentificationRepository;
                        if (! $identificationRepository->getById($param, false)) {
                            return response()->json(['message' => 'Identitas tidak ditemukan.'], 404);
                        }
                        break;
                    case 'project':
                        $projectRepository = new ProjectRepository;
                        if (! $projectRepository->getById($param, false)) {
                            return response()->json(['message' => 'Proyek tidak ditemukan.'], 404);
                        }
                        break;
                    case 'project_person_in_charge':
                        $projectPersonInChargeRepository = new ProjectPersonInChargeRepository;
                        if (! $projectPersonInChargeRepository->getById($param, false)) {
                            return response()->json(['message' => 'PIC Proyek tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fleet_rental_record':
                        $fleetRentalRecordRepository = new FleetRentalRecordRepository;
                        if (! $fleetRentalRecordRepository->getById($param, false)) {
                            return response()->json(['message' => 'Rental Record Kendaraan tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fuel_adjustment_category':
                        $fuelAdjustmentCategoryRepository = new FuelAdjustmentCategoryRepository;
                        if (! $fuelAdjustmentCategoryRepository->getById($param, false)) {
                            return response()->json(['message' => 'Kategori Penyesuaian Bahan Bakar tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fuel_adjustment':
                        $fuelAdjustmentRepository = new FuelAdjustmentRepository;
                        if (! $fuelAdjustmentRepository->getById($param, false)) {
                            return response()->json(['message' => 'Penyesuaian Bahan Bakar tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fuel_consumption':
                        $fuelConsumptionRepository = new FuelConsumptionRepository;
                        if (! $fuelConsumptionRepository->getById($param, false)) {
                            return response()->json(['message' => 'Penyesuaian Bahan Bakar tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fuel_refill':
                        $fuelRefillRepository = new FuelRefillRepository;
                        if (! $fuelRefillRepository->getById($param, false)) {
                            return response()->json(['message' => 'Penyesuaian Bahan Bakar tidak ditemukan.'], 404);
                        }
                        break;
                    case 'fuel_transaction':
                        $fuelTransactionRepository = new FuelTransactionRepository;
                        if (! $fuelTransactionRepository->getById($param, false)) {
                            return response()->json(['message' => 'Transaksi Bahan Bakar tidak ditemukan.'], 404);
                        }
                        break;
                    case 'solid_volume_estimate':
                        $solidVolumeEstimateRepository = new SolidVolumeEstimateRepository;
                        if (! $solidVolumeEstimateRepository->getById($param, false)) {
                            return response()->json(['message' => 'Estimasi Volume Padat tidak ditemukan.'], 404);
                        }
                        break;
                    case 'material_movement':
                        $materialMovementRepository = new MaterialMovementRepository;
                        if (! $materialMovementRepository->getById($param, false)) {
                            return response()->json(['message' => 'Perpindahan Material tidak ditemukan.'], 404);
                        }
                        break;
                    case 'explosion':
                        $explosionRepository = new ExplosionRepository;
                        if (! $explosionRepository->getById($param, false)) {
                            return response()->json(['message' => 'Explosion tidak ditemukan.'], 404);
                        }
                        break;
                }
            }
        }

        return $next($request);
    }
}
