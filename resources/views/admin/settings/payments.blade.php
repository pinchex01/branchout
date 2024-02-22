@extends('admin.layouts.settings')

@section('settings')
    <form class="row" method="POST" enctype="multipart/form-data" action="{{route('admin.settings.general')}}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('currency', 'Currency') !!}
                    {!! Form::select('currency',['KES'=>'Shillings','USD'=>'US Dollar','EUR'=>'Euro'], settings('currency') ? settings('currency') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('default_due_date', 'Default Invoice Due Day') !!}
                    {!! Form::selectRange('default_due_date',1,31,settings('default_due_date') ? settings('default_due_date') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('default_settlement_date', 'Default Settlement Day') !!}
                    {!! Form::selectRange('default_settlement_date',1,31,settings('default_settlement_date') ? settings('default_settlement_date') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('minimum_rent', 'Minimum Rent Amount') !!}
                    {!! Form::text('minimum_rent', settings('minimum_rent') ? settings('minimum_rent') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('commission', 'Default Commission') !!}
                    {!! Form::text('commission', settings('commission') ? settings('commission') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('convenience_fee', 'Enable Pesaflow Convenience Fee') !!}
                    {!! Form::select('convenience_fee',['0'=>'Disable','1'=>'Enable'],settings('convenience_fee') ? settings('convenience_fee') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('tax', 'Enable TAX') !!}
                    {!! Form::select('tax',['0'=>'Disable','1'=>'Enable'],settings('tax') ? settings('tax') : null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('tax_account', 'TAX Destination Account') !!}
                    {!! Form::select('tax_account',[''=>'Select Account'] + $admin_accounts,settings('tax_account') ? settings('tax_account') : null, array('class' => 'form-control')) !!}
                </div>
            </div>

        </div>
        {!! csrf_field() !!}
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
@stop
