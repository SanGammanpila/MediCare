<?php

namespace App\Http\Controllers;

use Dotenv\Regex\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function checkupsDone(Request $request)
    {
            
        $files = $request->file('checkup_files');

        if ($request->hasFile('checkup_files')) {
            $i = 0;
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = $request->patient_id."_".$request->receipt_id.$request->checkup_type[$i].'_'.date("dmy").'.'.$extension;
                $file_path = $request->patient_id.'/'.$fileName ;
                
                //Update Database
                $aff = DB::update('UPDATE receipt_checkups SET file_path =:path WHERE receipt_id = :rid AND checkup_id = :cid', ['path'=>$file_path,'rid' =>$request->receipt_id,'cid' =>$request->checkup_id[$i]]);
                DB::update('UPDATE receipt_checkups SET status = "done" WHERE receipt_id = :rid AND checkup_id = :cid', ['rid' =>$request->receipt_id,'cid' =>$request->checkup_id[$i]]);
                //Store the Files
                $file->storeAs('checkups/'.$request->patient_id.'/',$fileName);
                $i += 1;
            }

            // ! Redirect
            return 'Files stored '.$aff;
        }
        return 'No files';
    }

    public function medIssued(Request $request)
    {
        DB::table('receipts')
            ->where('id', $request->receipt_id)
            ->update(['checkMedicines' => 1]);   
        return 'done';
    }

    public function index($patient_data)
    {

        if(auth()->user()->user_type = "doctor")
        {
            return $patient_data;
            return view('Receipt.index')->with('patient_data',$patient_data);
        }
        else
        {
            return 'no position';
        }
    }

    public function med_suggest(Request $request)
    {
        $str = $request->input('query');
        $query = "SELECT name,id FROM medicines WHERE name LIKE '%".$str."%' LIMIT 5";
        $suggestions = DB::select($query);
        
        return $suggestions;
    }

    public function get_med_id(Request $request)
    {
        $query = "SELECT id FROM medicines WHERE name = '".$request->input('name')."'";
        $med_id =  DB::select($query);
        if ($med_id != null) {
            $id = $med_id[0]->id;
            return $id;
        }
        else{
            return "NA";
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        $patient_id = $request->input("patient_id");
        $checkups  = $request->input('checkups');
        $medicines = $request->only(['med','dos']);
        $notes_doc = $request->input('notes_doc');
        $notes_patient = $request->input('notes_patient');

        //Insert a new record to receipts table
        $receipt_id = DB::table('receipts')->insertGetId(
            ['patient_id' => $patient_id]
        );

        if (count($medicines)>0) {
            for ($i=0; $i < count($medicines["med"]); $i++) { 
              //Insert to receipt_medicine table
              DB::table('receipt_medicines')->insert(
                ['receipt_id' => $receipt_id,
                 'medicine_id' =>$medicines["med"][$i],
                 'dose' => $medicines["dos"][$i]] );
            }
        }

        if (count($checkups)>0) {
            for ($i=0; $i < count($checkups); $i++) { 
              //Insert to receipt_checkups table
              DB::table('receipt_checkups')->insert(
                ['receipt_id' => $receipt_id,
                 'checkup_id' =>$checkups[$i]
                ]);
              }
        }

        //Insert Notes for Doctor
        if (count($notes_doc)>0) {
            DB::table('receipt_notes')->insert([
                'receipt_id'=>$receipt_id,
                'note_type' =>'doctor',
                'note' => $notes_doc
            ]);
        }
        
        //Insert Notes for Patient
        if (count($notes_patient)>0) {
            DB::table('receipt_notes')->insert([
                'receipt_id'=>$receipt_id,
                'note_type' =>'patient',
                'note' => $notes_patient
            ]);
        }

        return redirect('/home') -> with('success', 'Receipt submitted');       
        return response()->json(['success' => 'Succesfull']);
    }

    public function viewCheckup(Request $request,$rid,$cid)
    {
        //send the relavent file
        $path = DB::select('SELECT file_path FROM receipt_checkups WHERE receipt_id = :rid AND checkup_id = :cid',['rid'=>$rid,'cid'=>$cid]);
        $path1 = $path[0]->file_path;

        $pathToFile = storage_path()."\app\checkups\\".$path1;
        
        //return $pathToFile;

        return response()->file($pathToFile);
       // return $rid.' '.$cid;   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
