<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//global routes
Route::get('uploads/{upload_path}','Api\MediaController@viewImage')->name('uploads.view');
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('login',function (){
    return redirect()->route('auth.login');
})->name('login');
Route::group(['prefix' => 'auth'], function (){
    Route::get('authorize','Auth\LoginController@loginWithAuthorizeToken')->name('auth.authorize');
    Route::get('otp','Auth\LoginController@loginWithOtp')->name('auth.otp');
    Route::post('otp','Auth\LoginController@loginWithOtp')->name('auth.otp');
    Route::get('signin','Auth\LoginController@getLogin')->name('auth.login');
    Route::post('signin','Auth\LoginController@login')->name('auth.login');
    Route::get('signup','Auth\RegisterController@getRegister')->name('auth.register');
    Route::any('signout','Auth\LoginController@logout')->name('auth.logout');
    Route::get('forgot','Auth\ForgotPasswordController@getForgotView')->name('auth.forgot');
    Route::get('reset/{reset_token}','Auth\ForgotPasswordController@validateResetToken')->name('auth.reset.token');
    Route::get('change','Auth\ForgotPasswordController@getLogin')->name('auth.reset');

    //Facebook login handlers
    Route::get('/fbredirect', 'Auth\LoginController@facebookRedirect');
    Route::get('/fbcallback', 'Auth\LoginController@facebookCallback');

    //Google login handlers
    Route::get('/gmredirect', 'Auth\LoginController@googleRedirect');
    Route::get('/gmcallback', 'Auth\LoginController@googleCallback');
});

/**
 * All Frontend routes should be wrapped here
 */
Route::get('', 'HomeController@landing')->name('app.landing');
Route::get('events', 'HomeController@allEvents')->name('app.events.list');
Route::get('events/{event_slug}', 'HomeController@viewEvent')->name('app.events.view');
Route::post('events/{event_slug}', 'HomeController@buyTickets')->name('app.events.buy');
Route::get('events/{event_slug}/order-details', 'HomeController@confirmOrderDetails')->name('app.events.confirm_order');
Route::post('events/{event_slug}/order-details', 'HomeController@confirmOrderDetails')->name('app.events.confirm_order');
Route::get('orders/{order}/checkout', 'HomeController@showCheckout')->name('app.orders.checkout');
Route::get('orders/{order}/complete', 'HomeController@checkoutSuccessful')->name('app.orders.complete');
Route::get('organisers/{organiser}', 'HomeController@viewOrganiser')->name('app.organisers.view');
Route::any('complete-payment/{payment_key}', 'HomeController@completePayment')->name('app.complete_payment');


Route::group(['prefix' => '','middleware' => 'auth'], function (){
    Route::get("change-password","Auth\LoginController@changePassword")->name("app.change_password");
    Route::post("change-password","Auth\LoginController@changePassword")->name("app.change_password");
    Route::get('home','Front\AccountController@dashboard')->name('account.dashboard');
    Route::get('organisers','Front\OrganiserController@index')->name('account.organisers.index');
    Route::get('make-organiser-application','Front\OrganiserController@create')->name('account.organisers.new');
    Route::get('make-sales-agent-application','Front\OrganiserController@createSalesAgent')->name('account.organisers.new-sales-agent');

    Route::post('events/{event_slug}/buy_tickets', 'HomeController@buyTickets')->name('app.events.buy');
    Route::get('tickets', 'Front\AccountController@listTickets')->name('app.tickets.index');
    Route::post('tickets/batch-download', 'Front\AccountController@downloadTickets')->name('app.tickets.batch_download');
    Route::get('tickets/{attendee}/download', 'Front\AccountController@downloadTicket')->name('app.tickets.download');
    Route::post('tickets/{attendee}/edit', 'Front\AccountController@editTicket')->name('app.tickets.edit');
    Route::get('orders', 'Front\AccountController@listOrders')->name('app.orders.list');
    Route::get('orders/{order}', 'Front\AccountController@viewOrder')->name('app.orders.view');
    Route::get('orders/{order_ref_no}', 'Front\AccountController@viewOrder')->name('app.orders.view');
    Route::get('sales', 'Front\AccountController@myMoney')->name('app.sales.list');
    Route::get('orders/{order_uuid}/preview', 'HomeController@orderDetails')->name('app.orders.preview');
    Route::get('my-tickets/{attendee_uuid}/download', 'Front\AccountController@downloadTicket')->name('app.tickets.preview_download');
    Route::get('my-tickets/{attendee_uuid}/view', 'Front\AccountController@viewTicket')->name('app.tickets.view');
});

