@extends('Admin_dashboard.layout')

@section('contents')
    <div class="content ">

        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <i class="bi bi-globe2 small me-2"></i> Board
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Lists</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                            <tr>
                                <th>
                                    <input class="form-check-input select-all" type="checkbox"
                                        data-select-all-target="#products" id="defaultCheck1">
                                </th>

                                <th>Name</th>
                                <th>Closed</th>
                                <th>Position</th>
                                <th>Subscribed</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- <pre>
                            @php
                            
                                print_r( $products );
                                
                            @endphp
                            </pre> --}}
                            @foreach ($responseData as $list)
                                <tr>
                                    <td>
                                        <input class="form-check-input" type="checkbox">
                                    </td>
                                    <td>{{ $list['name'] }}</td>
                                    <td>{{ $list['closed'] ? 'Yes' : 'No' }}</td>
                                    <td>{{ $list['pos'] }}</td>
                                    <td>{{ $list['subscribed'] ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
