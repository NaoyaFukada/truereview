<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">TrueReviews</a>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <!-- Adjust URL pattern to include the 'truereview' prefix -->
                    <a
                        class="nav-link {{ Request::is('/') ? 'active' : '' }}"
                        href="{{ route('home') }}"
                        >Home</a
                    >
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link {{ Request::is('productions') ? 'active' : '' }}"
                        href="{{ route('productions') }}"
                        >Production</a
                    >
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link {{ Request::is('new-movie') ? 'active' : '' }}"
                        href="{{ route('new-movie') }}"
                        >Create New Movies</a
                    >
                </li>
            </ul>
        </div>
    </div>
</nav>
