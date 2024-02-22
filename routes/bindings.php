<?php

Route::bind('organiser', function ($value) {
    try {
        return App\Models\Organiser::where('slug', $value)
            ->where('type','organiser')
            ->firstOrFail();
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, "Organiser not found");
    }
});

Route::bind('agent', function ($value) {
    try {
        return App\Models\Organiser::where('slug', $value)
            ->where('type','sales-agent')
            ->firstOrFail();
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, "Sales agent not found");
    }
});

Route::bind('organiser_id', function ($value) {
    try {
        return App\Models\Organiser::where('id', $value)
            ->where('type','organiser')
            ->firstOrFail();
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, "Organiser not found");
    }
});

Route::bind('agent_id', function ($value) {
    try {
        return App\Models\Organiser::where('id', $value)
            ->where('type','sales-agent')
            ->firstOrFail();
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, "Organiser not found");
    }
});

Route::bind('event_slug', function ($value) {
    try {
        return App\Models\Event::where('slug', $value)
            ->firstOrFail();
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, "Event not found");
    }
});

Route::bind('attendee_uuid', function ($value) {
    try {
        return App\Models\Attendee::where('pk', $value)
            ->firstOrFail();
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, "Ticket not found");
    }
});

Route::bind('order_uuid', function ($value) {
    try {
        return App\Models\Order::where('pk', $value)
            ->firstOrFail();
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, "Page not found");
    }
});

Route::bind('order_ref_no', function ($value) {
    dd($value);
    try {
        return App\Models\Order::where('ref_no', $value)
            ->firstOrFail();
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, "Page not found");
    }
});

Route::bind('attendee_ref_no', function ($value) {
    try {
        return App\Models\Attendee::where('ref_no', $value)
            ->firstOrFail();
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, "Page not found");
    }
});