@extends('layouts.app')

@section('content')
<div class="container">
      
      
      {{-- <input type="submit" value="DONE"  id="send_receipt" class="btn btn-success float-right"> --}}

  <div class="row">
      {{-- Left Column --}}

    <div class="col shadow-sm p-3 mb-5 bg-white rounded">
      <table class="table table-sm table-borderless table-striped">
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
              </tbody>
      </table>
      <hr>

      
      <div>
        <input id="new_medicine" onkeyup="suggestionsUpdate()" type="text" placeholder="Medicine" list="medicine_hints">
        <datalist id="medicine_hints">
        </datalist>

        <input id="new_dosage" type="text" placeholder="Dosage">
        <input id="add" type="button" value="Add" onclick="addMedicine()" class="btn btn-primary btn-md float-right" >
      </div>
      
      <hr> 
      {!!Form::open(['action'=>'ReceiptController@store', 'method'=>'POST'])!!}
      <div id="medidata">
        <input value="{{$patient_data->id}}" type="hidden" name="patient_id">
        
      </div>
      
      <button class="btn btn-secondary btn-sm btn-block" type="button" data-toggle="collapse" data-target="#advanced" aria-expanded="false" aria-controls="">More</button>
        <div class="collapse" id="advanced">
          <div class="card card-body">
           
            @foreach ($checkups as $cu)
              <div class="form-group form-check">
                  <input type="checkbox" class="form-check-input" value="{{$cu->id}}" name="checkups[]">
                  <label class="form-check-label">{{$cu->name}}</label>
              </div>
            @endforeach           
            </div>
        </div>
        <hr>
        <button class="btn btn-secondary btn-sm btn-block" type="button" data-toggle="collapse" data-target="#notes" aria-expanded="false" aria-controls="collapseExample">Notes</button>
        <hr>
        <div id="notes">
          <div class="form-group">
              <label>NOTES FOR DOCTOR:</label> <br>
              <textarea name="notes_doc" rows="2" class="form-control"> </textarea>
          </div>
              <label>NOTES FOR PATIENT:</label> <br>
              <textarea name="notes_patient" rows="2" class="form-control"> </textarea>
          <br>
        </div>
        <input type="submit" class="btn btn-success float-right" value="Submit">
        {!!Form::close()!!}
    </div>

      {{-- Right Column --}}
      <div id="receipt" class="col shadow p-3 mb-5 bg-white rounded ">
          <h4 class="text-center">RECEIPT</h4>

          <table class="table table-sm table-borderless">
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
                  </tbody>
          </table>

          <div>
            <table class="table table-striped">
              <thead>
                <tr>
                <th scope="col">#</th>
                    <th scope="col">Medicine</th>
                    <th scope="col">Dosage</th>
                    <th scope="col"></th>
                </tr>
              </thead>
              <tbody id="medicineRow">
              </tbody>
            </table>
          </div>
        </div>
  </div>
<hr>

<p>index.blade.php</p>

<button type="button" onclick="loadHistory()" class="btn btn-dark btn-lg btn-block text-info">Load History</button> 
<div id="history">
  
</div>
<script>

$('document').ready(function(){
  $('#new_medicine').focus();
});

