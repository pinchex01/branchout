@extends('layouts.user')

<?php $vue = true ?>

@section('page')
    <div class="panel panel-default">
        <div class="panel-heading">Create Organiser</div>

        <div class="panel-body">
            <organiser-application-form></organiser-application-form>
        </div>
    </div>
@endsection
