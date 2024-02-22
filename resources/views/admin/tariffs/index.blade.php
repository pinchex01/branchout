@extends('layouts.admin')

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">
                <span class="caption-subject bold uppercase"> Tariffs </span>
            </div>
            <div class="tools pull-right">
                @permission('create-tariffs')
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#frm-tf-new">
                    <i class="fa fa-plus"> New Tariff</i>
                </button>
                @push('modals')
                <form class="modal fade" id="frm-tf-new" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                      method="post" action="{{route('admin.settings.tariffs.new')}}">
                    {!! csrf_field() !!}
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="exampleModalLabel">Add New Tariff</h4>
                            </div>
                            <div class="modal-body">
                                @include('forms.admin.tariff_form')
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary submit-btn">Add</button>
                            </div>
                        </div>
                    </div>
                </form>

                @endpush
                @endpermission
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Charge</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($tariffs as $tariff)
                        <tr>
                            <td>{{$tariff->name}}</td>
                            <td>{{$tariff->t_floor}}</td>
                            <td>{{$tariff->t_ceiling}}</td>
                            <td>{{ $tariff->amount}}</td>
                            <td>{!! $tariff->status  == 'active' ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Disabled</span>' !!}</td>
                            <td>
                                @permission('edit-tariffs')
                                    <a href="{{ route('admin.settings.tariffs.edit', $tariff->id) }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-edit"></i> edit</a>
                                @endpermission
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No  uploads</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {!! $tariffs->render() !!}
        </div>
    </div>
@stop