var count =0;
  function addMedicine()
  {
    count++;
    var med=$('#new_medicine').val();
    var dos=$('#new_dosage').val();
    var med_id;
    $.ajax({
        type:'POST',
        url:'/ajax/get_med_id',
        data: {  "_token": "{{ csrf_token() }}",
                "name": med
                },
        success:function(data){
          if (data == "NA") {
            alert("Not Available");            
         }
         else{
           med_id = data;
           if (dos == '') {
              dos = 'NA';
            }

            var ele_med ='<input id="med' +count+ '" value="'+ med_id +'" type="hidden" name="med[]">'
            var ele_dos ='<input id="dos' +count+ '" value="'+ dos +'" type="hidden" name="dos[]">'

            $("#medidata").append(ele_med);
            $("#medidata").append(ele_dos);

            displayInList(med,dos,count);
         }
        }
        });

      $('#new_medicine').val("");  
      $('#new_medicine').focus();
      $('#new_dosage').val("");

  }

  function removeMedicine(key)
  {
    $("#" + "med" + key).remove();
    $("#" + "dos" + key).remove();
    $("#" + "row" + key).remove();
  }

  function displayInList(name,dosage,key)
  {
    var med_row = '<tr id="row'+ key +'">';
          med_row += '<th scope="row">' + count +'</th>';
          med_row += '<td>' + name + '</td>';
          med_row += '<td>' + dosage + '</td>';
          med_row += '<td> <button key = "' +key+ '" type="button" onclick="removeMedicine(this.getAttribute(\'key\'))" class="text-danger btn btn-link">Remove</button> </td>';
          med_row += '</tr>';
       
      $('#medicineRow').append(med_row);
  }
 
  $(document).ready(function () {
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
  });

  function receiptView(date,medicines,checkups,notes)
  {
    var container = '<div class="container shadow-sm p-3 mb-5 bg-white rounded">';
    var date = '<p class=" font-weight-bold text-danger">Date: ' +date+'</p>';
    var medicines_table ='';
    var checkup_table = '';
    var notes_table = '';

    
    if (medicines == undefined || medicines.length == 0)
      medicines_table ='<div class="alert alert-success" role="alert">No Medicines</div>'
    else{
    medicines_table += '<table class="table table-striped table-sm">\
                           <thead class="thead-dark"> <tr><th scope="col">#</th><th scope="col">Medicine</th><th scope="col">Dosage</th><th></th></tr></thead><tbody>';

    medicines.forEach(function(item,index,array){
     var row = '<tr><td>' +index + '</td>'
      row += '<td>' + item['medicine_id'] +'</td>';
      row += '<td>' + item['dose'] +'</td>';
      row += '<td></td></tr>';
      medicines_table += row;
    });
    // medicines_table += '</tbody></table>';
  }

if (checkups == undefined || checkups.length == 0)
  checkup_table = '<div class="alert alert-success" role="alert">No Checkups</div>'; 
else
{
  checkup_table += '<thead class = "thead-dark"> <tr><th scope="col">#</th>\
                           <th scope="col">Checkup</th>\
                           <th scope="col">Status</th>\
                           <th scope="col"></th>\
                           </tr></thead><tbody>';
                            
  checkups.forEach(function(item,index,array){
    var row = '<tr><td>' +index + '</td>'
    row += '<td>' + item['name'] +'</td>';
    row += '<td>' + item['status'] +'</td>';
    // row += '<td>' + item['checkup_id'] +'</td>';
    // row += '<td>' + item['receipt_id'] +'</td>';

    //Checkup id and receipt id send
    row += '<td><a target="_blank" href ="checkups/' +
             item['receipt_id'] + 
             '/' + 
             item['checkup_id'] + 
             '"> view </a></td></tr>';
    checkup_table += row;
  });
  '</tbody></table>';
}

if (notes == undefined || notes.length == 0)
  notes_table = '<div class="alert alert-success" role="alert">No Notes</div>'; 
else{
  var notes_table = '<h5>Notes</h5>';
notes.forEach(function(item,index,array){
   var row = '<h6>' + item['note_type'] +'</h6>';
    row += '<p>' + item['note'] +'</p>';
    notes_table += row;
  });
  // notes += '</tbody></table>';
}
  $('#history').append(container + date + medicines_table + checkup_table + notes_table + '</div>');
  }
 
  function loadHistory()
  {
    $.ajax({
        type:'GET',
        url:'/patient/loadHistory',
        data: {  "_token": "{{ csrf_token() }}",
                "patient_id": {{$patient_data->id}}
                },
        success:function(data){
          var dat = JSON.parse(data);

          // MEDICINES- [receipt_index][0].medicines[medicine_index][attributes - medicine_id/receipt_id/dose] - 
          //  CHECKUPS- [receipt_index][1].checkups[checkup_index][attributes - checkup_id/id/name/receipt_id/status] - 
          //     NOTES- [receipt_index][2].notes[0-doctor OR 1-patient][attributes - id/note/receipt_id/note_type] - 
          dat.forEach(function(item,index,array){
            if (item == undefined || item.length == 0)
              console.log('empty');
            else
              receiptView(dat[index][3]['date'],dat[index][0].medicines,dat[index][1].checkups,dat[index][2].notes);
          });
        }
        });
  }

  //Suggestions
  
  function suggestionsUpdate(){
    var str = $("#new_medicine").val(); 
    if (str.length == 0)
      $("medicine_hints").empty();
    else{
        $.ajax({
            type:'POST',
            url:'/ajax/med_suggest',
            data: {  "_token": "{{ csrf_token() }}",
                    "query": str,
                    },
            success:function(data){
              $("#medicine_hints").empty();
              console.log("hints : "+data);
              for(var k in data){
                $("#medicine_hints").append('<option value="' + data[k]['name'] + '" medicine_id = "'+ data[k]['id'] +'"></option>');
              }
            }
        });
    }
  }

