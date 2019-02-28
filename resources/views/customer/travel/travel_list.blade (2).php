@extends('layouts.customer.DashboardLayout')

@section('content')





    <div class="am-pagebody">
        <div class="card pd-20 pd-sm-40">
            <div class="row ">
                <div class="col-lg-8">
                    <h6 class="card-body-title">
                        Travel Request List</h6>
                    <p class="mg-b-20 mg-sm-b-30">
                      </p>
                </div>

            </div>
            <div id="wizard1">
                <section>

                    <div class="table-wrapper">

                        <div class="">
                            <table class="table-responsive table display responsive nowrap designation_table" width="100%">
                                <thead>
                                <tr>
                                    <th class="text-left" style="width:15%;">TR#</th>
                                    <th class="wd-25p text-left" style="width:15%;">Date</th>
                                    <th class="wd-25p text-left" style="width:15%;">Type</th>
                                    <th class="wd-25p text-left" style="width:15%;">Airline</th>
                                    <th class="wd-25p text-left" style="width:25%;">Employee</th>
                                    <th class="wd-25p text-center" style="width:25%;">Status</th>
                                    <th class="wd-25p text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>

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
        $('.designation_table').DataTable({
            "order": [[0, 'desc' ]],
            processing: true,
            serverSide: true,
            ajax: "{{url('get_travel_list_by_user')}}",
            columnDefs: [{
                "orderSequence": ["desc"],
                targets: [0],
                className: 'mdl-data-table__cell--non-numeric'
            }],
            columns: [
                {data: 'request_no', name: 'request_no'},
                {data: 'created_date', name: 'created_date'},

                {data: 'travel_type', name: 'travel_type'},
                {data: 'airline', name: 'airline'},

                {data: 'full_name', name: 'full_name'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    });
</script>