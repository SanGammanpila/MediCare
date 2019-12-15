<?php

namespace App\Http\Controllers;

use App\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return 'patient index';
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return 123;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return 'patient show';
        //
    }

    //Loads the relavent page as per user

    function homePharmacist($patient_data,$pid)
    {
        $receipt = DB::table('receipts')
                        ->where('patient_id','=',$pid)
                        ->whereDate('date','=',Carbon::today()->toDateString())
                        ->orderBy('date','desc')
                        ->get()
                        ->first();
                        // ->latest('date')
        if (empty($receipt)) return 'No Medi Today';

        $id = $receipt->id;
        $medicines = DB::table('receipt_medicines')->get()->where('receipt_id','=',$id);
        $checkups = DB::table('receipt_checkups')
        ->leftJoin('medical_checkups','receipt_checkups.checkup_id','=','medical_checkups.id')
        ->get()
        ->where('receipt_id','=',$id);
        $notes = DB::table('receipt_notes')->get()->where('receipt_id','=',$id);    
        return view('receipt.opd')->with(['receipt'=>$receipt,'meds'=>$medicines,'date'=>Carbon::today()->toDateString() ,'patient_data'=>$patient_data,'checkups'=>$checkups,'notes'=>$notes]);
    }

    function homeDoctor($patient_data)
    {
        $checkups = DB::table('medical_checkups')->get();
        return view('receipt.index')->with(['patient_data'=>$patient_data, 'checkups'=>$checkups]);
    }

    public function getPatient(Request $request)
    {
        $id = $request->patient_id;
        $patient_data = Patient::find($id);
        
        switch (auth()->user()->role) {
            case 'doctor':
                return $this->homeDoctor($patient_data);
                break;
            case 'pharmacist':
                return $this->homePharmacist($patient_data,$id);
                break;
            
            default:
                return 'no matching position';
                break;
        }
    }

    public function loadHistory(Request $request)
    {
        $receipts = DB::select('SELECT id,date FROM receipts WHERE patient_id = :pid ORDER BY date DESC;',[':pid'=>$request->input("patient_id")]);
        $history = array();
        foreach ($receipts as $receipt) {
            $id = $receipt->id;
            // $medicines = DB::table('receipt_medicines')->get()->where('receipt_id','=',$id->id)->toArray();
            // $checkups = DB::table('receipt_checkups')
            // ->leftJoin('medical_checkups','receipt_checkups.checkup_id','=','medical_checkups.id')
            // ->get()
            // ->where('receipt_id','=',$id->id)->toArray()
            // ;
            // $notes = DB::table('receipt_notes')->get()->where('receipt_id','=',$id->id)->toArray();
            
            $medicines = DB::select('SELECT * FROM receipt_medicines WHERE receipt_id = :id',['id'=>$id]);
            $checkups = DB:: select('SELECT * FROM receipt_checkups LEFT JOIN medical_checkups ON receipt_checkups.checkup_id = medical_checkups.id WHERE receipt_id = :id',['id'=>$id]);
            $notes = DB::select('SELECT * FROM receipt_notes WHERE receipt_id = :id',['id'=>$id]);

            $receipt = [["medicines" => $medicines],
                        ["checkups" => $checkups],
                        ["notes" => $notes]
                        ,
                        ["date" => $receipt->date]

                     ];

            // $history['receipt'+$i] = $receipt;
            // $i += 1;
            // array_merge($medicines,$checkups,$notes);
            array_push($history,$receipt);
            // array_push($history,$receipt);
            // $medicines =array();
            // $checkups =array();
            // $notes =array();
            // $receipt =array();

        }

        //var_dump(json_encode($history));

        // return json_encode($history, JSON_UNESCAPED_SLASHES);
        // return stripslashes(json_encode($history));
        return json_encode($history);
        // return response()->json($history);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