</script>


{{-- 
<script>

var count =0;


function addMedicine(name,dosage)
    {
      var med_row = '<tr>';
          med_row += '<th scope="row">' + count +'</th>';
          med_row += '<td>' + name + '</td>';
          med_row += '<td>' + dosage + '</td>';
          med_row += '</tr>';
          
      $('#medicineRow').append(med_row);

      count++;
        var name = $('#new_medicine').val();
        var dosage = $('#new_dosage').val();
        var med_id;

        //Get the Id of the medicine 
        $.ajax({
        type:'POST',
        url:'/ajax/get_med_id',
        data: {  "_token": "{{ csrf_token() }}",
                "name": name
                },
        success:function(data){
          med_id = data;
        }
        });

        var new_medicine = Array(name,dosage,med_id);
        // Add the new med to an array
        medicine_list.push(new_medicine);

        // Build the json string
        var med_json = '{"med" : "'+ name + '"';
         med_json += ',';
         med_json += '"dos" : "'+ dosage + '"';
         med_json += '}';

        // Create a Form field for the added med
        var med_field = "<input type='hidden' id='${count}' name='med_data[]' value='"+ med_json + "'>";
        $('#medicine_list').append(med_field);

        // Insert the med into list
        medicineReceipt(name,dosage);

        // Clear the input fields and Focus on medicine input
        $('#new_medicine').val(null);
        $('#new_dosage').val(null);


    }


$(document).ready(function () {
      
    var count =0;
    var medicine_list = Array();

// Show suggestions medicines]

// Show suggestions medicines]
$("#new_medicine").keyup(function(e){
  var str = $("#new_medicine").val(); 
  if (str.length == 0) {
    $("#medicine_hints").empty();    
  }
  else
  {
    e.preventDefault();
    $.ajax({
        type:'POST',
        url:'/ajax/med_suggest',
        data: {  "_token": "{{ csrf_token() }}",
                "q": str,
                },
        success:function(data){
          $("#medicine_hints").empty();
          console.log(data);
          for(var k in data){
            $("#medicine_hints").append('<option value="' + data[k]['name'] + '" medicine_id = "'+ data[k]['id'] +'"></option>');
          }

        }
    });
  }
});

//Adds the new med to list on the left
    function medicineReceipt(name,dosage)
    {
      var med_row = '<tr>';
          med_row += '<th scope="row">' + count +'</th>';
          med_row += '<td>' + name + '</td>';
          med_row += '<td>' + dosage + '</td>';
          med_row += '</tr>';
          
      $('#medicineRow').append(med_row);
    }

    $('#add').click(function(){
        count++;
        var name = $('#new_medicine').val();
        var dosage = $('#new_dosage').val();
        var med_id;

        //Get the Id of the medicine 
        $.ajax({
        type:'POST',
        url:'/ajax/get_med_id',
        data: {  "_token": "{{ csrf_token() }}",
                "name": name
                },
        success:function(data){
          med_id = data;
        }
        });

        var new_medicine = Array(name,dosage,med_id);
        // Add the new med to an array
        medicine_list.push(new_medicine);

        // Build the json string
        var med_json = '{"med" : "'+ name + '"';
         med_json += ',';
         med_json += '"dos" : "'+ dosage + '"';
         med_json += '}';

        // Create a Form field for the added med
        var med_field = "<input type='hidden' id='${count}' name='med_data[]' value='"+ med_json + "'>";
        $('#medicine_list').append(med_field);

        // Insert the med into list
        medicineReceipt(name,dosage);

        // Clear the input fields and Focus on medicine input
        $('#new_medicine').val(null);
        $('#new_dosage').val(null);
          // TODO - Set Focus
    });

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });

$("#send_receipt").click(function(e){
    e.preventDefault();
    var medicine_json = JSON.stringify(medicine_list);
    $.ajax({
        type:'POST',
        url:'/receipts/store',
        data: {  "_token": "{{ csrf_token() }}",
                "patient_id": '{{$patient_data->id}}',
                "medicines": medicine_json  
                },
        success:function(data){
          alert(data + ' ajax thingy');
        }
    });
});
});
</script> --}}

@endsection