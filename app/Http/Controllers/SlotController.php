<?php

namespace App\Http\Controllers;

use App\Models\OffDate;
use App\Models\ServiceType;
use App\Models\SeviceSetting;
use App\Models\Slot;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SlotController extends Controller
{   

    public function createAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slot_id' => 'required|exists:slots,id',
            'booking_details' => 'required|array',
            'booking_details.*.email' => 'required|email',
            'booking_details.*.first_name' => 'required|string',
            'booking_details.*.last_name' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
    
        $slotId = $request->input('slot_id');
        $bookingDetails = $request->input('booking_details');
    
        // Retrieve the slot
        $slot = Slot::find($slotId);
    
       // return $slot;
        if (!$slot) {
            return response()->json(['message' => 'Slot not found'], 404);
        }
    
        // Check if the slot has reached the maximum number of clients
        $bookedPersonCount = Appointment::where('slot_id', $slotId)->count();
    
        // return $bookedPersonCount.'-'.$slot->can_book_person;
        $newBookingCount=count($bookingDetails);

       // return $newBookingCount;
        if (($bookedPersonCount >= $slot->can_book_person) || ($bookedPersonCount >= $slot->can_book_person)) {
            return response()->json(['message' => 'Slot already booked to maximum capacity'], 400);
        }

        if (($bookedPersonCount+$newBookingCount > $slot->can_book_person)) {

            $availablePerson=$slot->can_book_person-$bookedPersonCount;
            return response()->json(['message' => 'Slot can book only '.$availablePerson.' persons'], 400);
        }
    
        // Create bookings (appointments) for each person
        $appointments = [];
        foreach ($bookingDetails as $booking) {
            $email = $booking['email'];
            $firstName = $booking['first_name'];
            $lastName = $booking['last_name'];
    
            // Create an appointment for the person
            $appointment = Appointment::create([
                'slot_id' => $slotId,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
    
            $appointments[] = $appointment;
        }
    
        // Update the booked_person count in the slots table
        $slot->booked_person = $bookedPersonCount + count($bookingDetails);
        $slot->save();
    
        return response()->json(['message' => 'Booking created', 'data' => $appointments]);
    }


    public function createSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|in:1,2',
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $serviceType = $request->input('service_type');
        $date = Carbon::parse($request->input('date'))->startOfDay();
        $requested_date = $request->input('date');
        $day = $date->dayOfWeek;


        // Check if the date is an sunday   
        if ($day == 0) {
            return response()->json(['message' => 'This day is off']);
        }

        // Check if the date is an off day
        $offDate = OffDate::where('date', $requested_date)->first();

        if ($offDate) {
            return response()->json(['message' => 'This day is off']);
        }

        // Check if the requested date is within the next 7 days
        $today = Carbon::now()->startOfDay();
        $nextSevenDays = $today->copy()->addDays(7);

        if ($date->greaterThan($nextSevenDays)) {
            return response()->json(['message' => 'You can book slot with in next 7 days'], 400);
        }

        $service = ServiceType::where('id', $serviceType)->first();

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        // Check if slots already exist for the requested date and service type
        $existingSlots = Slot::where('service_type_id', $service->id)
            ->where('date', $date)
            ->get();

        if (!$existingSlots->isEmpty()) {
            return response()->json(['message' => 'Slots', 'data' => $existingSlots]);
        }

        $serviceSettings = SeviceSetting::where(['service_id' => $service->id, 'day_id' => $day])->get();

       //return $serviceSettings;
        if ($serviceSettings->isEmpty()) {
            return response()->json(['message' => 'Service settings not found'], 404);
        }

        $slots = [];

        foreach ($serviceSettings as $setting) {
            if (!$this->isSlotAvailable($setting, $date)) {
                continue;
            }

            $startTime = $this->getStartTime($setting, $date);
            $endTime = $startTime->copy()->addMinutes($setting->slot_duration);

            while ($endTime->lte($this->getClosingTime($setting, $date))) {
                // Check if the slot falls within the break time
                if ($this->isWithinBreakTime($startTime, $endTime, $setting->break_start_time, $setting->break_end_time)) {
                    $startTime->addMinutes($setting->slot_duration + $setting->cleaning_break_duration);
                    $endTime->addMinutes($setting->slot_duration + $setting->cleaning_break_duration);
                    continue;
                }

                $slots[] = [
                    'service_type_id' => $service->id,
                    'date' => $date->toDateString(),
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'can_book_person'=>$setting->max_clients_per_slot
                ];

                $startTime->addMinutes($setting->slot_duration + $setting->cleaning_break_duration);
                $endTime->addMinutes($setting->slot_duration + $setting->cleaning_break_duration);
            }
        }

        $insertedIds = Slot::insert($slots);
        $insertedRecords = Slot::where('date', $requested_date)->get();

        return response()->json(['message' => 'Slots', 'data' => $insertedRecords]);
    }

    private function isWithinBreakTime($startTime, $endTime, $breakStartTime, $breakEndTime)
    {
        $startTime = Carbon::parse($startTime)->format('H:i:s');
        $endTime = Carbon::parse($endTime)->format('H:i:s');
        $breakStartTime = Carbon::parse($breakStartTime)->format('H:i:s');
        $breakEndTime = Carbon::parse($breakEndTime)->format('H:i:s');

        //echo $startTime.' + '.$endTime.' + '.$breakStartTime.' + '.$breakEndTime;
        //echo "<br>";

        return $startTime <= $breakEndTime && $endTime >= $breakStartTime;
    }





    private function isSlotAvailable($setting, $date)
    {
        $day = $date->dayOfWeek;

        // Check if the day is a public holiday
        if ($day === 2 && $this->isPublicHoliday($date)) {
            return false;
        }

        // Check if the day is Sunday
        if ($day === 0) {
            return false;
        }

        return true;
    }

    private function getStartTime($setting, $date)
    {
        $day = $date->dayOfWeek;
        $openingTime = Carbon::parse($setting->opening_time);

        // Adjust start time based on the day
        switch ($day) {
            case 1: // Monday to Friday
                $openingTime = $openingTime->addDays($day - 1);
                break;
            case 6: // Saturday
                $openingTime = $openingTime->addDays($day - 1)->setTime(10, 0);
                break;
            default:
                $openingTime = $openingTime->addDays($day);
                break;
        }

        return $openingTime;
    }





    private function getClosingTime($setting, $date)
    {
        $day = $date->dayOfWeek;
        $closingTime = Carbon::parse($setting->closing_time);

        // Adjust closing time based on the day
        switch ($day) {
            case 1: // Monday to Friday
                $closingTime = $closingTime->addDays($day - 1);
                break;
            case 6: // Saturday
                $closingTime = $closingTime->addDays($day - 1)->setTime(22, 0);
                break;
            default:
                $closingTime = $closingTime->addDays($day);
                break;
        }

        // Subtract the cleaning break duration
        $closingTime = $closingTime->subMinutes($setting->cleaning_break_duration);

        return $closingTime;
    }



    private function isPublicHoliday($date)
    {
        // Determine if the provided date is a public holiday
        // You can implement your logic here to check for public holidays
        // For the purpose of this example, we'll assume the third day from now is a public holiday
        $thirdDayFromNow = Carbon::now()->addDays(2)->startOfDay();
        return $date->equalTo($thirdDayFromNow);
    }
}
