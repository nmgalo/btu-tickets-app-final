<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Train;
use App\Models\Ticket;
use App\Models\TrainOption;
use App\Models\OrderedTicket;
use App\Models\OrderedTicketLocation;
use App\Notifications\OrderCreate;
use App\Http\Requests\TicketCreateRequest;
use App\Http\Requests\CreateTrainRequest;
use App\Http\Requests\CreateLocationRequest;


class TicketsController extends Controller
{

    public function getTicketsTimeTable() {
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
    					) / 60 AS duration"),
    			)->take(30)->get();
    }

    public function getTicketDetails($ticketId) {
        if (!is_numeric($ticketId))
            return response()->json(["error" => "Wrong ticket ID"], 400);

        // SELECT
        //     t3.id AS trainId, t1.id AS ticketId, t2.*, 
        //     t4.train_seats_count_x * t4.train_seats_count_y AS sumOfPlacesInTrain 
        // FROM tickets AS t1 
        // INNER JOIN trains AS t3 on t3.id = t1.train_id 
        // INNER JOIN ordered_tickets AS t2 on t1.id = t2.ticket_id 
        // INNER JOIN ordered_ticket_locations AS t5 on t2.id = t5.order_id 
        // INNER JOIN train_options AS t4 on t4.train_id = t3.id 
        // WHERE 
        //     t1.id = :ticketId and DATE(t1.departure_time) >= CURDATE()


        $train_matrix = [];

        $train_data = DB::table("tickets AS t1")
            ->where("t1.id", $ticketId)
            ->whereRaw("DATE(t1.departure_time) >= CURDATE()")
            ->leftJoin("trains AS t3", "t3.id", "=", "t1.train_id")
            ->leftJoin("train_options AS t4", "t4.train_id", "=", "t3.id")
            ->select(
                "t4.train_seats_count_x", "t4.train_seats_count_y",
                "t4.available_class AS availableClass", "t3.model AS trainModel",
                "t3.id AS trainId",
                "t1.price", "t1.departure_time AS departureTime", "t1.arrival_time AS arrivalTime", "t1.is_adapted"
            )
            ->first();

        $takenPlaces = DB::table("ordered_tickets AS t1")
            ->where("t1.ticket_id", $ticketId)
            ->join("ordered_ticket_locations AS t2", "t2.order_id", "=", "t1.id")
            ->select("t2.order_location_x AS orderedX", "t2.order_location_y AS orderedY")
            ->get();


        if (!$train_data || !$takenPlaces || !$train_data->trainId) 
            return response()->json(["error" => "ticket not found"], 404);

        $trainId = $train_data->trainId;

        // TODO refactor this
        for ($i = 1; $i <= $train_data->train_seats_count_x; $i++) {
            for ($j = 1; $j <= $train_data->train_seats_count_y; $j++) {
                array_push($train_matrix, [
                    "locationHashId" => 
                        base64_encode("$i;$j;$trainId;$ticketId"),
                    "location" => [$i, $j]
                ]);
            }
        }


        if (count($takenPlaces) == 0) {
            for ($x = 0; $x < count($train_matrix); $x++) {
               $train_matrix[$x]["isAvailable"] = true;
            }
        } else {
            for ($x = 0; $x < count($train_matrix); $x++) {
                for ($y = 0; $y < count($takenPlaces); $y++) {
                    if (
                        ($train_matrix[$x]["location"][0] == $takenPlaces[$y]->orderedX) && 
                        ($train_matrix[$x]["location"][1] == $takenPlaces[$y]->orderedY) 
                    )
                        $train_matrix[$x]["isAvailable"] = false; 
                    else 
                        $train_matrix[$x]["isAvailable"] = true;
                }
            }
        }


        $response = [
            "ticketId" => (int) $ticketId,
            "trainId" => $train_data->trainId,
            "vagonClass" => $train_data->availableClass,
            "trainModel" => $train_data->trainModel,
            "trainSeatsDimension" => [$train_data->train_seats_count_x, $train_data->train_seats_count_y],
            "price" => $train_data->price / 100,
            "seatsData" => $train_matrix,
            "departureTime" => $train_data->departureTime,
            "arrivalTime" => $train_data->arrivalTime,
            "isAdapter" => $train_data->is_adapted
        ];

        return $response;
        
    }


    public function createNewTicket(TicketCreateRequest $request) {

        $ticket = new \App\Models\Ticket();
        $ticket->from_location_id = $request->get("from_location_id");
        $ticket->to_location_id = $request->get("to_location_id");
        $ticket->price = $request->get("price");
        $ticket->departure_time = $request->get("departure_time");
        $ticket->arrival_time = $request->get("arrival_time");
        $ticket->is_adapted = $request->get("is_adapted");
        $ticket->train_id = $request->get("train_id");
        $res = $ticket->save();

        if ($res)
            return response()->json(["result" => "success"], 201);
        else
            return response()->json(["result" => "failed", "error" => ""], 500);
    }


    public function createNewLocation(CreateLocationRequest $request) {

        $location = new \App\Models\Location();
        $location->location_name = $request->get("location_name");
        $location->location_short_name = $request->get("location_short_name");
        $res = $location->save();


        if ($res)
            return response()->json(["result" => "success"], 201);
        return response()->json(["result" => "failed", "error" => ""], 500);

    }


    public function createNewTrain(CreateTrainRequest $request) {

        DB::beginTransaction();

        try {

            $train = Train::create([
                'model' => $request->get("model")
            ]);
            $train_id = $train->id;

            $train_options = new \App\Models\TrainOption();
            $train_options->train_id = $train_id;
            $train_options->train_seats_count_x = $request->get("train_seats_count_x");
            $train_options->train_seats_count_y = $request->get("train_seats_count_y");
            $train_options->available_class = 'econom';
            $train_options->save();

            DB::commit();

            return response()->json(["result" => "success"], 201);

        } catch (\Exception $e) {
            DB::rollback();
            // TODO remote stack object
            return response()->json(["error" => "error occurred", "stack" => $e->getMessage()], 500);
        }

    }

    public function orderSelectedPlace($locationHashId) {

        // TODO handle Exceptions   
        $ticketHashValue = explode(";", base64_decode($locationHashId));

        $locationX = (int) $ticketHashValue[0];
        $locationY = (int) $ticketHashValue[1];
        $trainId = (int) $ticketHashValue[2];
        $ticketId = (int) $ticketHashValue[3];

        $ticketData = Ticket::find($ticketId);
        $ticketPrice = $ticketData->price;

        if ($ticketPrice > \Auth::user()->balance) 
            return response()->json(["error" => "Insufficient amount"], 422);

        DB::beginTransaction();

        try {

            $orderedTickets = DB::table("ordered_tickets")
                                ->where([
                                    "ordered_ticket_locations.order_location_x" => $locationX,
                                    "ordered_ticket_locations.order_location_y" => $locationY,
                                ])
                                ->select("ordered_tickets.id", "ordered_ticket_locations.order_location_x", "ordered_ticket_locations.order_location_y")
                                ->leftJoin("ordered_ticket_locations", "ordered_tickets.id", "=", "ordered_ticket_locations.order_id")
                                ->get();

            

            if (!$orderedTickets->isEmpty() || !$orderedTickets)
                return response()->json([
                    "error" => "allready taken"
                ], 422);

            // return $orderedTickets;

            $order_virtual_id = Str::uuid()->toString();


            $userBalanceAction = \Auth::user()->update(["balance" => (\Auth::user()->balance) - $ticketData->price ]);
            $placeOrderAction = OrderedTicket::create([
                "ticket_id" => $ticketId,
                "user_id" => \Auth::id(),
                "order_id" => $order_virtual_id,
            ]);

            $placeOrderLocationAction = OrderedTicketLocation::create([
                "order_id" => $placeOrderAction->id,
                "order_location_x" => $locationX,
                "order_location_y" => $locationY,
            ]);

            $placeOrderAction->save();
            $placeOrderLocationAction->save();

            $orderIdNotification = [
                "orderId" => $order_virtual_id
            ];
            \Auth::user()->notify(new OrderCreate($orderIdNotification));

            DB::commit();

            if ($placeOrderAction && $placeOrderLocationAction && $userBalanceAction)
                return response()->json([
                    "result" => "OK",
                    "ticketUserId" => $order_virtual_id
                ], 200);
            else 
                response()->json([
                    "error" => 1
                ], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "error" => "error occurred, please try again later",
                "errorMsg" => $e->getMessage()
            ], 500);
        }

    }


    public function getTicketForUser($orderId) {
        $order = OrderedTicket::where("order_id", "=", $orderId)->get();
        if (!$order->isEmpty()) {
            $order = $order[0];
            return view("ticket", compact("order"));
        }
    }


}
