<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tariff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class TariffController extends Controller
{
    /**
     * @param Request $request
     * @return $this
     */
    public function index(Request $request)
    {
        $this->can('view-tariffs');

        $tariffs  = Tariff::paginate(20);

        return view('admin.tariffs.index',[
            'tariffs' => $tariffs
        ])->with('page_title',"Manage Tariff");
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|null|void
     */
    public function store(Request $request)
    {
        if($request->method() == 'GET')
            return redirect()->route('admin.settings.tariffs.index');

        $this->can('create-tariffs');

        $this->validate($request,  [
            'name' => "required|unique:tariffs,name",
            't_floor' => "required|numeric|min:0",
            't_ceiling' => "required|numeric|greater_than_field:t_floor",
            'amount' => "required|numeric",
        ],[
            't_ceiling.greater_than_field' => "The maximum value cannot be less than the minimum value"
        ]);

        //check if there's another tariff with the same bounds
        $ex = Tariff::where([
            't_floor' => $request->input('t_floor'),
            't_ceiling' => $request->input('t_ceiling'),
            'amount' => $request->input('amount'),
        ])->first();


        list($name, $floor, $ceiling, $amount) = array_values($request->only(['name','t_floor','t_ceiling','amount']));
        $tariff = Tariff::add_tariff($name, $floor, $ceiling, $amount,'active');

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => trans('messages.tariffs.created')]
            ]);

    }

    /**
     * @param Tariff $tariff
     * @param Request $request
     * @return $this
     */
    public function edit(Tariff $tariff, Request $request)
    {
        $this->can('edit-tariffs');

        return view('admin.tariffs.view',[
            'tariff' => $tariff
        ])->with('page_title',"Edit Tariff");
    }

    /**
     * @param Tariff $tariff
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|null|void
     */
    public function update(Tariff $tariff, Request $request)
    {
        $this->can('edit-tariffs');

        $v = \Validator::make($request->all(),  [
            'name' => [
                "required",
                Rule::unique('tariffs','name')
                    ->ignore($tariff->id)
            ],
            't_floor' => "required|numeric|min:0",
            't_ceiling' => "required|numeric|greater_than_field:t_floor",
            'amount' => "required|numeric",
        ],[
            't_ceiling.greater_than_field' => "The maximum value cannot be less than the minimum value"
        ]);

        //check if there's another tariff with the same bounds
        $ex = Tariff::where([
            't_floor' => $request->input('t_floor'),
            't_ceiling' => $request->input('t_ceiling'),
            'amount' => $request->input('amount'),
        ])->where("id", "<>", $tariff->id)
            ->first();

        if ($ex)
            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'danger', 'message' => trans('messages.tariffs.exists')]
                ])
                ->withInput($request->all())
                ->withErrors($v->errors());

        $otp = $this->otp($request,trans('otp.admin.update_tariff', [
            'tariff' => $tariff->name
        ]));
        if($otp){
            return $otp;
        }

        $tariff->fill( $request->only(['name','t_floor','t_ceiling','amount']));
        $tariff->save();

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => trans('messages.tariffs.updated')]
            ]);

    }
}
