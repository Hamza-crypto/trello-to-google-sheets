<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <a class="sidebar-toggle">
        <i class="hamburger align-self-center"></i>
    </a>
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home.child') }}">Products-App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home.child') }}">Home</a>
                </li>
                {{-- <li class="nav-item display-hidden">
                    <a class="nav-link" href="{{ route('add.products.child') }}">Add Products</a>
                </li> --}}
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login.child') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('signup.child') }}">Signup</a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.child') }}"><i class="fa fa-cart-shopping fa-lg"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('myOrders') }}">My Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('myAccount') }}">My Account</a>
                    </li>
                @endauth

            </ul>
        </div>
    </div>
</nav>