/*
|----------------------------------------------------------------------------------------
| All backend routes
|---------------------------------------------------------------------------------------
 */
Route::group(['middleware' => ['auth','admin'], 'prefix' => 'backend'], function ($router) {

    Route::get('', 'Admin\AdminController@dashboard')->name('admin.dashboard');

    Route::group(['prefix' => 'roles'], function ($router) {
        Route::get('', 'Admin\RoleController@index')->name('admin.roles.index');
        Route::post('', 'Admin\RoleController@store')->name('admin.roles.new');
        Route::get('{role}/view', 'Admin\RoleController@show')->name('admin.roles.view');
    });

    Route::group(['prefix' => 'merchant-roles'], function ($router) {
        Route::get('', 'Admin\MerchantRoleController@index')->name('admin.settings.merchant-roles.index');
        Route::get('{role}/view', 'Admin\MerchantRoleController@show')->name('admin.settings.merchant-roles.view');
    });

    Route::group(['prefix' => 'users'], function ($router) {
        Route::get('', 'Admin\UserController@index')->name('admin.users.index');
        Route::get('{user}/view', 'Admin\UserController@show')->name('admin.users.view');
        Route::get('{user}/roles', 'Admin\UserController@showRoles')->name('admin.users.roles');
        Route::get('{user}/activities', 'Admin\UserController@showActivities')->name('admin.users.activities');
        Route::get('{user}/reset', 'Admin\UserController@resetPassword')->name('admin.users.reset');
    });

    Route::group(['prefix' => 'events'], function ($router) {
        Route::get('', 'Admin\EventController@index')->name('admin.events.index');
        Route::get('{event}/view', 'Admin\EventController@show')->name('admin.events.view');
    });

    Route::group(['prefix' => 'orders'], function ($router) {
        Route::get('', 'Admin\OrderController@index')->name('admin.orders.index');
        Route::get('{order}/view', 'Admin\OrderController@show')->name('admin.orders.view');
        Route::get('{order}/notify', 'Admin\OrderController@sendNotification')->name('admin.orders.notify');
        Route::post('{order}/cancel','Admin\OrderController@cancelOrder')->name('admin.orders.cancel');
        Route::post('{order}/create-payment','Admin\OrderController@markAsPaid')->name('admin.orders.create_payment');
    });

    Route::group(['prefix' => 'payments'], function ($router) {
        Route::get('', 'Admin\PaymentController@index')->name('admin.payments.index');
        Route::post('{payment}/complete', 'Admin\PaymentController@processPayment')->name('admin.payments.complete');
    });

    Route::group(['prefix' => 'banks-accounts'], function ($router) {
        Route::get('', 'Admin\BankAccountController@index')->name('admin.bank-accounts.index');
        Route::get('{bank_account}/view', 'Admin\BankAccountController@show')->name('admin.bank-accounts.view');
        Route::get('{bank_account}/edit', 'Admin\BankAccountController@show')->name('admin.bank-accounts.edit');
        Route::get('{bank_account}/settlements', 'Admin\BankAccountController@showSettlements')->name('admin.bank-accounts.settlements');
        Route::get('{bank_account}/statements', 'Admin\BankAccountController@statements')->name('admin.bank-accounts.statements');
        Route::post('{bank_account}/statements', 'Admin\BankAccountController@statements')->name('admin.bank-accounts.statements');
        Route::post('{bank_account}/statements', 'Admin\BankAccountController@statements')->name('admin.bank-accounts.statements');
        Route::post('{bank_account}/withdraw', 'Admin\BankAccountController@statements')->name('admin.bank-accounts.withdraw');
    });

    Route::group(['prefix' => 'banks'], function ($router) {
        Route::get('', 'Admin\BankController@adminBanks')->name('admin.banks.index');
        Route::get('{bank}/view', 'Admin\BankController@viewAdminBankAccount')->name('admin.banks.view');
    });

    Route::group(['prefix' => 'uploads'], function ($router) {
        Route::get('', 'Admin\UploadController@index')->name('admin.uploads.index');
        Route::get('{upload}/view', 'Admin\UploadController@show')->name('admin.uploads.view');
        Route::get('{upload}/download', 'Admin\UploadController@download')->name('admin.uploads.download');
        Route::get('{upload}/preview', 'Admin\UploadController@preview')->name('admin.uploads.preview');
        Route::post('{upload}/delete', 'Admin\UploadController@destroy')->name('admin.uploads.delete');
    });

    Route::group(['prefix' => 'organisers'], function ($router) {
        Route::get('', 'Admin\OrganiserController@index')->name('admin.organisers.index');
        Route::get('{organiser_id}/view', 'Admin\OrganiserController@show')->name('admin.organisers.view');
        Route::get('{organiser_id}/bank-accounts', 'Admin\OrganiserController@bankAccounts')->name('admin.organisers.bank-accounts');
        Route::get('{organiser_id}/events', 'Admin\OrganiserController@events')->name('admin.organisers.events');
        Route::get('{organiser_id}/users', 'Admin\OrganiserController@users')->name('admin.organisers.users');
    });

    Route::group(['prefix' => 'tasks'], function ($router) {
        Route::get('', 'Admin\ApplicationController@index')->name('admin.tasks.index');
        Route::get('{application}/view', 'Admin\ApplicationController@show')->name('admin.tasks.view');
        Route::post('{application}/approve', 'Admin\ApplicationController@approve')->name('admin.tasks.approve');
        Route::post('{application}/reject', 'Admin\ApplicationController@reject')->name('admin.tasks.reject');
        Route::get('pick_task', 'Admin\ApplicationController@pick')->name('admin.tasks.pick');
        Route::post('{application}/review', 'Admin\ApplicationController@review')->name('admin.tasks.review');
        Route::get('queue','Admin\ApplicationController@queue')->name('admin.tasks.queue');
        Route::get('inbox','Admin\ApplicationController@inbox')->name('admin.tasks.inbox');
        Route::get('completed','Admin\ApplicationController@completed')->name('admin.tasks.complete');
    });

    Route::group(['prefix' => 'settings'], function ($router) {
        Route::get('general', 'Admin\SettingsController@generalSettings')->name('admin.settings.general');
        Route::post('general', 'Admin\SettingsController@generalSettingsSave')->name('admin.settings.general');
        Route::get('ticket-design', 'Admin\SettingsController@ticketDesign')->name('admin.settings.ticket_template');
        Route::post('ticket-design', 'Admin\SettingsController@ticketDesign')->name('admin.settings.ticket_template');
        Route::get('ticket-preview', 'Admin\SettingsController@previewTicketDesign')->name('admin.settings.ticket_preview');
        Route::group(['prefix' => 'notification-templates'], function ($router) {
            Route::get('', 'Admin\TemplateController@index')->name('admin.settings.templates.index');
            Route::get('create', 'Admin\TemplateController@create')->name('admin.settings.templates.create');
            Route::post('create', 'Admin\TemplateController@store')->name('admin.settings.templates.create');
            Route::get('edit/{template}', 'Admin\TemplateController@edit')->name('admin.settings.templates.edit');
            Route::post('edit/{template}', 'Admin\TemplateController@update')->name('admin.settings.templates.edit');
            Route::patch('edit/{template}', 'Admin\TemplateController@update')->name('admin.settings.templates.edit');
            Route::get('preview/{template}', 'Admin\TemplateController@show')->name('admin.settings.templates.preview');
        });
        Route::group(['prefix' => 'tariffs'], function ($router) {
            Route::get('', 'Admin\TariffController@index')->name('admin.settings.tariffs.index');
            Route::get('create', 'Admin\TariffController@store')->name('admin.settings.tariffs.new');
            Route::post('create', 'Admin\TariffController@store')->name('admin.settings.tariffs.new')->middleware('otp');
            Route::get('edit/{tariff}', 'Admin\TariffController@edit')->name('admin.settings.tariffs.edit');
            Route::post('edit/{tariff}', 'Admin\TariffController@update')->name('admin.settings.tariffs.edit')->middleware('otp');
            Route::patch('edit/{tariff}', 'Admin\TariffController@update')->name('admin.settings.tariffs.edit')->middleware('otp');
        });
        Route::group(['prefix' => 'staff'], function ($router) {
            Route::get('', 'Admin\UserController@listStaff')->name('admin.settings.staffs.index');
            Route::get('remove', 'Admin\UserControl@removeUserRole')->name('admin.settings.staffs.remove');
            Route::get('change-role', 'Admin\UserControl@changeUserRole')->name('admin.settings.staffs.change');
        });

        Route::get('payments', 'Admin\SettingsController@paymentsSettings')->name('admin.settings.payments');
        Route::post('payments', 'Admin\SettingsController@paymentsSettingsSave')->name('admin.settings.payments');
        Route::group(['prefix' => 'system-banks'], function ($router) {
            Route::get('', 'Admin\BankController@index')->name('admin.settings.banks.index');
            Route::post('', 'Admin\BankController@store')->name('admin.settings.banks.index');
            Route::get('new', 'Admin\BankController@create')->name('admin.settings.banks.new');
            Route::post('new', 'Admin\BankController@store')->name('admin.settings.banks.new');
            Route::get('{bank}/view', 'Admin\BankController@show')->name('admin.settings.banks.view');
            Route::get('{bank}/edit', 'Admin\BankController@edit')->name('admin.settings.banks.edit');
            Route::post('{bank}/edit', 'Admin\BankController@update')->name('admin.settings.banks.edit');
            Route::patch('{bank}/edit', 'Admin\BankController@update')->name('admin.settings.banks.edit');
        });
        Route::get('settlements', 'Admin\SettingsController@settlementSettings')->name('admin.settings.settlements');
        Route::post('settlements', 'Admin\SettingsController@settlementSettingsSave')->name('admin.settings.settlements');
        Route::group(['prefix' => 'superuser','middleware' => 'avenger'], function(){
            Route::get('','Admin\SettingsController@allSettings')->name('admin.settings.all');
            Route::post('','Admin\SettingsController@allSettings')->name('admin.settings.all');
            Route::post('/add','Admin\SettingsController@addSetting')->name('admin.settings.all.add');
            Route::post('/update','Admin\SettingsController@updateSetting')->name('admin.settings.all.save');
        });

        Route::group(['prefix' => 'merchant-roles'], function ($router) {
        Route::get('', 'Admin\MerchantRoleController@index')->name('admin.settings.merchant-roles.index');
        Route::post('', 'Admin\MerchantRoleController@store')->name('admin.settings.merchant-roles.index');
        Route::get('new', 'Admin\MerchantRoleController@create')->name('admin.settings.merchant-roles.new');
        Route::post('new', 'Admin\MerchantRoleController@store')->name('admin.settings.merchant-roles.new');
        Route::get('{role}/view', 'Admin\MerchantRoleController@show')->name('admin.settings.merchant-roles.view');
        Route::post('{role}/view', 'Admin\MerchantRoleController@update')->name('admin.settings.merchant-roles.view');
        Route::patch('{role}/view', 'Admin\MerchantRoleController@update')->name('admin.settings.merchant-roles.view');
    });
    });
});

