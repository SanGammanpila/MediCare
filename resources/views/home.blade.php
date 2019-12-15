@extends('layouts.app')

@section('content')

<div class="jumbotron jumbotron-fluid container" >
    <div class="container">
      <h1 class="display-4">Patient Id</h1>
      {{-- <p class="lead">This is a modified jumbotron that occupies the entire horizontal space of its parent.</p> --}}
      {!!Form::open(['action'=>'PatientController@getPatient','method' => 'GET'])!!}
      <div class="form-group">
          {{Form::text('id','',['placeholder' => 'Patient ID', 'name'=>'patient_id', 'class' =>'form-control form-control-lg', 'autofocus'])}}
          {{Form::hidden('text','age',['name'=>'hiddenf'])}}
      </div>
    
      {{Form::submit('submit',['class' => 'btn btn-primary btn-lg'])}}
      {!! Form::close() !!}
    </div>
  </div>

<div class="container">
</div>
@endsection