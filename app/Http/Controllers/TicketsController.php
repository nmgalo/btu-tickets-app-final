<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TrainOption;
use App\Models\OrderedTicket;
use DB;

class TicketsController extends Controller
{

    public function getTicketsTimeTable(Request $request) {
    	return DB::table("tickets AS t1")
    			->whereRaw("DATE(t1.departure_time) >= CURDATE()")
    			->leftJoin("locations AS t2", "t1.from_location_id", "=", "t2.id")
    			->leftJoin("locations AS t3", "t1.to_location_id", "=", "t3.id")
    			->select(
    				"t1.id AS ticketId", "t1.price AS ticketPrice", "t2.location_name AS fromDestinationName",
    				"t3.location_name AS toDestinationName", "t1.is_adapted AS isAdapted",
    				DB::raw("
    					UNIX_TIMESTAMP(t1.departure_time) as scheduleTime, 
    					UNIX_TIMESTAMP(t1.arrival_time) as arrivalTime, 
    					(
    						UNIX_TIMESTAMP(t1.arrival_time) - UNIX_TIMESTAMP(t1.departure_time)
    					) / 60 AS duration"), // duration დავიკიდე ცოტათი, unix თაიმიდან პრობლემა იყო წუთებზე გადაყვანა
    			)->take(30)->get();
    }


    public function getTicketDetails($ticketId) {
        if (!is_numeric($ticketId))
            return response()->json(["error" => "Wrong ticket ID"], 400);

        $train_data = DB::table("tickets AS t1")
            ->where("t1.id", $ticketId)
            ->join("ordered_tickets AS t2", "t1.id", "=", "t2.ticket_id")
            ->join("trains AS t3", "t3.id", "=", "t1.train_id")
            // ->leftJoin("tickets AS t4", "")
            ->select(
                "t2.id AS trainId", "t1.id AS ticketId"
            )->get();

        return $train_data->toArray();

        $seats = OrderedTicket::find($train_data)->seats;
        $available_seats_sum = $seats[0];

        return $available_seats_sum;
    }

}
