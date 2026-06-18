<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SystemConfigSeeder::class,
            CountrySeeder::class,
            DepartmentSeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
            Cie10CategorySeeder::class,
            Cie10CodeSeeder::class,
            SpecialtySeeder::class,
            InstitutionSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            MedicationCategorySeeder::class,
            MedicationCatalogSeeder::class,
            ServiceCatalogSeeder::class,
            TriageProtocolSeeder::class,
            BuildingSeeder::class,
            FloorSeeder::class,
            RoomSeeder::class,
            BedSeeder::class,
            PaymentMethodSeeder::class,
            TestPatientSeeder::class,
            TestAppointmentSeeder::class,
            TestMedicalRecordSeeder::class,
        ]);
    }
}
