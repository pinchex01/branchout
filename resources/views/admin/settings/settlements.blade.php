@extends('layouts.admin_settings')

@section('page')

<div class="panel with-nav-tabs panel-default">
  <div class="panel-heading panel-n-tab-heading hidden-xs">
          <h3 class="panel-title">Settlement Settings</h3>
  </div>
                <div class="panel-heading">
                    @include('admin.settings._settings_menu')
              </div>
              <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
                      <h3 class="panel-title">Settlement Settings</h3>
              </div>
              <div class="panel-body">

                <form class="row" method="POST" enctype="multipart/form-data" action="{{route('admin.settings.general')}}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('settlement_type', 'Settlement Type') !!}
                                {!! Form::select('settlement_type',['auto'=>'Automatic','manual'=>'Manual'], settings('settlement_type') ? settings('settlement_type') : null, array('class' => 'form-control')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('settlement_schedule', 'Settlement Schedule') !!}
                                {!! Form::select('settlement_schedule',['daily'=>'Daily','weekly'=>'Weekly','real-time'=>'Real-Time'],settings('settlement_schedule') ? settings('settlement_schedule') : null, array('class' => 'form-control')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('min_withdrawable', 'Minimum Withdrawal Amount') !!}
                                {!! Form::number('min_withdrawable',settings('min_withdrawable') ? settings('min_withdrawable') : null, array('class' => 'form-control')) !!}
                            </div>
                        </div>

                    </div>
                    {!! csrf_field() !!}
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>

              </div>

</div>

@stop
