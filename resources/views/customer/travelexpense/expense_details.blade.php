@extends('layouts.customer.DashboardLayout')
@section('content')
<div class="am-pagebody">


 <!-- Designer card -->


<div class="card pd-20 pd-sm-40">
                <div class="row ">
                    <div class="col-lg-8">
                        <h6 class="card-body-title">
                            Expense Claim Details</h6>
                        <p class="mg-b-20 mg-sm-b-30">
                             <label class="form-control-label"><b>{{$data['data'][0]->travelexpense_no}}</b></label> | Expense Date: <label class="form-control-label"><b>{{date("d-M-Y", strtotime($data['data'][0]->created_at))}}</b></label>
                        </p>
                    </div>
                    <div class="col-lg-4 text-right">
                        <p class="mg-b-20 mg-sm-b-30 ">
						
						
						
                            Status: 
                           
							
							@if($data['data'][0]->status_id==1)
                                <span class="cursorpointer status_tag" style="background:#ff9600; display: inline-block; padding:5px; color:#fff; border-radius:5px;"><i class="fa fa-refresh" style="color: white;" title="Pending"></i> {{$data['data'][0]->status}}</span>
                                @elseif($data['data'][0]->status_id==2)
                                    <span class="cursorpointer status_tag" style="background:#1894ff; display: inline-block; padding:5px; color:#fff; border-radius:5px;"><i class="fa fa-refresh" style="color: white;" title="Processing"></i> {{$data['data'][0]->status}}</span>
                                @elseif($data['data'][0]->status_id==3)
                                    <span class="cursorpointer status_tag" style="background:red; display: inline-block; padding:5px; color:#fff; border-radius:5px;"><i class="fa fa-ban" style="color: white;" title="Pending"></i> {{$data['data'][0]->status}}</span>
                                @elseif($data['data'][0]->status_id==4)
                                    <span class="cursorpointer status_tag" style="background:green; display: inline-block; padding:5px; color:#fff; border-radius:5px;"><i class="fa fa-check-circle" style="color: white;" title="Approved"></i> {{$data['data'][0]->status}}</span>
                                @endif
                       
							
							
							</p>
							
							
							
							
                        <div class="approval_workflow">
						@if($data['approvals']['status']=="true")
                            <table class="table tabletd_padding_min " width="100%">
                                <thead>
                                    <tr>
                                        <th class="  text-left">
                                            Approval
                                        </th>
                                        <th class="wd-25p text-center">
                                            Status
                                        </th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach($data['approvals']['data'] as $value)
                                <tr>
                                    <td>{{$value->user_name}} ({{$value->user_role}})</td>
                                    <td class="text-center">{{$value->status}}</td>
                                </tr>
                                @endforeach 
                                </tbody>
                            </table>
                            @else
                                <img src="{{asset('img/travel_expense_approval_flow.png')}}" width="100%" height="137px"/>
                         @endif
						</div>
                    </div>
                </div>

                <hr  class="sp_row" />
               
  <h5>
                       Employee Details</h5>

                       <br />
                <div class="form-layout">
                    <div class="row mg-b-25">


                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    
Full Name:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>{{$data['data'][0]->employee_name}}</b>
                                </label>
                            </div>
                        </div>
                        <!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    Expense Type:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>{{$data['data'][0]->expense_type}}</b>
                                </label>
                            </div>
                        </div>
                        <!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    Travel Purpose:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>{{$data['data'][0]->travel_purpose}}</b>
                                </label>
                            </div>
                        </div>
                        <!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    Destination:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>@if($data['data'][0]->location_destination!="")
                                    {{$data['data'][0]->location_destination}}
                                    @else
                                    {{$data['data'][0]->destination}}
                                    @endif  </b>
                                </label>
                            </div>
                        </div>
                        <!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
							 @if($data['data'][0]->request_no!="")
                                <label class="form-control-label">
                                    Travel No:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>{{$data['data'][0]->request_no}}</b>
                                </label>
								   @endif
                            </div>
                        </div>
               
                        
                        <!-- col-4 -->
                
                            
                  
                        <!-- col-4 -->
                    </div>
         
                    <!-- form-layout-footer -->
                </div>
                <!-- form-layout -->

                  <hr  class="sp_row" />
                     <h5>
                       Payment Order</h5>

                       <br />


                <div class="form-layout">
                    <div class="row mg-b-25">


                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    
Payment Order Number:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>{{$data['data'][0]->payment_order_number}}</b>
                                </label>
                            </div>
                        </div>
                        <!-- col-4 -->
                 
						  <!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    By Order of:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>{{$data['data'][0]->by_order_of}}</b>
                                </label>
                            </div>
                        </div>
                        <!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    Payment Currency:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>
{{$data['data'][0]->currency_name}}</b>
                                </label>
                            </div>
                        </div>
                        <!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    On Account of:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>
@if($data['data'][0]->location_account_of_name!="")
                                    {{$data['data'][0]->location_account_of_name}}
                                    @else
                                    {{$data['data'][0]->on_account_of_location}}
                                    @endif  </b>
                                </label>
                            </div>
                        </div>
                        <!-- col-4 -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">
                                    Expense Authorization:
                                </label>
                                <br />
                                <label class="form-control-label">
                                    <b>
<a href="http://172.16.1.250:82/proaxive/public/line_manager_attachments/{{$data['data'][0]->expense_authorization_docs}}" target="_blank"> <i class="fa fa-paperclip"></i> See Attached</a>
</b>
                                </label>
                            </div>
                        </div>
                        <!-- col-4 -->
                      
                        <!-- col-4 -->
                
                            <div class="col-md-12">


                            

                              <h5 class=" mg-t-20-force">
                                    Expense</h5>

                                <table class="table display responsive nowrap members_table" width="100%"  >
                                    <thead>
                                        <tr>
                                            <th class="wd-25p text-left">
                                                Type	
                                            </th>
                                            <th class="wd-25p text-left">
                                                Date	
                                            </th>
                                            <th class="wd-25p text-left">
                                               Description	
                                            </th>
                                            <th class="wd-25p text-left">
                                                Receipt Number	
                                            </th>
											<th class="wd-25p text-left">
                                                Amount (EUR)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['data'] as $value)
                                <tr>
                                    <td>{{$value->travel_expense_list_expense_name}}</td>
                                    <td>{{date("d-M-Y", strtotime($value->travel_expense_list_date))}}</td>
                                    <td>{{$value->travel_expense_list_description}}</td>
                                    <td>{{$value->travel_expense_list_receipt_number}}</td>
                                    <td>{{$value->travel_expense_list_amount}}</td>
                                </tr>
                                @endforeach
                                    </tbody>
                                </table>
                              
                                
							
								
								
								
								
								
                            </div>
						<div class="col-md-8"> </div>
						
						<div class="col-md-4">
                        <b>Total ({{$data['data'][0]->currency_name}}) : <input type="text" class="form-control text-right " style=" font-weight:bold;" value="{{$value->total_amount}}" readonly=""/></b>
                    </div>
                    
               
                  
                        <!-- col-4 -->
                    </div>
         
                    <!-- form-layout-footer -->
                </div>











            </div>


 <!-- Designer card end -->






    <!-- card -->

    <!-- card -->
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>

<script>
    $(document).ready(function(){

  
  
   $(".status_tag").click(function(){
    $(".approval_workflow").fadeToggle();
  });
  
  
  
    });
</script>


