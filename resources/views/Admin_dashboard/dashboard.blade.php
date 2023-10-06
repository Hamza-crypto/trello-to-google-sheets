@extends('Admin_dashboard.layout')

@section('contents')
    <div class="content ">

        <div class="row row-cols-1 row-cols-md-3 g-4">
            {{-- ------------------------- sales chart -------------------- --}}

            <div class="col-lg-12 col-md-12">
                <div class="card widget h-100">
                    <div class="card-header d-flex">
                        <h6 class="card-title">
                            All Boards
                            <a href="#" class="bi bi-question-circle ms-1 small" data-bs-toggle="tooltip"
                                title="Daily orders and sales"></a>
                        </h6>

                    </div>
                    <div class="card-body">
                        <div class="d-md-flex align-items-center mb-3">
                            <div class="row">
                                @if (isset($responseData) && is_array($responseData))
                                    @foreach ($responseData as $board)
                                        <div class="row">
                                            <div class="col">
                                                <h4>{{ $board['name'] }}</strong></h4> <br>
                                                <!-- Display the board's shortLink -->
                                                <input type="hidden" name="shortLink" value="{{ $board['shortLink'] }}">
                                                <!-- Add more fields as needed -->
                                            </div>
                                            <div class="col">
                                                <!-- Create a form to submit the shortLink to the controller method -->
                                                <form action="{{ route('fetchData') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="shortLink" value="{{ $board['shortLink'] }}">
                                                    <button type="submit" class="btn btn-primary">Fetch Lists</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p>No data available.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
