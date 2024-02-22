<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('oauth/lookup','Api\UserController@lookup');
Route::post('oauth/signin','Api\UserController@signin');
Route::post('oauth/users/register','Api\UserController@register');
Route::post('oauth/forgot','Auth\ForgotPasswordController@getUser');
Route::post('oauth/forgot/request','Auth\ForgotPasswordController@forgotPasswordRequest');
Route::post('oauth/forgot/code','Auth\ForgotPasswordController@validateResetCode');
Route::post('oauth/change','Auth\ForgotPasswordController@changePassword');
Route::post('buy-tickets','Api\OrderController@buyTickets');
Route::post('confirm-order','Api\OrderController@confirmOrder');
Route::post('order-checkout','Api\OrderController@getCheckoutPayload');
Route::post('complete-payment','Api\PaymentController@completePayment');
Route::post('purchase-ticket','Api\OrderController@createFromPayment')->name('api.purchase_ticket');
Route::post('validate-purchase','Api\OrderController@validateTicketPurchase');
Route::post('pesaflow-ipn','Api\PaymentController@pesaflowIpnEndpoint')->name('api.pesaflow-ipn');
Route::post('checkout','Api\PaymentController@completeCheckout');
Route::get('events','Api\EventController@index')->name('api.events.index');

Route::group(['prefix' => 'search'], function (){
    Route::get('agents','Api\SearchController@getAgents')->name('api.search.agents');
});


Route::post('uploads/avatar','Api\MediaController@uploadAvatar')->middleware('api.auth');
Route::post('uploads/file','Api\MediaController@upload')->middleware('api.upload');

Route::post('users/fetch','Api\UserController@getUserWithIDAndName')->name('api.users.fetch');
Route::post('users/get','Api\UserController@getByUsername')->name('api.users.get');

Route::group(['prefix' => 'users', 'middleware' => 'api.auth'], function (){
    Route::post('new','Api\UserController@addUser')->name('api.users.new');
    Route::post('add-role','Api\UserController@addUserToRole')->name('api.users.new.role');
});

Route::group(['prefix' => 'organisers', 'middleware' => 'api.auth'], function (){
    Route::post('create','Api\OrganiserController@createOrganiserApplication')->name('api.organisers.create');
    Route::post('create-sales-agent','Api\OrganiserController@createSalesAgentApplication')->name('api.organisers.create-sales-agent');
    Route::post('new','Api\OrganiserController@store')->name('api.organisers.new');
});
Route::group(['prefix' => 'events', 'middleware' => 'api.auth'], function (){
    Route::post('create-application','Api\EventController@create')->name('api.events.create');
    Route::post('new','Api\EventController@store')->name('api.events.new');
    Route::post('{event}/edit','Api\EventController@update')->name('api.events.new');
    Route::post('{event}/change-status','Api\EventController@toggleEventStatus')->name('api.events.status');
    Route::post('{event}/add-sales-agent','Api\EventController@addSalesPerson')->name('api.events.sales-person.new');
});
Route::group(['prefix' => 'tickets', 'middleware' => 'api.auth'], function (){
    Route::post('new','Api\TicketController@store')->name('api.tickets.new');
    Route::get('{ticket}/details','Api\TicketController@details')->name('api.tickets.view');
    Route::post('{ticket}/edit','Api\TicketController@update')->name('api.tickets.edit');
});
Route::group(['prefix' => 'orders', 'middleware' => 'api.auth'], function (){
    Route::post('create', 'Api\OrderController@createOrderManual')->name('api.orders.create');
    Route::get('ticket-info/{ticket_no}','Api\OrderController@getTicketInfo')->name('api.order.ticket_info');
    Route::post('check-in','Api\OrderController@checkInTicket')->name('api.order.check_in');
    Route::post('{order}/add-attendee','Api\OrderController@addAttendee')->name('api.orders.attendees.new');
});
Route::group(['prefix' => 'bank-accounts', 'middleware' => 'api.auth'], function (){
    Route::post('new','Api\BankAccountController@store')->name('api.bank-accounts.new');
});

Route::group(['prefix' => 'comments', 'middleware' => 'api.auth'], function (){
    Route::post('new','Api\CommentController@store')->name('api.comments.new');
});

Route::group(['prefix' => 'organiser/{organiser}/', 'middleware' => 'api.auth.organiser'], function(){
    
    
    Route::group(['prefix' => "events"], function(){
        Route::get("", "Api\EventController@orgListEvents");
        Route::group([ 'prefix' =>'{event}/'], function(){
            Route::get("", "Api\EventController@orgEventDetails");

            Route::get('orders', 'Api\OrderController@orgOrders');
            Route::get('orders/{order}', 'Api\OrderController@orgViewOrder');

            Route::get('tickets', 'Api\AttendeeController@orgListAttendees');
            Route::get('tickets/{ticket_no}', 'Api\AttendeeController@orgGetTicketInfo');
            Route::post('tickets', 'Api\AttendeeController@orgCheckInTicket');
        });
        
    });
});
