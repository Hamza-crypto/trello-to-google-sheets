<div class="menu">
    <div class="menu-header">
        
        <a href="{{ route('dashboard') }}" class="menu-header-logo">
            {{-- <img src="../../assets/images/logo.svg" alt="logo"> --}}
            <h4>Trello Tracker</h4>
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-lg menu-close-btn">
            <i class="bi bi-x"></i>
        </a>
    </div>
    <div class="menu-body mt-3">
        
        <ul>
            <li>
                <a class="{{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>
            {{-- <li>
                <a class="{{ request()->is('admin/orders') || request()->is('orders') ? 'active' : '' }}"
                    href="{{ route('orders') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a class="{{ request()->is('admin/productsList') || request()->is('productsListView') || request()->is('admin/productsGrid') || request()->is('productsGridView') ? 'active' : '' }}"
                    href="{{ route('productsListView') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-truck"></i>
                    </span>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a class="{{ request()->is('admin/allCustomers') || request()->is('allCustomers') ? 'active' : '' }}"
                    href="{{ route('allCustomers') }}">
                    <span class="nav-link-icon">
                        <i class="bi-person-badge"></i>
                    </span>
                    <span>Customers</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Invoices</span>
                </a>
                <ul>
                    <li>
                        <a href="./invoices.html">List</a>
                    </li>
                    <li>
                        <a href="./invoice-detail.html">Detail</a>
                    </li>
                </ul>
            </li> --}}
        </ul>
    </div>
</div>
<!-- ./  menu -->
