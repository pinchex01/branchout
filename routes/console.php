<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('update', function () {

    \App\Models\Event::each(function($event){
        $sales = $event->orders()->complete()->sum('orders.amount');
        $tickets = $event->order_items()->whereHas('order',function($q){
          $q->complete();
        })->sum('order_items.quantity');
        $event->update([ 'sales_volume' => $sales,  'tickets_sold' => $tickets]);
    });
})->describe('Display an inspiring quote');
