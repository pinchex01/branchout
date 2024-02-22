@extends('layouts.admin')

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">Application Details</div>
            <div class="tool pull-right">
                <a class="btn btn-default" href="{{ route('admin.tasks.queue') }}">
                    <i class="fa fa-arrow-left"></i> Back to Tasks
                </a>
                @include('admin.applications._application_actions')
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <a href="#" class="thumbnail" style=" width: 160px !important;">
                        <img src="{{ $application->user->getAvatar() }}" alt="User Avatar">
                    </a>
                </div>
                <div class="col-md-9">
                    <div class="panel panel-default panel-inner">
                        <div class="panel-heading">
                            <div class="panel-title">
                                Applicant's Details
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-condensed" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>ID Number</th>
                                        <th>Phone No.</th>
                                        <th>Email</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{$application->user->full_name}}</td>
                                        <td>{{$application->user->id_number}}</td>
                                        <td>{{$application->user->phone}}</td>
                                        <td>{{$application->user->email}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default panel-inner">
                        <div class="panel-heading">
                            <div class="panel-title">
                                Task Details
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-condensed" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Application No.</th>
                                        <th>Type</th>
                                        <th>Submitted On</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ $application->application_no }}</td>
                                        <td>{{ $application->type == 'bank_account' ? 'Bank Account' : title_case($application->type) }}</td>
                                        <td>{{ $application->created_at }}</td>
                                        <td>{!! $application->status_label !!}</td>
                                        <td>{{ $application->assigned_to ? : ' - ' }} </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($merchant_info  = array_get($application->payload, 'organiser'))
                
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default panel-inner">
                        <div class="panel-heading">
                            <div class="panel-title">
                                Organiser Details
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-condensed" width="100%">
                                    <tbody>
                                    @foreach($merchant_info as $key => $value)
                                        <tr>
                                            <td><strong>{{$value['label']}}</strong></td>
                                            <td>{{ array_get($value, 'name',$value['value'])}}</td>
                                        </tr>
                                    @endforeach
                                    @foreach($application->uploads->where('name','Business Registration Certificate') as $download)
                                        <tr>
                                            <td><strong>{{$download->name}}</strong></td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" target="_blank" id="{{str_slug($download->name)}}" href="{{ route('admin.uploads.preview', $download->id) }}">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($bank_account_info  = array_get($application->payload, 'bank_account'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default panel-inner">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    Payment Details
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-condensed" width="100%">
                                        <tbody>
                                        @foreach($bank_account_info as $key => $value)
                                            <tr>
                                                <td><strong>{{$value['label']}}</strong></td>
                                                <td>{{ array_get($value, 'name',$value['value'])}}</td>
                                            </tr>

                                        @endforeach
                                        @foreach($application->uploads->where('name','Cheque Book Leaf') as $download)
                                            <tr>
                                                <td><strong>{{$download->name}}</strong></td>
                                                <td>
                                                    <a class="btn btn-sm btn-primary" target="_blank" id="{{str_slug($download->name)}}" href="{{ route('admin.uploads.preview', $download->id) }}">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($event_info  = array_get($application->payload, 'event'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default panel-inner">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    Event Details
                                </div>
                            </div>
                            <div class="panel-body">
                                @if($application->organiser)
                                    <div class="panel panel-default panel-inner">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                Organiser Details
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object" height="100" src="{{ $application->organiser->getAvatar() }}" alt="User Avatar">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-hover table-condensed" width="100%">
                                                            <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Phone No.</th>
                                                                <th>Email</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>{{$application->organiser->name}}</td>
                                                                <td>{{$application->organiser->phone}}</td>
                                                                <td>{{$application->organiser->email}}</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-condensed" width="100%">
                                        <tbody>
                                        @foreach($event_info as $key => $value)
                                            <tr>
                                                <td><strong>{{$value['label']}}</strong></td>
                                                <td>{{ array_get($value, 'name',$value['value'])}}</td>
                                            </tr>

                                        @endforeach
                                        @foreach($application->uploads->where('name','Cheque Book Leaf') as $download)
                                            <tr>
                                                <td><strong>{{$download->name}}</strong></td>
                                                <td>
                                                    <a class="btn btn-sm btn-primary" target="_blank" id="{{str_slug($download->name)}}" href="{{ route('admin.uploads.preview', $download->id) }}">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default panel-inner">
                        <div class="panel-heading">
                            <div class="panel-title">
                                Revision History
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-condensed" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Type of Task</th>
                                        <th>Date Created</th>
                                        <th>Assigned To</th>
                                        <th>Processed At</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>{{ $task->name }}</td>
                                            <td>{{ $task->created_at }}</td>
                                            <td>{{ $task->user }}</td>
                                            <td>{{ $task->completed_at }}</td>
                                        </tr>

                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>
    </div>


@stop


