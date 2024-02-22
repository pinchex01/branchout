<?php

namespace App\Http\Controllers\Api;

use App\Models\Organiser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function getAgents(Request $request)
    {
        $agents = Organiser::query()
            ->selectRaw("organisers.id, organisers.name, organisers.email, 
            organisers.phone,organisers.about, organisers.avatar,organisers.status")
            ->agents()
            ->nameLike($request->input('q'))
            ->get();

        //modify avatar url
        $agents->each(function ($agent){
            return $agent->avatar = $agent->getAvatar();
        });

        return response([
            "status" => 'ok',
            'agents' => $agents,
            'count' => $agents->count()
        ], 200);

    }
}
