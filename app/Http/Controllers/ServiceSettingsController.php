<?php

namespace App\Http\Controllers;

use App\Models\SeviceSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Slot;
use App\Models\Appointment;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ServiceSettingsController extends Controller
{
    public function index()
    {
        $serviceSettings = SeviceSetting::with(['service', 'day'])->get();

        return response()->json(['service_settings' => $serviceSettings]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_id' => 'required|exists:services,id',
            'day_id' => 'required|exists:days,id',
            'opening_time' => 'required',
            'closing_time' => 'required',
            'max_clients_per_slot' => 'required|integer',
            'slot_duration' => 'required|integer',
            'cleaning_break_duration' => 'required|integer',
            'break_start_time' => 'required',
            'break_end_time' => 'required',
        ]);

        $serviceSetting = SeviceSetting::create($validatedData);

        return response()->json(['service_setting' => $serviceSetting], 201);
    }

    // public function update(Request $request, $id)
    // {

    //    // return $request;

    //     $validatedData = $request->validate([
    //         'opening_time' => 'required',
    //         'closing_time' => 'required',
    //         'max_clients_per_slot' => 'required|integer',
    //         'slot_duration' => 'required|integer',
    //         'cleaning_break_duration' => 'required|integer',
    //         'break_start_time' => 'required',
    //         'break_end_time' => 'required',
    //     ]);

    //    // return "Here i am";
    //     $serviceSetting = SeviceSetting::findOrFail($id);

    //     //return $serviceSetting;
    //     $serviceSetting->update($validatedData);

    //     return response()->json(['service_setting' => $serviceSetting]);
    // }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'opening_time' => 'required',
            'closing_time' => 'required',
            'max_clients_per_slot' => 'required|integer',
            'slot_duration' => 'required|integer',
            'cleaning_break_duration' => 'required|integer',
            'break_start_time' => 'required',
            'break_end_time' => 'required',
        ]);

        $serviceSetting = SeviceSetting::findOrFail($id);

        // Check if slots exist for the updated day
        $day = Carbon::now()->addDays($serviceSetting->day_id)->startOfDay();
        $day = $day->format('Y-m-d');


        // Check if slot created against these settings
        $existingSlots = Slot::where('service_type_id', $serviceSetting->service_id)
            ->where('date', $day)
            ->exists();
            
        if ($existingSlots) {
            return response()->json(['message' => 'Cannot update settings. Slots already exist for the day.'], 400);
        }

        // Check if bookings exist for the updated day
        $existingBookings = Appointment::whereHas('slot', function (Builder $query) use ($serviceSetting,$day) {
            $query->where('service_type_id', $serviceSetting->service_id)
                ->where('date', $day);
        })->exists();

        if ($existingBookings) {
            return response()->json(['message' => 'Cannot update settings. Bookings already exist for the day.'], 400);
        }

        // Update the service setting
        $serviceSetting->update($validatedData);

        return response()->json(['service_setting' => $serviceSetting]);
    }


    public function destroy($id)
    {
        $serviceSetting = SeviceSetting::findOrFail($id);
        $serviceSetting->delete();

        return response()->json(['message' => 'Service setting deleted successfully']);
    }
}
