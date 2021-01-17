# tickets-app-backend

Ticket app rest API service

## მახასიათებლები
* განრიგის ნახვა
* ბილეთის ძებნა
* დაჯავშნა
* ელექტრონული ბილეთის მიღება


## პროექტის შესახებ

ამ საიტის დახმარებით სახლიდან გაუსვლელად შეგვიძლია ზუსტი, დეტალური ინფორმაცია მივიღოთ რკინიგზის განრიგზე, შევიძინოთ ბილეთები, ავირჩიოთ სასურველი ადგილი სხვადასხვა კლასში და მივიღოთ ყველა საჭირო თუ აუცილებელი ინფორმაცია თითოეული სადგურის, მატარებლისა თუ ტექნიკური მახასიათებლის შესახებ.

<!-- პროექტის წერის ფარგლებში წამოიჭრა რამდენიმე მნიშვნელოვანი პრობლემა, ამიტომ მთლიანი სურათის აღსაქმელად დოკუმენტში განვიხილავ მათაც და წარმოვადგენ მათი გადაჭრის გზებსაც.

... -->

<a href="https://github.com/nmgalo/btu-tickets-app-final-front" target="_blank">
	Front-end
</a>



<br>

## routes list

<div style="overflow-x: scroll;">
<pre>

+--------+----------+--------------------------------------------------------------------+------+------------------------------------------------------------+------------------+
| Domain | Method   | URI                                                                | Name | Action                                                     | Middleware       |
+--------+----------+--------------------------------------------------------------------+------+------------------------------------------------------------+------------------+
|        | GET|HEAD | /                                                                  |      | Closure                                                    | web              |
|        | POST     | api/admin/location/create                                          |      | App\Http\Controllers\TicketsController@createNewLocation   | api              |
|        |          |                                                                    |      |                                                            | jwt.verify.admin |
|        | POST     | api/admin/ticket/create                                            |      | App\Http\Controllers\TicketsController@createNewTicket     | api              |
|        |          |                                                                    |      |                                                            | jwt.verify.admin |
|        | POST     | api/admin/train/create                                             |      | App\Http\Controllers\TicketsController@createNewTrain      | api              |
|        |          |                                                                    |      |                                                            | jwt.verify.admin |
|        | GET|HEAD | api/admin/user                                                     |      | App\Http\Controllers\UserController@getAuthenticatedUser   | api              |
|        |          |                                                                    |      |                                                            | jwt.verify.admin |
|        | POST     | api/balance/top-up                                                 |      | App\Http\Controllers\PaymentController@topUpUserBalance    | api              |
|        | POST     | api/passenger/login                                                |      | App\Http\Controllers\UserController@authenticate           | api              |
|        | POST     | api/passenger/register                                             |      | App\Http\Controllers\UserController@register               | api              |
|        | GET|HEAD | api/v1/tickets/order-tickets/order-selected-place/{locationHashId} |      | App\Http\Controllers\TicketsController@orderSelectedPlace  | api              |
|        |          |                                                                    |      |                                                            | jwt.verify       |
|        | GET|HEAD | api/v1/tickets/order/{ticketId}                                    |      | App\Http\Controllers\TicketsController@getTicketDetails    | api              |
|        | GET|HEAD | api/v1/tickets/ordet/seat/{locationHash}                           |      | App\Http\Controllers\TicketsController@chooseSeat          | api              |
|        | GET|HEAD | api/v1/tickets/stations/timetable                                  |      | App\Http\Controllers\TicketsController@getTicketsTimeTable | api              |
+--------+----------+--------------------------------------------------------------------+------+------------------------------------------------------------+------------------+
<pre>
</div>


## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).