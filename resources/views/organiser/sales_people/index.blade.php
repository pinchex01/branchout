@extends('layouts.organiser')

<?php $vue = true ?>
<?php $maps = true ?>
<?php $event_menu = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">
            Sales Agents
            <div class="pull-right">
                <button href="#md_sales_frm" data-toggle="modal" class="btn btn-primary btn-sm" ><i class="fa fa-user-plus"></i> Add Agent </button>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-advance dt-responsive">
                    <thead>
                    <tr>
                        <th>Code #</th>
                        <th>Name</th>
                        <th>Tickets Sold</th>
                        <th>Total Sales</th>
                        <th>Commission</th>
                        <th>Paid</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($sales_people as $person)
                        <tr>
                            <td>{{ $person->code }}</td>
                            <td>{{$person->organiser}}</td>
                            <td>{{$person->tickets_sold}}</td>
                            <td>{{$person->total }}</td>
                            <td>{{$person->fees}}</td>
                            <td>
                                @if($person->settled)
                                    <span class="label label-success">Yes</span>
                                @else
                                    <span class="label label-danger">No</span>
                                @endif
                            </td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <p class="text-center">
                                    You don't have a sales team yet. <br>
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('modals')
<div class="modal fade" id="md_sales_frm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Add Sales Agents(s)</h4>
            </div>
            <div class="modal-body">
                <add-sales-agent-form event="{{ $event->id }}"></add-sales-agent-form>
            </div>
        </div>
    </div>
</div>
@endpush