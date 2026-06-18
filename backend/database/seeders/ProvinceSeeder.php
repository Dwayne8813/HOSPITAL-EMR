<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $departments = DB::table('departments')->pluck('id', 'code')->toArray();

        $provinces = [
            // Chuquisaca
            ['department_id' => $departments['CHU'], 'name' => 'Hernando Siles', 'code' => 'HS'],
            ['department_id' => $departments['CHU'], 'name' => 'Yamparáez', 'code' => 'YA'],
            ['department_id' => $departments['CHU'], 'name' => 'Nor Cinti', 'code' => 'NC'],
            ['department_id' => $departments['CHU'], 'name' => 'Sur Cinti', 'code' => 'SC'],
            ['department_id' => $departments['CHU'], 'name' => 'Belisario Boeto', 'code' => 'BB'],
            ['department_id' => $departments['CHU'], 'name' => 'Luis Calvo', 'code' => 'LC'],

            // La Paz
            ['department_id' => $departments['LPZ'], 'name' => 'Murillo', 'code' => 'MU'],
            ['department_id' => $departments['LPZ'], 'name' => 'Achacachi', 'code' => 'AC'],
            ['department_id' => $departments['LPZ'], 'name' => 'La Paz', 'code' => 'LP'],
            ['department_id' => $departments['LPZ'], 'name' => 'Ingavi', 'code' => 'IN'],
            ['department_id' => $departments['LPZ'], 'name' => 'Los Andes', 'code' => 'LA'],
            ['department_id' => $departments['LPZ'], 'name' => 'Camacho', 'code' => 'CA'],
            ['department_id' => $departments['LPZ'], 'name' => 'Manco Kapac', 'code' => 'MK'],

            // Cochabamba
            ['department_id' => $departments['CBB'], 'name' => 'Cercado', 'code' => 'CE'],
            ['department_id' => $departments['CBB'], 'name' => 'Chapare', 'code' => 'CH'],
            ['department_id' => $departments['CBB'], 'name' => 'Arani', 'code' => 'AR'],
            ['department_id' => $departments['CBB'], 'name' => 'Punata', 'code' => 'PU'],
            ['department_id' => $departments['CBB'], 'name' => 'Quillacollo', 'code' => 'QU'],
            ['department_id' => $departments['CBB'], 'name' => 'Tapacarí', 'code' => 'TA'],

            // Oruro
            ['department_id' => $departments['ORU'], 'name' => 'Cercado', 'code' => 'OR'],
            ['department_id' => $departments['ORU'], 'name' => 'Avaroa', 'code' => 'AV'],
            ['department_id' => $departments['ORU'], 'name' => 'Mejillones', 'code' => 'ME'],
            ['department_id' => $departments['ORU'], 'name' => 'Sajama', 'code' => 'SJ'],
            ['department_id' => $departments['ORU'], 'name' => 'Poopó', 'code' => 'PO'],
            ['department_id' => $departments['ORU'], 'name' => 'Litoral', 'code' => 'LI'],

            // Potosí
            ['department_id' => $departments['PTS'], 'name' => 'Cercado', 'code' => 'PT'],
            ['department_id' => $departments['PTS'], 'name' => 'Charcas', 'code' => 'CH'],
            ['department_id' => $departments['PTS'], 'name' => 'Alonso de Ibáñez', 'code' => 'AI'],
            ['department_id' => $departments['PTS'], 'name' => 'José María Linares', 'code' => 'JL'],
            ['department_id' => $departments['PTS'], 'name' => 'Cornelio Saavedra', 'code' => 'CS'],
            ['department_id' => $departments['PTS'], 'name' => 'Modesto Omiste', 'code' => 'MO'],
            ['department_id' => $departments['PTS'], 'name' => 'Rudolfo Siles', 'code' => 'RS'],

            // Tarija
            ['department_id' => $departments['TJA'], 'name' => 'Cercado', 'code' => 'CER'],
            ['department_id' => $departments['TJA'], 'name' => 'Gran Chaco', 'code' => 'GC'],
            ['department_id' => $departments['TJA'], 'name' => 'Avilés', 'code' => 'AV'],
            ['department_id' => $departments['TJA'], 'name' => 'Méndez', 'code' => 'ME'],
            ['department_id' => $departments['TJA'], 'name' => "O'Connor", 'code' => 'OC'],

            // Santa Cruz
            ['department_id' => $departments['SCZ'], 'name' => 'Andrés Ibáñez', 'code' => 'AI'],
            ['department_id' => $departments['SCZ'], 'name' => 'Cordillera', 'code' => 'CO'],
            ['department_id' => $departments['SCZ'], 'name' => 'Vallegrande', 'code' => 'VG'],
            ['department_id' => $departments['SCZ'], 'name' => 'Chiquitos', 'code' => 'CH'],
            ['department_id' => $departments['SCZ'], 'name' => 'Sara', 'code' => 'SA'],
            ['department_id' => $departments['SCZ'], 'name' => 'Guarayos', 'code' => 'GU'],
            ['department_id' => $departments['SCZ'], 'name' => 'Ichilo', 'code' => 'IC'],
            ['department_id' => $departments['SCZ'], 'name' => 'Cabantuyacu', 'code' => 'CB'],

            // Beni
            ['department_id' => $departments['BEN'], 'name' => 'Cercado', 'code' => 'BC'],
            ['department_id' => $departments['BEN'], 'name' => 'Vaca Díez', 'code' => 'VD'],
            ['department_id' => $departments['BEN'], 'name' => 'Ballivián', 'code' => 'BA'],
            ['department_id' => $departments['BEN'], 'name' => 'Yacuma', 'code' => 'YA'],
            ['department_id' => $departments['BEN'], 'name' => 'Moxos', 'code' => 'MX'],
            ['department_id' => $departments['BEN'], 'name' => 'Cercado', 'code' => 'CE'],

            // Pando
            ['department_id' => $departments['PAN'], 'name' => 'Federico Román', 'code' => 'FR'],
            ['department_id' => $departments['PAN'], 'name' => 'Madre de Dios', 'code' => 'MD'],
            ['department_id' => $departments['PAN'], 'name' => 'Manuripi', 'code' => 'MN'],
            ['department_id' => $departments['PAN'], 'name' => 'Porvenir', 'code' => 'PO'],
            ['department_id' => $departments['PAN'], 'name' => 'Bolpebra', 'code' => 'BO'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
