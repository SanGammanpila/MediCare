@extends('layouts.app')

<style>
  .container{
    width: 100vw;
    max-width: 300px;
    margin: auto;
    color: brown;
    text-align: center; 
  }

  .form-login
{   
    width: 100%;
    max-width:300px;
    padding: 15px;
    margin: 0 auto;
}

</style>

@section('content')

<body>
  
<div class="jumbotron  container" >
      <h1 class="display-4">Patient Id</h1>
      {{-- <p class="lead">This is a modified jumbotron that occupies the entire horizontal space of its parent.</p> --}}
      {!!Form::open(['action'=>'PatientController@getPatient','method' => 'GET','class'=>'form-login'])!!}
      <div class="form-group">
          {{Form::text('id','',['placeholder' => 'Patient ID', 'name'=>'patient_id', 'class' =>'form-control form-control-lg', 'autofocus'])}}
          {{Form::hidden('text','age',['name'=>'hiddenf'])}}
      </div>
    
      {{Form::submit('submit',['class' => 'btn btn-primary btn-lg'])}}
      {!! Form::close() !!}
 </div>
</body>
@endsection