@extends('layouts.admin_settings')


@section('page')
    <div class="panel with-nav-tabs panel-default">
        <div class="panel-heading panel-n-tab-heading hidden-xs">
            <h3 class="panel-title pull-left">Banks</h3>
            <div class="pull-right">
                @permission('create-banks')
                <button class="btn btn-primary"  data-toggle="modal" data-target="#modal_bank_new"><i class="fa fa-plus"></i> New</button>
                @endpermission
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-heading">
            @include('admin.settings._settings_menu')
        </div>
        <div class="panel-heading panel-n-tab-heading hidden-sm hidden-md hidden-lg">
            <h3 class="panel-title">System Banks</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-hover dt-responsive">
                <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Accounts</th>
                    <th>Paybill</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @php($skipped = ($banks->currentPage() * $banks->perPage()) - $banks->perPage())
                @foreach($banks as $bank)
                    <tr>
                        <td>{{ $loop->iteration + $skipped }}</td>
                        <td><a  href="{{route('admin.settings.system-banks.view',[$bank->id])}}">{{$bank->name}}</a></td>
                        <td>{{$bank->accounts->count()}}</td>
                        <td>{{$bank->paybill}}</td>
                        <td>{!!  $bank->status == \App\Models\Bank::STATUS_ACTIVE? '<span class="label label-success">Active</span>' :'<span class="label label-warning">Disabled</span>'  !!}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Select <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a  href="{{route('admin.settings.system-banks.view',[$bank->id])}}">View</a>
                                    </li>
                                </ul>
                            </div>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $banks->render() !!}
        </div>

    </div>
@stop


@push('modals')
<form class="modal fade has-date-picker" id="modal_bank_new" action="{{route('admin.settings.system-banks.new')}}"  method="post" role="basic" aria-hidden="true">
    {!! csrf_field() !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Bank</h4>
            </div>
            <div class="modal-body">
                @include('forms.admin.bank_form')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i
                            class="fa fa-times-circle-o"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i>
                    Submit
                </button>
            </div>
        </div>
    </div>
</form>
@endpush

