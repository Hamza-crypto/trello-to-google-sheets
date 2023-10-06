<div class="card mb-4">
    <div class="card-body">
        <div class="d-md-flex gap-4 align-items-center">
            <div class="d-md-flex gap-4 align-items-center">
                <form class="mb-3 mb-md-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <select class="form-select">
                                <option>Sort by</option>
                                <option value="desc">Desc</option>
                                <option value="asc">Asc</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="dropdown ms-auto">
                <a href="#" data-bs-toggle="dropdown" class="btn btn-primary dropdown-toggle"
                    aria-haspopup="true" aria-expanded="false">View</a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{ route('productsListView') }}" class="dropdown-item">List View</a>
                    <a href="{{ route('productsGridView') }}" class="dropdown-item">Grid View</a>
                </div>
            </div>
        </div>
    </div>
</div>