/**
 * All Agent routes
 */
Route::group(['prefix' => '{agent}/sales-agent','middleware' => ['auth','agent']], function (){
    Route::get('dashboard','Agent\AgentController@dashboard')->name('agent.dashboard');
    Route::group(['prefix' => 'events'], function (){
        Route::get('','Agent\EventController@index')->name('agent.events.index');
        Route::get('browse','Agent\EventController@browse')->name('agent.events.browse');
        Route::get('history','Agent\EventController@browse')->name('agent.events.history');
        Route::get('{event}/details','Agent\EventController@show')->name('agent.events.view');
        Route::get('{event}/buy','Agent\EventController@show')->name('agent.events.view');
        Route::post('{event}/become-an-agent','Agent\EventController@becomeAgent')->name('agent.events.become_agent');
        Route::post('{event}/buy_tickets', 'Agent\OrderController@buyTickets')->name('agent.events.buy');
        Route::get('{event}/complete-order', 'Agent\OrderController@confirmOrderDetails')->name('agent.events.confirm_order');
        Route::post('{event}/complete-order', 'Agent\OrderController@confirmOrderDetails')->name('agent.events.confirm_order');
    });
    Route::group(['prefix' => 'orders'], function (){
        Route::get('','Agent\OrderController@index')->name('agent.orders.index');
        Route::get('{order}/details','Agent\OrderController@show')->name('agent.orders.view');
    });

    Route::group(['prefix' => 'accounts'], function (){
        Route::get('','Agent\BankAccountController@index')->name('agent.bank-accounts.index');
        Route::get('{bank_account}/details','Agent\BankAccountController@show')->name('agent.bank-accounts.view');
        Route::get('{bank_account}/statements','Agent\BankAccountController@statements')->name('agent.bank-accounts.statements');
        Route::get('{bank_account}/settlements','Agent\BankAccountController@show')->name('agent.bank-accounts.settlements');
        Route::get('{bank_account}/edit','Agent\BankAccountController@show')->name('agent.bank-accounts.edit');
        Route::post('{bank_account}/make-withdrawal','Agent\BankAccountController@withdraw')->name('agent.bank-accounts.withdraw');
    });
});

