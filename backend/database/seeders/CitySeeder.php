<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $provinces = DB::table('provinces')->pluck('id', 'name')->toArray();

        $cities = [
            // Chuquisaca - Hernando Siles
            ['province_id' => $provinces['Hernando Siles'], 'name' => 'Sucre', 'code' => 'SUC'],
            ['province_id' => $provinces['Hernando Siles'], 'name' => 'Yotala', 'code' => 'YOT'],
            ['province_id' => $provinces['Hernando Siles'], 'name' => 'Poroma', 'code' => 'POR'],

            // Chuquisaca - Yamparáez
            ['province_id' => $provinces['Yamparáez'], 'name' => 'Tarabuco', 'code' => 'TAR'],
            ['province_id' => $provinces['Yamparáez'], 'name' => 'Yamparáez', 'code' => 'YAM'],

            // Chuquisaca - Nor Cinti
            ['province_id' => $provinces['Nor Cinti'], 'name' => 'Camargo', 'code' => 'CAM'],
            ['province_id' => $provinces['Nor Cinti'], 'name' => 'Incahuasi', 'code' => 'INC'],

            // Chuquisaca - Sur Cinti
            ['province_id' => $provinces['Sur Cinti'], 'name' => 'Villa Abecia', 'code' => 'VAB'],
            ['province_id' => $provinces['Sur Cinti'], 'name' => 'Culpina', 'code' => 'CUL'],

            // Chuquisaca - Belisario Boeto
            ['province_id' => $provinces['Belisario Boeto'], 'name' => 'Villa Serrano', 'code' => 'VSE'],

            // Chuquisaca - Luis Calvo
            ['province_id' => $provinces['Luis Calvo'], 'name' => 'Villa Vaca Guzmán', 'code' => 'VVG'],
            ['province_id' => $provinces['Luis Calvo'], 'name' => 'Macharetí', 'code' => 'MAC'],

            // La Paz - Murillo
            ['province_id' => $provinces['Murillo'], 'name' => 'La Paz', 'code' => 'LPZ'],
            ['province_id' => $provinces['Murillo'], 'name' => 'El Alto', 'code' => 'EAL'],
            ['province_id' => $provinces['Murillo'], 'name' => 'Viacha', 'code' => 'VIA'],
            ['province_id' => $provinces['Murillo'], 'name' => 'Luribay', 'code' => 'LUR'],

            // La Paz - Achacachi
            ['province_id' => $provinces['Achacachi'], 'name' => 'Achacachi', 'code' => 'ACH'],
            ['province_id' => $provinces['Achacachi'], 'name' => 'Huarina', 'code' => 'HUA'],
            ['province_id' => $provinces['Achacachi'], 'name' => 'Sapahaqui', 'code' => 'SAP'],

            // La Paz - La Paz
            ['province_id' => $provinces['La Paz'], 'name' => 'Palerмо', 'code' => 'PAL'],
            ['province_id' => $provinces['La Paz'], 'name' => 'San Pedro', 'code' => 'SPD'],

            // La Paz - Ingavi
            ['province_id' => $provinces['Ingavi'], 'name' => 'Tiquina', 'code' => 'TIQ'],
            ['province_id' => $provinces['Ingavi'], 'name' => 'Sorata', 'code' => 'SOR'],

            // La Paz - Los Andes
            ['province_id' => $provinces['Los Andes'], 'name' => 'Pucarani', 'code' => 'PUC'],
            ['province_id' => $provinces['Los Andes'], 'name' => 'Batallas', 'code' => 'BAT'],

            // La Paz - Camacho
            ['province_id' => $provinces['Camacho'], 'name' => 'Puerto Acosta', 'code' => 'PUA'],
            ['province_id' => $provinces['Camacho'], 'name' => 'Humanata', 'code' => 'HUM'],

            // La Paz - Manco Kapac
            ['province_id' => $provinces['Manco Kapac'], 'name' => 'Copacabana', 'code' => 'COP'],
            ['province_id' => $provinces['Manco Kapac'], 'name' => 'Tito Yupanqui', 'code' => 'TYU'],

            // Cochabamba - Cercado
            ['province_id' => $provinces['Cercado'], 'name' => 'Cochabamba', 'code' => 'CBB'],
            ['province_id' => $provinces['Cercado'], 'name' => 'Colcapirhua', 'code' => 'COL'],
            ['province_id' => $provinces['Cercado'], 'name' => 'Quillacollo', 'code' => 'QUI'],
            ['province_id' => $provinces['Cercado'], 'name' => 'Sacaba', 'code' => 'SAC'],
            ['province_id' => $provinces['Cercado'], 'name' => 'Tiquipaya', 'code' => 'TIQ'],

            // Cochabamba - Chapare
            ['province_id' => $provinces['Chapare'], 'name' => 'Chapare', 'code' => 'CHA'],
            ['province_id' => $provinces['Chapare'], 'name' => 'Villa Tunari', 'code' => 'VTU'],
            ['province_id' => $provinces['Chapare'], 'name' => 'Cochabamba', 'code' => 'CBC'],

            // Cochabamba - Arani
            ['province_id' => $provinces['Arani'], 'name' => 'Arani', 'code' => 'ARN'],
            ['province_id' => $provinces['Arani'], 'name' => 'Vacas', 'code' => 'VAC'],

            // Cochabamba - Punata
            ['province_id' => $provinces['Punata'], 'name' => 'Punata', 'code' => 'PUN'],
            ['province_id' => $provinces['Punata'], 'name' => 'Tacachi', 'code' => 'TAC'],

            // Cochabamba - Quillacollo
            ['province_id' => $provinces['Quillacollo'], 'name' => 'Quillacollo', 'code' => 'QLL'],
            ['province_id' => $provinces['Quillacollo'], 'name' => 'Sipe Sipe', 'code' => 'SIP'],

            // Cochabamba - Tapacarí
            ['province_id' => $provinces['Tapacarí'], 'name' => 'Tapacarí', 'code' => 'TPC'],

            // Oruro - Cercado
            ['province_id' => $provinces['Cercado'], 'name' => 'Oruro', 'code' => 'ORU'],
            ['province_id' => $provinces['Cercado'], 'name' => 'Caracollo', 'code' => 'CAR'],
            ['province_id' => $provinces['Cercado'], 'name' => 'El Cruce', 'code' => 'ECR'],

            // Oruro - Avaroa
            ['province_id' => $provinces['Avaroa'], 'name' => 'Challapata', 'code' => 'CHA'],
            ['province_id' => $provinces['Avaroa'], 'name' => 'Santuario de Quillacas', 'code' => 'SQA'],

            // Oruro - Mejillones
            ['province_id' => $provinces['Mejillones'], 'name' => 'La Riviera', 'code' => 'LRV'],
            ['province_id' => $provinces['Mejillones'], 'name' => 'Mejillones', 'code' => 'MEJ'],

            // Oruro - Sajama
            ['province_id' => $provinces['Sajama'], 'name' => 'Curahuara de Carangas', 'code' => 'CUC'],
            ['province_id' => $provinces['Sajama'], 'name' => 'Turco', 'code' => 'TUR'],

            // Oruro - Poopó
            ['province_id' => $provinces['Poopó'], 'name' => 'Poopó', 'code' => 'POO'],
            ['province_id' => $provinces['Poopó'], 'name' => 'Pampa Arguija', 'code' => 'PAR'],

            // Oruro - Litoral
            ['province_id' => $provinces['Litoral'], 'name' => 'COSTA', 'code' => 'COS'],
            ['province_id' => $provinces['Litoral'], 'name' => 'Eucaliptus', 'code' => 'EUC'],

            // Potosí - Cercado
            ['province_id' => $provinces['Cercado'], 'name' => 'Potosí', 'code' => 'PTS'],
            ['province_id' => $provinces['Cercado'], 'name' => 'Tupiza', 'code' => 'TUP'],

            // Potosí - Charcas
            ['province_id' => $provinces['Charcas'], 'name' => 'San Pedro de Buena Vista', 'code' => 'SPB'],
            ['province_id' => $provinces['Charcas'], 'name' => 'Toro Toro', 'code' => 'TTO'],

            // Potosí - Alonso de Ibáñez
            ['province_id' => $provinces['Alonso de Ibáñez'], 'name' => 'Villa de Caracotas', 'code' => 'VCC'],

            // Potosí - José María Linares
            ['province_id' => $provinces['José María Linares'], 'name' => 'Puna', 'code' => 'PUN'],
            ['province_id' => $provinces['José María Linares'], 'name' => 'Caiza "D"', 'code' => 'CAI'],

            // Potosí - Cornelio Saavedra
            ['province_id' => $provinces['Cornelio Saavedra'], 'name' => 'Betanzos', 'code' => 'BET'],
            ['province_id' => $provinces['Cornelio Saavedra'], 'name' => 'Potosí', 'code' => 'PTO'],

            // Potosí - Modesto Omiste
            ['province_id' => $provinces['Modesto Omiste'], 'name' => 'Villazón', 'code' => 'VIL'],

            // Potosí - Rudolfo Siles
            ['province_id' => $provinces['Rudolfo Siles'], 'name' => 'Uyuni', 'code' => 'UYU'],
            ['province_id' => $provinces['Rudolfo Siles'], 'name' => 'Colchani', 'code' => 'COL'],

            // Tarija - Cercado
            ['province_id' => $provinces['Cercado'], 'name' => 'Tarija', 'code' => 'TJA'],
            ['province_id' => $provinces['Cercado'], 'name' => 'San Lorenzo', 'code' => 'SLO'],

            // Tarija - Gran Chaco
            ['province_id' => $provinces['Gran Chaco'], 'name' => 'Yacuiba', 'code' => 'YAC'],
            ['province_id' => $provinces['Gran Chaco'], 'name' => 'Villa Montes', 'code' => 'VMO'],
            ['province_id' => $provinces['Gran Chaco'], 'name' => 'Villamontes', 'code' => 'VIL'],

            // Tarija - Avilés
            ['province_id' => $provinces['Avilés'], 'name' => 'Uriondo', 'code' => 'URI'],
            ['province_id' => $provinces['Avilés'], 'name' => 'Concepción', 'code' => 'CON'],

            // Tarija - Méndez
            ['province_id' => $provinces['Méndez'], 'name' => 'Entre Ríos', 'code' => 'ETR'],

            // Tarija - O'Connor
            ['province_id' => $provinces["O'Connor"], 'name' => 'Bermejo', 'code' => 'BER'],
            ['province_id' => $provinces["O'Connor"], 'name' => 'Caraparí', 'code' => 'CAR'],

            // Santa Cruz - Andrés Ibáñez
            ['province_id' => $provinces['Andrés Ibáñez'], 'name' => 'Santa Cruz', 'code' => 'SCZ'],
            ['province_id' => $provinces['Andrés Ibáñez'], 'name' => 'Montero', 'code' => 'MON'],
            ['province_id' => $provinces['Andrés Ibáñez'], 'name' => 'Warnes', 'code' => 'WAR'],
            ['province_id' => $provinces['Andrés Ibáñez'], 'name' => 'El Torno', 'code' => 'ETO'],

            // Santa Cruz - Cordillera
            ['province_id' => $provinces['Cordillera'], 'name' => 'Laguna', 'code' => 'LAG'],
            ['province_id' => $provinces['Cordillera'], 'name' => 'Cabezas', 'code' => 'CAB'],

            // Santa Cruz - Vallegrande
            ['province_id' => $provinces['Vallegrande'], 'name' => 'Vallegrande', 'code' => 'VGR'],
            ['province_id' => $provinces['Vallegrande'], 'name' => 'Pucará', 'code' => 'PUC'],

            // Santa Cruz - Chiquitos
            ['province_id' => $provinces['Chiquitos'], 'name' => 'San José de Chiquitos', 'code' => 'SJC'],
            ['province_id' => $provinces['Chiquitos'], 'name' => 'San Ramón', 'code' => 'SRN'],

            // Santa Cruz - Sara
            ['province_id' => $provinces['Sara'], 'name' => 'Portachuelo', 'code' => 'POR'],
            ['province_id' => $provinces['Sara'], 'name' => 'Santa Rosa', 'code' => 'SR'],

            // Santa Cruz - Guarayos
            ['province_id' => $provinces['Guarayos'], 'name' => 'Ascensión de Guarayos', 'code' => 'AGU'],

            // Santa Cruz - Ichilo
            ['province_id' => $provinces['Ichilo'], 'name' => 'Buena Vista', 'code' => 'BVI'],
            ['province_id' => $provinces['Ichilo'], 'name' => 'San Carlos', 'code' => 'SCA'],

            // Santa Cruz - Cabantuyacu
            ['province_id' => $provinces['Cabantuyacu'], 'name' => 'San Ignacio', 'code' => 'SIN'],

            // Beni - Cercado
            ['province_id' => $provinces['Cercado'], 'name' => 'Trinidad', 'code' => 'TRI'],
            ['province_id' => $provinces['Cercado'], 'name' => 'Loreto', 'code' => 'LOR'],

            // Beni - Vaca Díez
            ['province_id' => $provinces['Vaca Díez'], 'name' => 'Riberalta', 'code' => 'RIB'],
            ['province_id' => $provinces['Vaca Díez'], 'name' => 'Guayaramerín', 'code' => 'GUA'],

            // Beni - Ballivián
            ['province_id' => $provinces['Ballivián'], 'name' => 'Reyes', 'code' => 'REY'],
            ['province_id' => $provinces['Ballivián'], 'name' => 'San Borja', 'code' => 'SBO'],

            // Beni - Yacuma
            ['province_id' => $provinces['Yacuma'], 'name' => 'Rurrenabaque', 'code' => 'RUR'],
            ['province_id' => $provinces['Yacuma'], 'name' => 'San Ramón', 'code' => 'SRM'],

            // Beni - Moxos
            ['province_id' => $provinces['Moxos'], 'name' => 'San Ignacio', 'code' => 'SII'],

            // Pando - Federico Román
            ['province_id' => $provinces['Federico Román'], 'name' => 'Cobija', 'code' => 'COB'],
            ['province_id' => $provinces['Federico Román'], 'name' => 'Porvenir', 'code' => 'POV'],

            // Pando - Madre de Dios
            ['province_id' => $provinces['Madre de Dios'], 'name' => 'San Buenaventura', 'code' => 'SBU'],
            ['province_id' => $provinces['Madre de Dios'], 'name' => 'Las Piedras', 'code' => 'LPD'],

            // Pando - Manuripi
            ['province_id' => $provinces['Manuripi'], 'name' => 'Puerto Rico', 'code' => 'PRR'],
            ['province_id' => $provinces['Manuripi'], 'name' => 'Filadelfia', 'code' => 'FIL'],

            // Pando - Porvenir
            ['province_id' => $provinces['Porvenir'], 'name' => 'Porvenir', 'code' => 'PRV'],

            // Pando - Bolpebra
            ['province_id' => $provinces['Bolpebra'], 'name' => 'Bolpebra', 'code' => 'BOL'],
        ];

        DB::table('cities')->insert($cities);
    }
}
