<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function index(){
        return view('welcome');
    }
    public function show(Request $request){

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords =DB::table('withdraw_request')
            ->where('merchant_id', '=',24)->where('store_id', '=',25)->count();
        $totalRecordswithFilter = DB::table('withdraw_request')->select('count(*) as allcount')
            ->where('merchant_id', '=',24)->where('store_id', '=',25)
            ->count();

        // Get records, also we have included search filter as well
        $records = DB::table('withdraw_request')
            ->where('merchant_id', '=',24)
            ->where('store_id', '=',25)
            ->select('sp_invoice_no',
                'invoice_id',
                'recived_amount',
                'comission_amount',
                'request_date',
                'merchant_payable_amount')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        $i = 0;
        foreach ($records as $record) {

            $data_arr[] = array(
                "sp_invoice_no" => $record->sp_invoice_no,
                "invoice_id" => $record->invoice_id,
                "recived_amount" => $record->recived_amount,
                "comission_amount" => $record->comission_amount,
                "merchant_payable_amount" => $record->merchant_payable_amount,
                "request_date" => $record->request_date,
                "hold"=>"<input type='checkbox' value='$record->sp_invoice_no' class='btn btn-primary'>",

            );
        }


        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
        );

        return response()->json($response);
//        echo json_encode($response);
//        $comments = DB::table('withdraw_request')
//            ->where('merchant_id', '=',24)
//            ->where('store_id', '=',25)
//            ->paginate(10);
//        if($request->ajax()){
//            return view('welcome',compact('comments'))->render();
//        }
//        return view('welcome',compact('comments'));
    }
}
