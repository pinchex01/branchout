@inject('Organiser','\App\Models\Organiser')
<?php $vue = true ?>
@extends('layouts.admin')



@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">
                <span class="caption-subject bold uppercase"> Organisers </span>
            </div>
            <div class="tools pull-right">
                <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#cs-filter"
                        aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-sliders"></i> Filter
                </button>

                @permission(['create-merchants'])
                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#md_new_merchant"
                        aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-plus"></i> New
                </button>
                @push('modals')
                <div class="modal fade" id="md_new_merchant" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="exampleModalLabel">Add Organiser</h4>
                            </div>
                            <div class="modal-body">
                                <organiser-form></organiser-form>
                            </div>
                        </div>
                    </div>
                </div>
                @endpush
                @endpermission

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="collapse {{ request()->has('filters')? 'in': '' }}" id="cs-filter">
                <form class="well">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Landlord ID</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[merch_id]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Landlord ID',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Name</label>
                            <div class="col-sm-10">
                                {!! Form::text('filters[merch_name]', null, [
                                'class' => 'form-control',
                                'placeholder'=> 'Landlord Name',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer filter-actions">
                        <button type="submit" class="btn btn-danger ">
                            Filter
                        </button>
                        <a href="{{route('admin.organisers.index')}}" class="btn btn-default">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>

            <table class="table table-striped  table-hover dt-responsive">
                <thead>
                <tr>
                    <th> #</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date Joined</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($organisers as $merchant)
                    <tr>
                        <td>{{$merchant->id}}</td>
                        <td><a href="{{route('admin.organisers.view',[$merchant->id])}}">{{$merchant->name }}</a></td>
                        <td>{{$merchant->email}}</td>
                        <td>{{$merchant->phone}}</td>
                        <td>{{date('M d, Y', strtotime($merchant->created_at))}}</td>
                        <td>{!! $merchant->status_label !!}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="{{route('admin.organisers.view',[$merchant->id])}}">View</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7">No records found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {!! $organisers->render() !!}
        </div>
    </div>
@stop

