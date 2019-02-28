<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Designation;
use App\Department;
use App\Role;
use App\TravelMember;
use App\TravelType;
use App\UserMembers;
use App\Locations;
use App\Airlines;
use App\TravelRequest;
use App\RequestApprovals;
use App\ProjectApprovalUser;
use Illuminate\Support\Facades\Hash;
use Datatables;
use Auth;
use DB;
use URL;
use Mail;

class TravelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['passport_not_found']=0;
        if(User::where('id',Auth::user()->id)->where('passport_no',null)->exists()){
            $data['passport_not_found']=1;
        }
        $data['members']=UserMembers::where('is_deleted', 0)->where('user_id', Auth::user()->id)->get();
        $data['travel_type']=TravelType::where('is_active', 0)->get();
        $data['locations']=Locations::where('is_deleted', 0)->get();
        $data['airlines']=Airlines::where('is_deleted', 0)->get();
        $data['user_members']=UserMembers::with('Relation')->where('is_deleted', 0)->get();

        return view('customer/travel/create_request', $data);
    }

    public function hr_create_travel()
    {

        $data['customers']=User::where('role_id', 3)->where('is_deleted', 0)->get();
        $data['travel_type']=TravelType::where('is_active', 0)->get();
        $data['locations']=Locations::where('is_deleted', 0)->get();
        $data['airlines']=Airlines::where('is_deleted', 0)->get();


        return view('hr/travel/create_request', $data);
    }

    public function create(Request $request){

        $purpose= $request->input('purpose');
        $trip_from= $request->input('trip_from');
        $trip_to= $request->input('trip_to');
        $travel_type= $request->input('travel_type');
        $start_date= $request->input('start_date');
        $end_date= $request->input('end_date');
        $airline= $request->input('airline');
        $comment= $request->input('comment');
      //  $members= $request->input('selected_members');
        $class=$request->input('class');

        $members = json_decode($request->input('selected_members'));

        $employee_department_id=Auth::user()->department_id;


        $start_date=date('Y-m-d', strtotime($start_date));
        $end_date_email="";
      //  return $end_date;
        if($end_date!=null){
            $end_date=date('Y-m-d', strtotime($end_date));
            $end_date_email=date('d-m-Y', strtotime($end_date));
        }


        $current_month = date('m');
        $current_year = date('y');


        //return $image = $request->file('line_manager');

       /* $this->validate($request, [
            'line_manager' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);*/

        if ($request->hasFile('line_manager')) {
            $image = $request->file('line_manager');
            $name = time().'.'.$image->getClientOriginalExtension();

            $destinationPath = public_path('/line_manager_attachments');
            $image->move($destinationPath, $name);
            //User::where('id', Auth::user()->id)->update(['passport_no'=>$passport_no,'passport_attachment'=>$name]);

        }

        $last_request=TravelRequest::create([
            'purpose'=>$purpose,
            'trip_from_loc'=>$trip_from,
            'trip_to_loc'=>$trip_to,
            'travel_type'=>$travel_type,
            'user'=>Auth::user()->id,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'status'=>1,
            'airline_id'=>$airline,
            'comment'=>$comment,
            'line_manager_attachments'=>$name,
            'class'=>$class
        ]);

        TravelRequest::where('id', $last_request->id)->update(['request_no'=>'TRQ'.$current_month.$current_year.$last_request->id]);
        if($members!=null){
           // print_r($members);
            foreach($members as $key => $data){
                TravelMember::create([
                    'travel_id'=>$last_request->id,
                    'user_member_id'=>$data->id
                ]);
            }
        }


        $all_project_along_user=ProjectApprovalUser::select('user_id')->where('project_id', $employee_department_id)->get();
        $manager=User::select('email')->where('role_id', 2)->whereIn('id', $all_project_along_user)->get();
        $managers_email=[];
        foreach($manager as $key=> $emailData){
            array_push($managers_email, $emailData->email);
        }
        $current_user_data=User::with('designation_name')->where('id', Auth::user()->id)->first();
        $desgination=$current_user_data['designation_name']['name'];



        $current_travel_data=TravelRequest::with('Toloc', 'FromLoc')->where('id', $last_request->id)->first();
        $destination=$current_travel_data['toloc']['name'];
        $source=$current_travel_data['fromLoc']['name'];
        $tr_no=$current_travel_data['request_no'];
        $data=[
            'emp_id'=>Auth::user()->emp_id,
            'request_no'=>$tr_no,
            'emp_name'=>Auth::user()->name,
            'emp_email'=>Auth::user()->email,
            'comment'=>$comment,
            'purpose'=>$purpose,
            'designation'=>$desgination,
            'source'=>$source,
            'destination'=>$destination,
            'class'=>$class,
            'departure_date'=>date("d-m-Y", strtotime($start_date)),
            'return_date'=>$end_date_email,
            'redirection_url'=>URL::to('/single_travel/'.$last_request->id),
            'base_url'=>URL::to('/img/email_images')
        ];
        //$managers_email
        Mail::send(['html'=>'new_request_email'], $data, function($message) use($managers_email) {
            $message->to($managers_email)->subject
            ('Travel Request approval required');
            $message->from('portal.alert@proaxive.my','Proaxive Management');
        });

        $all_req_url=URL::to('customer_travel_list');
        $return_arr=array([
            'status'=>1,
            'status_code'=>200,
            'message'=>"Travel Request submitted successfully"
        ]);

        return $return_arr;

    }

    public function object_to_array($data) {
        return collect($data)->map(function($x) {
            return (array) $x;
        })->toArray();
    }

    public function create_from_hr(Request $request){
        $purpose= $request->input('purpose');
        $trip_from= $request->input('trip_from');
        $trip_to= $request->input('trip_to');
        $travel_type= $request->input('travel_type');
        $start_date= $request->input('start_date');
        $end_date= $request->input('end_date');
        $airline= $request->input('airline');
        $comment= $request->input('comment');
        $customer_id= $request->input('customer_id');
        $class=$request->input('class');
        $members = json_decode($request->input('selected_members'));

        $start_date=date('Y-m-d', strtotime($start_date));
        if($end_date!=null){
            $end_date=date('Y-m-d', strtotime($end_date));
        }

        $current_month = date('m');
        $current_year = date('y');

        if ($request->hasFile('line_manager')) {
            $image = $request->file('line_manager');
            $name = time().'.'.$image->getClientOriginalExtension();

            $destinationPath = public_path('/line_manager_attachments');
            $image->move($destinationPath, $name);
            //User::where('id', Auth::user()->id)->update(['passport_no'=>$passport_no,'passport_attachment'=>$name]);

        }


        $last_request=TravelRequest::create([

            'purpose'=>$purpose,
            'trip_from_loc'=>$trip_from,
            'trip_to_loc'=>$trip_to,
            'travel_type'=>$travel_type,
            'user'=>$customer_id,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'status'=>1,
            'airline_id'=>$airline,
            'line_manager_attachments'=>$name,
            'class'=>$class,
            'comment'=>$comment
        ]);

        TravelRequest::where('id', $last_request->id)->update(['request_no'=>'TRQ'.$current_month.$current_year.$last_request->id]);
        if($members!=null){
            foreach($members as $key => $data){
                TravelMember::create([
                    'travel_id'=>$last_request->id,
                    'user_member_id'=>$data->id
                ]);
            }
        }


        $return_arr=array([
            'status'=>1,
            'status_code'=>200,
            'message'=>"Request has been created successfuly"
        ]);

        return $return_arr;
    }


    public function get_list(){
        $hr_login_id=Auth::user()->id;
        $get_hr_projects=ProjectApprovalUser::select('project_id')->where('user_id', $hr_login_id)->get();
        $list= DB::table('travel_request')->join('locations as from_loc', 'from_loc.id', '=', 'travel_request.trip_from_loc')
            ->join('locations as to_loc', 'to_loc.id', '=', 'travel_request.trip_to_loc')
            ->join('travel_type', 'travel_type.id', '=', 'travel_request.travel_type')
            ->join('users', 'users.id', '=', 'travel_request.user')
            ->join('travel_status', 'travel_status.id', '=', 'travel_request.status')
            ->join('airlines', 'airlines.id', '=', 'travel_request.airline_id')
            ->select(
                'travel_request.request_no',
                'travel_request.id',
                'travel_request.is_resubmit_entry',
                'travel_request.start_date',
                'travel_request.end_date',
                'travel_request.purpose',
                'from_loc.name as from_loc',
                'from_loc.code as from_code',
                'to_loc.name as to_loc',
                'to_loc.code as to_code',
                'travel_type.name as travel_type',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.email',
                'users.name as full_name',
                'travel_request.created_at as created_date',
                'travel_request.comment',
                'travel_status.name as status',
                'travel_status.id as status_id',
                'airlines.name as airline'
                )
            ->whereIn('users.department_id', $get_hr_projects)->get();
        return Datatables::of($list)->addColumn('status', function($list){
            if($list->status_id==1){
                return "<div style='display: block; text-align:center;'><i class='fa fa-clock-o' title='Pending'></i></div>";
            }
            else if($list->status_id==2){
                return "<div style='display: block; text-align:center;'><i class='fa fa-spinner' title='Processing' style='color: blue;'></i></div>";
            }
            else if($list->status_id==3){

                return "<div style='display: block; text-align:center;'><i class='fa fa-ban' style='color:red;' title='Rejected'></i></div>";
            }
            else if($list->status_id==4){
                return "<div style='display: block; text-align:center;'><i class='fa fa-check-circle' style='color:green;' title='Approved'></i></div>";
            }
        })->addColumn('created_date', function($list){
            return date("d-M-Y", strtotime($list->created_date));
        })->addColumn('request_no', function($list){
            if($list->is_resubmit_entry==1){
                return $list->request_no.' '."<i class='fa fa-history' style='color:green;' title='ReSubmitted'></i>";
            }
            else{
                return $list->request_no;
            }

        })->addColumn('trip', function($list){
            return '<span title="'.$list->from_loc.'" style="background:gray; color:#fff; padding:2px;">'.$list->from_code.'</span>-<span title="'.$list->to_loc.'"  style="background:gray; color:#fff; padding:2px;">'.$list->to_code.'</span>';
        })->addColumn('action', function ($list) {
            return '<a href="'.url('/single_travel/'.$list->id.'').'" style="color:#fff;"><button title="View Details" style="text-align:center;"><i class="icon ion-document-text"></i></button></a>';
        })
            ->editColumn('id', 'ID: {{$id}}')
            ->removeColumn('password')
            ->make(true);
    }

    public function single_view($id){
        $data['single']=TravelRequest::with('FromLoc','Toloc','Members.UserMember.Relation','User','User.department','User.designation_name','Traveltype','Status','Airline', 'Approvals.User', 'Approvals.User.designation_name')->where('id', $id)->first()->toArray();

        //return $data;

       // return $data;
        return View('hr.travel.single', $data);
    }

    public function get_list_view(){
        return View('hr.travel.travel_list');
    }

    public function get_list_view_customer(){
        return View('customer.travel.travel_list');
    }

    public function hr_approval(Request $request){
        $status=$request->input('status');
        $comment=$request->input('comment');
        $id= $request->input('request_id');

        $message_text="";

        $request=TravelRequest::select('request_no','user')->where('id', $id)->first();

        $user_id= $request['user'];
        $request_no= $request['request_no'];
        $user=User::select('name','email','emp_id')->where('id', $user_id)->first();
        $user_email=$user['email'];
        $user_name=$user['name'];
        $user_emp_id=$user['emp_id'];

        $data['emp_name']=$user_name;
        $data['emp_email']=$user_email;
        $data['emp_id']=$user_emp_id;

        $approval_status_text="";


        if($status==1){
            RequestApprovals::create([
                'request_id'=>$id,
                'user_id'=>Auth::user()->id,
                'status'=>4,
                'comment'=>$comment
            ]);
            TravelRequest::where('id', $id)->update(['status'=>2]);
            $approval_status_text="Approved";

            $message_text="Your Travel Request has been Approved";

        }
        else{
            RequestApprovals::create([
                'request_id'=>$id,
                'user_id'=>Auth::user()->id,
                'status'=>3,
                'comment'=>$comment
            ]);
            $approval_status_text="Rejected";
            TravelRequest::where('id', $id)->update(['status'=>3]);
            $message_text="Your Travel Request has been Rejected";
        }

        $data['approval']=RequestApprovals::with('user', 'travel_request')->where('request_id', $id)->get();
        $data['travel_request']=TravelRequest::with('user')->where('id', $id)->get();
        $data['info'] = ['message_text'=>$message_text,'request_no'=>$request_no];
        $data['redirection_url']=URL::to('/customer_travel_details/'.$id);
        $data['approval_text']=$approval_status_text;


        //return $data;

        Mail::send(['html'=>'approval_email'], $data, function($message) use($user_email, $user_name, $request_no, $approval_status_text) {
            $message->to($user_email, $user_name)->subject
            ('Travel Request '.$request_no.' '.$approval_status_text.' '.'by HR');
            $message->from('portal.alert@proaxive.my','Proaxive Management');
        });

        $data['redirection_url']=URL::to('/single_proceed_travel/'.$id);
        $md=User::select('email')->where('role_id', 1)->get();
        $md_secretary_emails=[];
        foreach($md as $key=> $emailData){
            array_push($md_secretary_emails, $emailData->email);
        }
        Mail::send(['html'=>'approval_email'], $data, function($message) use($md_secretary_emails, $user_email, $user_name, $request_no, $approval_status_text) {
            $message->to($md_secretary_emails)->subject
            ('Travel Request '.$request_no.' '.$approval_status_text.' '.'by HR');
            $message->from('portal.alert@proaxive.my','Proaxive Management');
        });

        return redirect()->back()->with('message',$message_text);
    }

    public function get_list_by_user(){
        $list= DB::table('travel_request')->join('locations as from_loc', 'from_loc.id', '=', 'travel_request.trip_from_loc')
            ->join('locations as to_loc', 'to_loc.id', '=', 'travel_request.trip_to_loc')
            ->join('travel_type', 'travel_type.id', '=', 'travel_request.travel_type')
            ->join('users', 'users.id', '=', 'travel_request.user')
            ->join('travel_status', 'travel_status.id', '=', 'travel_request.status')
            ->join('airlines', 'airlines.id', '=', 'travel_request.airline_id')
            ->select(
                'travel_request.request_no',
                'travel_request.id',
                'travel_request.archive',
                'travel_request.is_resubmit_entry',
                'travel_request.start_date',
                'travel_request.end_date',
                'travel_request.purpose',
                'from_loc.name as from_loc',
                'from_loc.code as from_code',
                'to_loc.name as to_loc',
                'to_loc.code as to_code',
                'travel_type.name as travel_type',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.email',
                'users.name as full_name',
                'travel_request.created_at as created_date',
                'travel_request.comment',
                'travel_status.name as status',
                'travel_status.id as status_id',
                'airlines.name as airline'
            )->where('user', Auth::user()->id)
            ->get();
        return Datatables::of($list)->addColumn('status', function($list){
            if($list->status_id==1){
                return "<div style='display: block; text-align:center;'><i class='fa fa-clock-o' title='Pending'></i></div>";
            }
            else if($list->status_id==2){
                return "<div style='display: block; text-align:center;'><i class='fa fa-spinner' title='Processing' style='color: blue;'></i></div>";
            }
            else if($list->status_id==3){

                return "<div style='display: block; text-align:center;'><i class='fa fa-ban' style='color:red;' title='Rejected'></i></div>";
            }
            else if($list->status_id==4){
                return "<div style='display: block; text-align:center;'><i class='fa fa-check-circle' style='color:green;' title='Approved'></i></div>";
            }
        })->addColumn('created_date', function($list){
            return date("d-M-Y", strtotime($list->created_date));
        })->addColumn('request_no', function($list){
            if($list->is_resubmit_entry==1){
                return $list->request_no.' '."<i class='fa fa-history' style='color:green;' title='ReSubmitted'></i>";
            }
            else{
                return $list->request_no;
            }

        })->addColumn('trip', function($list){
            return '<span title="'.$list->from_loc.'" style="background:gray; color:#fff; padding:2px;">'.$list->from_code.'</span>-<span title="'.$list->to_loc.'"  style="background:gray; color:#fff; padding:2px;">'.$list->to_code.'</span>';
        })->addColumn('action', function ($list) {
            if($list->status_id==3 && $list->archive==0){
                return '<a href="'.url('/customer_travel_details/'.$list->id.'').'" style="color:#fff;"><button title="View Details" style="text-align:center;"><i class="icon ion-document-text"></i></button></a> <a href="'.url('/resubmit_travel/'.$list->id.'').'" style="color:#fff;"><button title="Resubmit Request" style="text-align:center;"><i class="fa fa-history"></i></button></a>';
            }
            else{
                return '<a href="'.url('/customer_travel_details/'.$list->id.'').'" style="color:#fff;"><button title="View Details" style="text-align:center;"><i class="icon ion-document-text"></i></button></a>';
            }

        })
            ->editColumn('id', 'ID: {{$id}}')
            ->removeColumn('password')
            ->make(true);
    }

    public function single_view_customer($id){
         $data['single']=TravelRequest::with('FromLoc','Toloc','Members.UserMember.Relation','User','User.department','User.designation_name','Traveltype','Status','Airline', 'Approvals.User', 'Approvals.User.designation_name')->where('id', $id)->first()->toArray();

        //return $data;

        // return $data;
        return View('customer.travel.single', $data);
    }


    public function get_list_view_md(){
        return View('md.travel.travel_list');
    }

    public function get_processed_list(){
        $md_login_id=Auth::user()->id;
        $get_md_projects=ProjectApprovalUser::select('project_id')->where('user_id', $md_login_id)->get();
        $list= DB::table('travel_request')->join('locations as from_loc', 'from_loc.id', '=', 'travel_request.trip_from_loc')
            ->join('locations as to_loc', 'to_loc.id', '=', 'travel_request.trip_to_loc')
            ->join('travel_type', 'travel_type.id', '=', 'travel_request.travel_type')
            ->join('users', 'users.id', '=', 'travel_request.user')
            ->join('travel_status', 'travel_status.id', '=', 'travel_request.status')
            ->join('airlines', 'airlines.id', '=', 'travel_request.airline_id')
            ->select(
                'travel_request.request_no',
                'travel_request.id',
                'travel_request.is_resubmit_entry',
                'travel_request.start_date',
                'travel_request.end_date',
                'travel_request.purpose',
                'from_loc.name as from_loc',
                'from_loc.code as from_code',
                'to_loc.name as to_loc',
                'to_loc.code as to_code',
                'travel_type.name as travel_type',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.email',
                'users.name as full_name',
                'travel_request.created_at as created_date',
                'travel_request.comment',
                'travel_status.name as status',
                'travel_status.id as status_id',
                'airlines.name as airline'
            )->where('status', '!=',1)->whereIn('users.department_id', $get_md_projects)->get();
        return Datatables::of($list)->addColumn('status', function($list){
            if($list->status_id==1){
                return "<div style='display: block; text-align:center;'><i class='fa fa-clock-o' title='Pending'></i></div>";
            }
            else if($list->status_id==2){
                return "<div style='display: block; text-align:center;'><i class='fa fa-spinner' title='Processing' style='color: blue;'></i></div>";
            }
            else if($list->status_id==3){

                return "<div style='display: block; text-align:center;'><i class='fa fa-ban' style='color:red;' title='Rejected'></i></div>";
            }
            else if($list->status_id==4){
                return "<div style='display: block; text-align:center;'><i class='fa fa-check-circle' style='color:green;' title='Approved'></i></div>";
            }
        })->addColumn('created_date', function($list){
            return date("d-M-Y", strtotime($list->created_date));
        })->addColumn('request_no', function($list){
            if($list->is_resubmit_entry==1){
                return $list->request_no.' '."<i class='fa fa-history' style='color:green;' title='ReSubmitted'></i>";
            }
            else{
                return $list->request_no;
            }

        })->addColumn('trip', function($list){
            return '<span title="'.$list->from_loc.'" style="background:gray; color:#fff; padding:2px;">'.$list->from_code.'</span>-<span title="'.$list->to_loc.'"  style="background:gray; color:#fff; padding:2px;">'.$list->to_code.'</span>';
        })->addColumn('action', function ($list) {
            return '<a href="'.url('/single_proceed_travel/'.$list->id.'').'" style="color:#fff;"><button title="View Details"><i class="icon ion-document-text"></i></button></a>';
        })
            ->editColumn('id', 'ID: {{$id}}')
            ->removeColumn('password')
            ->make(true);
    }

    public function single_view_md($id){
        $data['single']=TravelRequest::with('FromLoc','Toloc','Members.UserMember.Relation','User','User.department','User.designation_name','Traveltype','Status','Airline', 'Approvals.User', 'Approvals.User.designation_name')->where('id', $id)->first()->toArray();

        //return $data;

        // return $data;
        return View('md.travel.single', $data);
    }

    public function md_approval(Request $request){
        $status=$request->input('status');
        $comment=$request->input('comment');
        $id= $request->input('request_id');

        $message_text="";

        $request=TravelRequest::select('request_no','user')->where('id', $id)->first();

        $user_id= $request['user'];
        $request_no= $request['request_no'];
        $user=User::select('name','email','emp_id')->where('id', $user_id)->first();
        $hr_secretary=User::select('email')->where('role_id', 4)->get();
        $hr_secretary_emails=[];
        $user_email=$user['email'];
        $user_name=$user['name'];
        $user_emp_id=$user['emp_id'];
        $approval_status_text="";

        $data['emp_name']=$user_name;
        $data['emp_email']=$user_email;
        $data['emp_id']=$user_emp_id;

        if($status==1){
            RequestApprovals::create([
                'request_id'=>$id,
                'user_id'=>Auth::user()->id,
                'status'=>4,
                'comment'=>$comment
            ]);

            $approval_status_text="Approved";

            $data['approval']=RequestApprovals::with('user', 'travel_request')->where('request_id', $id)->get();
            $data['travel_request']=TravelRequest::with('user')->where('id', $id)->get();
            $data['info'] = ['message_text'=>$message_text,'request_no'=>$request_no];
            $data['redirection_url']=URL::to('/customer_travel_details/'.$id);
            $data['approval_text']=$approval_status_text;

            TravelRequest::where('id', $id)->update(['status'=>4]);

            $message_text="Your Travel Request has been Approved";


            foreach($hr_secretary as $key=> $emailData){
                array_push($hr_secretary_emails, $emailData->email);
            }
            //return $hr_secretary_emails;
            Mail::send(['html'=>'approval_email'], $data, function($message) use($hr_secretary_emails, $request_no, $approval_status_text) {
                $message->to($hr_secretary_emails)->subject('Travel Request '.$request_no.' '.$approval_status_text.' '.'by MD');
                $message->from('portal.alert@proaxive.my','Proaxive Management');
            });
        }
        else{

            $data['approval']=RequestApprovals::with('user', 'travel_request')->where('request_id', $id)->get();
            $data['travel_request']=TravelRequest::with('user')->where('id', $id)->get();
            $data['info'] = ['message_text'=>$message_text,'request_no'=>$request_no];
            $data['redirection_url']=URL::to('/customer_travel_details/'.$id);
            RequestApprovals::create([
                'request_id'=>$id,
                'user_id'=>Auth::user()->id,
                'status'=>3,
                'comment'=>$comment
            ]);
            TravelRequest::where('id', $id)->update(['status'=>3]);
            $approval_status_text="Rejected";
            $message_text="Your Travel Request has been Rejected";
        }

        $data['approval_text']=$approval_status_text;

        //return $data;
        Mail::send(['html'=>'approval_email'], $data, function($message) use($user_email, $user_name, $request_no, $approval_status_text) {
            $message->to($user_email, $user_name)->subject
            ('Travel Request '.$request_no.' '.$approval_status_text.' '.'by MD');
            $message->from('portal.alert@proaxive.my','Proaxive Management');
        });






        return redirect()->back()->with('message',$message_text);
    }

    public function resubmit_travel(Request $request){
        $purpose= $request->input('purpose');
        $trip_from= $request->input('trip_from');
        $trip_to= $request->input('trip_to');
        $travel_type= $request->input('travel_type');
        $start_date= $request->input('start_date');
        $end_date= $request->input('end_date');
        $airline= $request->input('airline');
        $comment= $request->input('comment');
        //  $members= $request->input('selected_members');
        $class=$request->input('class');

        $previous_travel_id=$request->input("previous_travel_id");

        $members = json_decode($request->input('selected_members'));


        $start_date=date('Y-m-d', strtotime($start_date));
        $end_date_email="";
        //  return $end_date;
        if($travel_type==2){
            $end_date=date('Y-m-d', strtotime($end_date));
            $end_date_email=date('d-m-Y', strtotime($end_date));
        }
        else{
            $end_date=null;
        }


        $current_month = date('m');
        $current_year = date('y');


        //return $image = $request->file('line_manager');

        /* $this->validate($request, [
             'line_manager' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         ]);*/
        $previous_travel_details=TravelRequest::where('id', $previous_travel_id)->first();
        $name=$previous_travel_details['line_manager_attachments'];

        if ($request->hasFile('line_manager')) {
            $image = $request->file('line_manager');
            $name = time().'.'.$image->getClientOriginalExtension();

            $destinationPath = public_path('/line_manager_attachments');
            $image->move($destinationPath, $name);
            //User::where('id', Auth::user()->id)->update(['passport_no'=>$passport_no,'passport_attachment'=>$name]);

        }

        $last_request=TravelRequest::create([
            'purpose'=>$purpose,
            'trip_from_loc'=>$trip_from,
            'trip_to_loc'=>$trip_to,
            'travel_type'=>$travel_type,
            'user'=>Auth::user()->id,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'status'=>1,
            'airline_id'=>$airline,
            'comment'=>$comment,
            'line_manager_attachments'=>$name,
            'class'=>$class,
            'is_resubmit_entry'=>1
        ]);

        TravelRequest::where('id', $last_request->id)->update(['request_no'=>'TRQ'.$current_month.$current_year.$last_request->id]);
        if($members!=null){
            // print_r($members);
            foreach($members as $key => $data){
                TravelMember::create([
                    'travel_id'=>$last_request->id,
                    'user_member_id'=>$data->id
                ]);
            }
        }

        TravelRequest::where('id', $previous_travel_id)->update(['archive'=>1]);

        $manager=User::select('email')->where('role_id', 2)->get();
        $managers_email=[];
        foreach($manager as $key=> $emailData){
            array_push($managers_email, $emailData->email);
        }
        $current_user_data=User::with('designation_name')->where('id', Auth::user()->id)->first();
        $desgination=$current_user_data['designation_name']['name'];



        $current_travel_data=TravelRequest::with('Toloc', 'FromLoc')->where('id', $last_request->id)->first();
        $destination=$current_travel_data['toloc']['name'];
        $source=$current_travel_data['fromLoc']['name'];
        $tr_no=$current_travel_data['request_no'];
        $data=[
            'emp_id'=>Auth::user()->emp_id,
            'request_no'=>$tr_no,
            'emp_name'=>Auth::user()->name,
            'emp_email'=>Auth::user()->email,
            'comment'=>$comment,
            'purpose'=>$purpose,
            'designation'=>$desgination,
            'source'=>$source,
            'destination'=>$destination,
            'class'=>$class,
            'departure_date'=>date("d-m-Y", strtotime($start_date)),
            'return_date'=>$end_date_email,
            'redirection_url'=>URL::to('/single_travel/'.$last_request->id),
            'base_url'=>URL::to('/img/email_images')
        ];
        //$managers_email
        Mail::send(['html'=>'new_request_email'], $data, function($message) use($managers_email) {
            $message->to($managers_email)->subject
            ('Travel Request approval required (Resubmitted)');
            $message->from('portal.alert@proaxive.my','Proaxive Management');
        });

        $all_req_url=URL::to('customer_travel_list');
        $return_arr=array([
            'status'=>1,
            'status_code'=>200,
            'message'=>"Travel Request submitted successfully"
        ]);

        return $return_arr;

    }

    public function resubmit_travel_view($travel_id){
        $data['passport_not_found']=0;
        if(User::where('id',Auth::user()->id)->where('passport_no',null)->exists()){
            $data['passport_not_found']=1;
        }
        $data['members']=UserMembers::where('is_deleted', 0)->where('user_id', Auth::user()->id)->get();
        $data['travel_type']=TravelType::where('is_active', 0)->get();
        $data['locations']=Locations::where('is_deleted', 0)->get();
        $data['airlines']=Airlines::where('is_deleted', 0)->get();
        $data['user_members']=UserMembers::with('Relation')->where('is_deleted', 0)->get();

        $data['travel_details']=TravelRequest::with('members')->where('id', $travel_id)->first();

        $data['previous_travel_id']=$travel_id;


        return View('customer/travel/resubmit_request', $data);
    }

    public function get_travel_members($travel_id){

        $members_details=TravelMember::with('UserMember.Relation')->where('travel_id', $travel_id)->get();
        return $members_details;
    }

    public function approval_view_hr(){
        return View("hr/travel/approvals");
    }

    public function hr_approvals_list(){
        $hr_login_id=Auth::user()->id;
        $get_hr_projects=ProjectApprovalUser::select('project_id')->where('user_id', $hr_login_id)->get();
        $list= DB::table('travel_request')->join('locations as from_loc', 'from_loc.id', '=', 'travel_request.trip_from_loc')
            ->join('locations as to_loc', 'to_loc.id', '=', 'travel_request.trip_to_loc')
            ->join('travel_type', 'travel_type.id', '=', 'travel_request.travel_type')
            ->join('users', 'users.id', '=', 'travel_request.user')
            ->join('travel_status', 'travel_status.id', '=', 'travel_request.status')
            ->join('airlines', 'airlines.id', '=', 'travel_request.airline_id')
            ->select(
                'travel_request.request_no',
                'travel_request.id',
                'travel_request.is_resubmit_entry',
                'travel_request.start_date',
                'travel_request.end_date',
                'travel_request.purpose',
                'from_loc.name as from_loc',
                'from_loc.code as from_code',
                'to_loc.name as to_loc',
                'to_loc.code as to_code',
                'travel_type.name as travel_type',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.email',
                'users.name as full_name',
                'travel_request.created_at as created_date',
                'travel_request.comment',
                'travel_status.name as status',
                'travel_status.id as status_id',
                'airlines.name as airline'
            )->where('travel_status.id', 1)->whereIn('users.department_id', $get_hr_projects)->get();

        return Datatables::of($list)->addColumn('status', function($list){
            if($list->status_id==1){
                return "<div style='display: block; text-align:center;'><i class='fa fa-clock-o' title='Pending'></i></div>";
            }
            else if($list->status_id==2){
                return "<div style='display: block; text-align:center;'><i class='fa fa-spinner' title='Processing' style='color: blue;'></i></div>";
            }
            else if($list->status_id==3){

                return "<div style='display: block; text-align:center;'><i class='fa fa-ban' style='color:red;' title='Rejected'></i></div>";
            }
            else if($list->status_id==4){
                return "<div style='display: block; text-align:center;'><i class='fa fa-check-circle' style='color:green;' title='Approved'></i></div>";
            }
        })->addColumn('created_date', function($list){
            return date("d-M-Y", strtotime($list->created_date));
        })->addColumn('request_no', function($list){
            if($list->is_resubmit_entry==1){
                return $list->request_no.' '."<i class='fa fa-history' style='color:green;' title='ReSubmitted'></i>";
            }
            else{
                return $list->request_no;
            }

        })->addColumn('trip', function($list){
            return '<span title="'.$list->from_loc.'" style="background:gray; color:#fff; padding:2px;">'.$list->from_code.'</span>-<span title="'.$list->to_loc.'"  style="background:gray; color:#fff; padding:2px;">'.$list->to_code.'</span>';
        })->addColumn('action', function ($list) {
            return '<button class=" btn-success " title="Approve" data-toggle="modal" data-target="#modaldemo4" onclick="approve('.$list->id.','."'$list->request_no'".')"><i class="icon ion-checkmark small"  ></i></button> 
            <a href="javascript:;" style="color:#fff;"><button title="Disapprove"  class=" btn-danger " data-toggle="modal" data-target="#modaldemo3" onclick="disapprove('.$list->id.','."'$list->request_no'".')"><i class="icon ion-close  small"  ></i></button></a>  <a href="'.url('/single_travel/'.$list->id.'').'" style="color:#fff;"><button title="View Details" style="text-align:center;"><i class="icon ion-document-text"></i></button></a>';
        })
            ->editColumn('id', 'ID: {{$id}}')
            ->removeColumn('password')
            ->make(true);
    }


    public function approval_view_md(){
        return View("md/travel/approvals");
    }


    public function md_approvals_list(){
        $md_login_id=Auth::user()->id;
        $get_md_projects=ProjectApprovalUser::select('project_id')->where('user_id', $md_login_id)->get();
        $list= DB::table('travel_request')->join('locations as from_loc', 'from_loc.id', '=', 'travel_request.trip_from_loc')
            ->join('locations as to_loc', 'to_loc.id', '=', 'travel_request.trip_to_loc')
            ->join('travel_type', 'travel_type.id', '=', 'travel_request.travel_type')
            ->join('users', 'users.id', '=', 'travel_request.user')
            ->join('travel_status', 'travel_status.id', '=', 'travel_request.status')
            ->join('airlines', 'airlines.id', '=', 'travel_request.airline_id')
            ->select(
                'travel_request.request_no',
                'travel_request.id',
                'travel_request.is_resubmit_entry',
                'travel_request.start_date',
                'travel_request.end_date',
                'travel_request.purpose',
                'from_loc.name as from_loc',
                'from_loc.code as from_code',
                'to_loc.name as to_loc',
                'to_loc.code as to_code',
                'travel_type.name as travel_type',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.email',
                'users.name as full_name',
                'travel_request.created_at as created_date',
                'travel_request.comment',
                'travel_status.name as status',
                'travel_status.id as status_id',
                'airlines.name as airline'
            )->where('travel_status.id', 2)->whereIn('users.department_id', $get_md_projects)->get();

        return Datatables::of($list)->addColumn('status', function($list){
            if($list->status_id==1){
                return "<div style='display: block; text-align:center;'><i class='fa fa-clock-o' title='Pending'></i></div>";
            }
            else if($list->status_id==2){
                return "<div style='display: block; text-align:center;'><i class='fa fa-spinner' title='Processing' style='color: blue;'></i></div>";
            }
            else if($list->status_id==3){

                return "<div style='display: block; text-align:center;'><i class='fa fa-ban' style='color:red;' title='Rejected'></i></div>";
            }
            else if($list->status_id==4){
                return "<div style='display: block; text-align:center;'><i class='fa fa-check-circle' style='color:green;' title='Approved'></i></div>";
            }
        })->addColumn('created_date', function($list){
            return date("d-M-Y", strtotime($list->created_date));
        })->addColumn('request_no', function($list){
            if($list->is_resubmit_entry==1){
                return $list->request_no.' '."<i class='fa fa-history' style='color:green;' title='ReSubmitted'></i>";
            }
            else{
                return $list->request_no;
            }

        })->addColumn('trip', function($list){
            return '<span title="'.$list->from_loc.'" style="background:gray; color:#fff; padding:2px;">'.$list->from_code.'</span>-<span title="'.$list->to_loc.'"  style="background:gray; color:#fff; padding:2px;">'.$list->to_code.'</span>';
        })->addColumn('action', function ($list) {
            return '<button class=" btn-success " title="Approve" data-toggle="modal" data-target="#modaldemo4" onclick="approve('.$list->id.','."'$list->request_no'".')"><i class="icon ion-checkmark small"  ></i></button> 
            <a href="javascript:;" style="color:#fff;"><button title="Disapprove"  class=" btn-danger " data-toggle="modal" data-target="#modaldemo3" onclick="disapprove('.$list->id.','."'$list->request_no'".')"><i class="icon ion-close  small"  ></i></button></a>  <a href="'.url('/single_proceed_travel/'.$list->id.'').'" style="color:#fff;"><button title="View Details" style="text-align:center;"><i class="icon ion-document-text"></i></button></a>';
        })
            ->editColumn('id', 'ID: {{$id}}')
            ->removeColumn('password')
            ->make(true);
    }

}
