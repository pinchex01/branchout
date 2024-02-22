<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->load_user($request);
            $this->load_organiser($request);
            $this->load_event($request);

            return $next($request);
        });
    }

    protected function load_organiser($request)
    {
        if ($organiser = organiser()){
            \JavaScript::put([
                'Organiser' => [
                    'id' => $organiser->id,
                    'name' => $organiser->name,
                    'slug' => $organiser->slug,
                    'email' => $organiser->email,
                    'phone' => $organiser->phone
                ]
            ]);
        }
    }

    protected function load_user($request)
    {
        if ($user = $request->user()){

            \JavaScript::put([
                'User' => [
                    'id' => $user->id,
                    'id_number' => $user->id_number,
                    'api_token' => $user->api_token,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'party_points' => $user->points,
                    'wallet' => $user->account->balance
                ]
            ]);
        }
    }

    protected function load_event($request)
    {
        if($event = $request->route()->parameter('event')){
            \JavaScript::put([
                'Event' => $event->toArray()
            ]);
        }
    }

    /**
     * Check if session order data has expired
     *
     * @param Event $event
     * @return array|null
     */
    public function checkout_data(Event $event)
    {
        $user = user();
        $order_session = session()->get('ticket_order_' . $event->id);

        if (!$order_session || $order_session['expires_at'] < Carbon::now()){
            return false;
        }

        return $order_session;
    }

    /**
     * Abort if permission(s) fail
     * @param string|array $permission
     * @param int $status
     */
    public function can($permission, $status  = 403)
    {
        if (!\Laratrust::can($permission)) abort($status, "Access Denied");
    }

    /**
     * Check if given permission(s) fail and closure evaluates false
     * @param string|array $permission
     * @param $closure
     * @param int $status
     * @internal param bool $strict
     */
    public function _can($permission, $closure, $status = 403)
    {
        if ((!\Laratrust::can($permission)) || !$closure() )
            abort($status, "Access Denied");
    }

    /**
     * If closure fails abort with 403
     * @param $closure
     * @param int $status
     */
    public function before($closure, $status = 403)
    {
        if (!$closure())
            abort($status, "Access Denied");
    }

    /**
     * @param array|null $input_data
     * @return Controller
     */
    public function flashInput(array $input_data = null)
    {
        \Session::flashInput($input_data);
        return $this;
    }

    /**
     * [listen_export description]
     * @param  Request $request [description]
     * @param  Builder $builder [description]
     * @param  [type]  $view    [description]
     * @return [type]           [description]
     */
    public function listen_export(Request $request, Builder $builder, $view, array $data, $name)
    {
      if($request->get('export',null) == 'pdf'){
        $items  = [ 'items' => $builder->get() ];
        $pdf = \PDF::loadView($view, $data + $items);

        return $pdf->download(str_slug($name).".pdf");
      }
    }
}