/**
 * All Organiser routes
 */
Route::group(['prefix' => '{organiser}/organiser','middleware' => ['auth','merchant']], function (){
    Route::get('dashboard','Organiser\OrganiserController@dashboard')->name('organiser.dashboard');
    Route::group(['prefix' => 'events'], function (){
        Route::get('','Organiser\EventController@index')->name('organiser.events.index');
        Route::get('new','Organiser\EventController@create')->name('organiser.events.new');
        Route::get('{event}/dashboard','Organiser\EventController@show')->name('organiser.events.view');
        Route::get('{event}/edit','Organiser\EventController@edit')->name('organiser.events.edit');
        /**
         * Event specific controllers
         */
        Route::group(['prefix' => '{event}/tickets'], function (){
            Route::get('','Organiser\TicketController@index')->name('organiser.tickets.index');
            Route::get('new','Organiser\TicketController@create')->name('organiser.tickets.new');
            Route::get('{ticket}/edit','Organiser\TicketController@edit')->name('organiser.tickets.edit');
        });

        Route::group(['prefix' => '{event}/orders'], function (){
            Route::get('','Organiser\OrderController@index')->name('organiser.orders.index');
            Route::get('{order}/details','Organiser\OrderController@show')->name('organiser.orders.view');
            Route::get('{order}/preview','Organiser\OrderController@previewTicket')->name('organiser.orders.preview');
            Route::post('{order}/create-payment','Organiser\OrderController@markAsPaid')->name('organiser.orders.create_payment');
            Route::get('{order}/notify','Organiser\OrderController@sendNotification')->name('organiser.orders.notify');
        });

        Route::group(['prefix' => '{event}/attendees'], function (){
            Route::get('','Organiser\AttendeeController@show')->name('organiser.attendees.index');
            Route::post('','Organiser\AttendeeController@checkIn')->name('organiser.attendees.check_in');
        });

        Route::group(['prefix' => '{event}/sales'], function (){
            Route::get('','Organiser\EventController@listSalesPeople')->name('organiser.sales.index');
        });

    });
    Route::group(['prefix' => 'accounts'], function (){
        Route::get('','Organiser\BankAccountController@index')->name('organiser.bank-accounts.index');
        Route::get('{bank_account}/details','Organiser\BankAccountController@show')->name('organiser.bank-accounts.view');
        Route::get('{bank_account}/statements','Organiser\BankAccountController@statements')->name('organiser.bank-accounts.statements');
        Route::get('{bank_account}/settlements','Organiser\BankAccountController@show')->name('organiser.bank-accounts.settlements');
        Route::get('{bank_account}/edit','Organiser\BankAccountController@show')->name('organiser.bank-accounts.edit');
        Route::post('{bank_account}/make-withdrawal','Organiser\BankAccountController@withdraw')->name('organiser.bank-accounts.withdraw');
    });
});
