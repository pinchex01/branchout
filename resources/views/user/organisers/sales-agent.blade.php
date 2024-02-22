@extends('layouts.user')

<?php $vue = true ?>

@section('page')
@if ($applications->count())
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>Sales Agent Application</h3>
            <div class="pull-right">
                <button data-toggle="modal" data-target="#md_org_new" class="btn btn-primary btn-sm" ><i class="fa fa-plus"></i> Make Application </button>
                @push('modals')
                <div class="modal fade" id="md_org_new" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="exampleModalLabel">Make Sales Agent Application</h4>
                            </div>
                            <div class="modal-body">
                                <sales-agent-application-form :allow_individual="'true'"></sales-agent-application-form>
                            </div>
                        </div>
                    </div>
                </div>
                @endpush

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped  table-hover dt-responsive">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th class="hidden-xs">Date Submitted</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td>
                                {{ $application->name }}
                                <small class="visible-xs text-muted">{{date('M d, Y', strtotime($application->created_at))}}</small>
                            </td>
                            <td class="hidden-xs">{{date('M d, Y', strtotime($application->created_at))}}</td>
                            <td>
                               {!! $application->status_label !!}
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="3">No records found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
        <div class="panel panel-default">
            <div class="panel-heading">Create your sales agent profile</div>

            <div class="panel-body">
                <sales-agent-application-form :allow_individual="true"></sales-agent-application-form>
            </div>
        </div>
    @endif

@if ($organisers->where('type','sales-agent')->count())
<div class="panel panel-default">
    <div class="panel-heading">
        <h3>Sales Agent</h3>
        <div class="pull-right">

        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped  table-hover dt-responsive">
                <thead>
                <tr>
                    <th>Name</th>
                    <th class="hidden-xs">Email</th>
                    <th class="hidden-xs">Phone</th>
                    <th class="hidden-xs">Date Joined</th>
                    <th class="hidden-xs">Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($organisers->where('type','sales-agent') as $organiser)
                    <tr>
                        <td>
                            @if($organiser->status == 'active')
                                <a  href="{{route('agent.dashboard',[$organiser->slug])}}">{{$organiser->name }}</a>
                                <small class="visible-xs text-muted"> {{ $organiser->email}}</small>
                                <small class="visible-xs text-muted"> {{ $organiser->phone}}</small>
                            @else
                                {{$organiser->name}}
                            @endif
                        </td>
                        <td class="hidden-xs">{{$organiser->email}}</td>
                        <td class="hidden-xs">{{$organiser->phone}}</td>
                        <td class="hidden-xs">{{date('M d, Y', strtotime($organiser->created_at))}}</td>
                        <td class="hidden-xs">
                            {!! $organiser->status_label !!}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="4">No records found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
