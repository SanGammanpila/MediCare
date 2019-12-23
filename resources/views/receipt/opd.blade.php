{{-- Patient Controller --}}


@extends('layouts.app')
@section('content')
<div class="container">

    <h4 class="text-center">RECEIPT</h4>
    
<table class="table table-sm table-borderless" >
        <tbody>
            <tr>
            <td>Patient ID</td>
            <td>{{$patient_data->id}}</td>
        </tr>
          <tr>
            <td>Name</td>
            <td>{{$patient_data->name}}</td>
          </tr>
          <tr>
              <td>Age</td>
            <td>{{$patient_data->age}}</td>
        </tr>
          <tr>
            <td>Date</td>
            <td>{{$date}}</td>
        </tr>
        </tbody>
</table>

</div>
@if ($receipt->checkMedicines)
    Medicines All done
@endif
<div>


    <div class="container  shadow p-3 mb-5 bg-white rounded " id="medicines">

      <h2>Medicines</h2>
      <table class="table table-striped">
        <thead>
          <tr>
              <th scope="col">#</th>
              <th scope="col">Medicine</th>
              <th scope="col">Dosage</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($meds as $med)
              <tr>
                  <td>{{($loop->index)+1}}</td>
                  <td>{{$med->medicine_id}}</td>
                  <td>{{$med->dose}}</td>
              </tr>
            @endforeach
        </tbody>
      </table>

      <button id="btnmedIssued" onclick="medIssued()" class="btn btn-danger">Okay</button>
    </div>


    <div class="container shadow p-3 mb-5 bg-white rounded  ">

<h2>Checkups</h2>
{!!Form::open(['action'=>'ReceiptController@checkupsDone', 'method'=>'POST', 'enctype'=>'multipart/form-data'])!!}
<table class="table table-striped">
    <thead>
      <tr>
          <th scope="col">#</th>
          <th scope="col">Checkup</th>
          <th scope="col">Status</th>
          <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($checkups as $cu)
          <tr>
              <td>{{($loop->index)+1}}</td>
              <td>{{$cu->name}}</td>
              <td>{{$cu->status}}</td>
              <td><input type="file" name="checkup_files[]" multiple id=""></td>
              <input type="hidden" value="{{$cu->name}}" name="checkup_type[]">
              <input type="hidden" value="{{$cu->checkup_id}}" name="checkup_id[]">
             
              {{-- <td><button type="button" class="btn btn-link">Upload</button></td> --}}
              <td></td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <input type="hidden" value="{{$patient_data->id}}" name="patient_id">
    <input value="{{$receipt->id}}" type="hidden" name="receipt_id">
    <input type="submit" class="btn btn-success" value="Done">
    {!!Form::close()!!}
  </div> 
<p>op.blade.php</p>
</div>
</div>
</div>
@endsection()


<script>
  function medIssued() {

    $.ajax({
        type: 'POST',
        url: '/receipts/medIssued',
        data: {'_token' :'{{csrf_token()}}',
                'receipt_id':"{{$receipt->id}}"
        },
        success: function(data){
          //TODO Remove Button or Change To 
          //TODO Grey out the area
          $("#medicines").addClass("bg-secondary");
          $("#btnmedIssued").addClass("disabled");
          console.log(data);
        }
    });
    
  }

</script>