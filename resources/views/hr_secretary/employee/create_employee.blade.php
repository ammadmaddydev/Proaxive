@extends('layouts.hr.DashboardLayout')

@section('content')





    <div class="am-pagebody">
        <div class="card pd-20 pd-sm-40">
            <div class="row ">
                <div class="col-lg-8">
                    <h6 class="card-body-title">
                        Employee Management</h6>
                    <p class="mg-b-20 mg-sm-b-30">
                        Create new employee account.</p>
                </div>

            </div>
            <div id="wizard1">
                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif
                <section>


                    <div class="form-layout">
                        <form method="post" action="{{url('/create_employee')}}">
                            <div class="row mg-b-25">
                                @csrf
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">First Name: <span class="tx-danger">*</span></label>
                                        <input class="form-control" type="text" name="firstname"   placeholder="Type Your Firstname" required>
                                    </div>
                                </div><!-- col-4 -->
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Last Name: <span class="tx-danger">*</span></label>
                                        <input class="form-control" type="text" name="lastname"   placeholder="Type Your Lastname" required>
                                    </div>
                                </div><!-- col-4 -->
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Email: <span class="tx-danger">*</span></label>
                                        <input class="form-control" type="email" name="email"   placeholder="Type Your Email" required>
                                    </div>
                                </div><!-- col-4 -->

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Password: <span class="tx-danger">*</span></label>
                                        <input class="form-control" type="password" name="password"   placeholder="Type Your Password" required>
                                    </div>
                                </div><!-- col-4 -->

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label">Phone: <span class="tx-danger">*</span></label>
                                        <input class="form-control" type="text" name="phone"   placeholder="Type Your Phone" required>
                                    </div>
                                </div><!-- col-4 -->




                                <div class="col-lg-4">
                                    <div class="form-group mg-b-10-force">
                                        <label class="form-control-label">Department: </label>
                                        <select class="form-control " data-placeholder="department" name="department" required>
                                            <option label="Choose one" value="" selected></option>

                                            @foreach($department as $key => $data)
                                                <option value="{{$data->id}}">
                                                    {{$data->name}}
                                                </option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group mg-b-10-force">
                                        <label class="form-control-label">Desgination: </label>
                                        <select class="form-control " data-placeholder="designation" name="designation" required>
                                            <option label="Choose one" value="" selected></option>
                                            @foreach($designation as $key => $data)
                                                <option value="{{$data->id}}">
                                                    {{$data->name}}
                                                </option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group mg-b-10-force">
                                        <label class="form-control-label">Role: </label>
                                        <select class="form-control " data-placeholder="role" name="role" required>
                                            <option label="Choose one" value="" selected></option>
                                            @foreach($role as $key => $data)
                                                <option value="{{$data->id}}">
                                                    {{$data->name}}
                                                </option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- col-4 -->
                            </div><!-- row -->

                            <div class="form-layout-footer text-right">
                                <button class="btn btn-info mg-r-5" type="submit"> Create Account</button>

                            </div><!-- form-layout-footer -->
                        </form>
                    </div><!-- form-layout -->
                    <hr/>
                        <h6 class="card-body-title">Employee List</h6>


                        <div class="table-wrapper">
                            <table class="table display responsive nowrap employee_table" width="100%">
                                <thead>
                                <tr>

                                    <th class="wd-15p">First Name</th>
                                    <th class="wd-20p">Last Name</th>
                                    <th class="wd-15p">Email</th>
                                    <th class="wd-15p">Designation</th>
                                    <th class="wd-25p text-center">Department</th>
                                    <th class="wd-25p text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div><!-- table-wrapper -->


                </section>

            </div>
        </div>
        <!-- card -->

        <!-- card -->
    </div>



@endsection

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>



<script>
    $(document).ready(function(){
        $('.employee_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: 'http://172.16.1.250:82/proaxive/public/view_employees',
            columnDefs: [{
                targets: [0, 1, 2],
                className: 'mdl-data-table__cell--non-numeric'
            }],
            columns: [
                {data: 'first_name', name: 'first_name'},
                {data: 'last_name', name: 'last_name'},
                {data: 'email', name: 'email'},
                {data: 'designation_name', name: 'designation_name'},
                {data: 'department_name', name: 'department_name'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    });
</script>