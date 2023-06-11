<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\ServiceType;
use App\Models\SeviceSetting;
use App\Models\Days;
use App\Models\OffDate;

class ServiceSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Create service settings for Men Haircut
        ServiceType::create([
          
            'name' =>'Men Haircut',
            'duration' => '10',            
        ]);

              // Create service settings for Men Haircut
              ServiceType::create([
          
                'name' =>'Women Haircut',
                'duration' => '60',            
            ]);

        $serviceMenHaircut = ServiceType::where('name', 'Men Haircut')->first();
        $serviceWomenHaircut = ServiceType::where('name', 'Women Haircut')->first();

        // Create days
        $monday = Days::create(['name' => 'Monday']);
        $tuesday = Days::create(['name' => 'Tuesday']);
        $wednesday = Days::create(['name' => 'Wednesday']);
        $thursday = Days::create(['name' => 'Thursday']);
        $friday = Days::create(['name' => 'Friday']);
        $saturday = Days::create(['name' => 'Saturday']);
        $sunday = Days::create(['name' => 'Sunday']);

        // Set off days for each service
        OffDate::create([
            'service_id' => $serviceMenHaircut->id,
            'date' => Carbon::now()->addDays(2)->toDateString(),
        ]);
        OffDate::create([
            'service_id' => $serviceWomenHaircut->id,
            'date' => Carbon::now()->addDays(2)->toDateString(),
        ]);

        // Create service settings for Men Haircut
        SeviceSetting::create([
            'service_id' => $serviceMenHaircut->id,
            'day_id' => $monday->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 10,
            'cleaning_break_duration' => 5,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        SeviceSetting::create([
            'service_id' => $serviceMenHaircut->id,
            'day_id' => $tuesday->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 10,
            'cleaning_break_duration' => 5,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        SeviceSetting::create([
            'service_id' => $serviceMenHaircut->id,
            'day_id' => $wednesday->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 10,
            'cleaning_break_duration' => 5,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        SeviceSetting::create([
            'service_id' => $serviceMenHaircut->id,
            'day_id' => $thursday->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 10,
            'cleaning_break_duration' => 5,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);
        SeviceSetting::create([
            'service_id' => $serviceMenHaircut->id,
            'day_id' => $friday->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 10,
            'cleaning_break_duration' => 5,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        SeviceSetting::create([
            'service_id' => $serviceMenHaircut->id,
            'day_id' => $saturday->id,
            'opening_time' => '10:00',
            'closing_time' => '22:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 10,
            'cleaning_break_duration' => 5,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        // Create service settings for Women Haircut
        SeviceSetting::create([
            'service_id' => $serviceWomenHaircut->id,
            'day_id' => $monday->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 60,
            'cleaning_break_duration' => 10,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        SeviceSetting::create([
            'service_id' => $serviceWomenHaircut->id,
            'day_id' => $tuesday->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 60,
            'cleaning_break_duration' => 10,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        SeviceSetting::create([
            'service_id' => $serviceWomenHaircut->id,
            'day_id' => $wednesday->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 60,
            'cleaning_break_duration' => 10,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        SeviceSetting::create([
            'service_id' => $serviceWomenHaircut->id,
            'day_id' => $thursday->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 60,
            'cleaning_break_duration' => 10,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        SeviceSetting::create([
            'service_id' => $serviceWomenHaircut->id,
            'day_id' => $friday->id,
            'opening_time' => '08:00', 'closing_time' => '20:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 60,
            'cleaning_break_duration' => 10,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);
        SeviceSetting::create([
            'service_id' => $serviceWomenHaircut->id,
            'day_id' => $saturday->id,
            'opening_time' => '10:00',
            'closing_time' => '22:00',
            'max_clients_per_slot' => 3,
            'slot_duration' => 60,
            'cleaning_break_duration' => 10,
            'break_start_time' => '15:00',
            'break_end_time' => '16:00',
        ]);

        $this->command->info('Service settings seeded successfully.');
    }
}
