@extends('Admin_dashboard.layout')

@section('contents')
    <div class="content ">

        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <i class="bi bi-globe2 small me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-9">

                @include('Admin_dashboard.includes.products.headerSection')

                <div class="row g-4">
                    @foreach ($products as $product)
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="card card-hover">
                                <a href="#">
                                    <img src="{{ $product['thumbnail'] }}" class="card-img-top" alt="..." height="200px">
                                </a>
                                <div class="card-body">
                                    <a href="#">
                                        <h5 class="card-title mb-3">{{ $product['title'] }}</h5>
                                    </a>
                                    <div class="d-flex gap-3 mb-3 align-items-center">
                                        <h4 class="mb-0">${{ $product['price'] }}.00</h4>
                                    </div>
                                    <div class="d-flex gap-2 mb-3">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-muted"></i>
                                        <span>({{ $product['rating'] }})</span>
                                    </div>
                                    <div class="d-flex">
                                        <a href="#" class="btn btn-primary">Add to Cart</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                <nav class="mt-5" aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            @include('Admin_dashboard.includes.products.filterSection')
        </div>
    </div>
@endsection
