@extends('layouts.fixedMaster')

@section('content')
<style>
    @media only screen and (min-width: 992px) {
        .scrollable-section {
            max-height: 80vh; /* Make it scrollable if too many reviews */
            overflow-y: auto;
        }
    }

    /* Stack components vertically for screens less than 992px */
    @media only screen and (max-width: 991px) {
        .main-container {
            flex-direction: column; /* Stack panels vertically */
            height: auto;
            padding: 0;
        }

        .left-panel,
        .right-panel {
            width: 100%; /* Make both panels full-width */
            margin: 0 0 20px 0; /* Add spacing between sections */
        }

        .right-panel {
            padding: 0;
        }

        .right-panel {
            height: auto; /* Remove fixed height for right panel */
            overflow-y: visible; /* Allow full content to show */
        }
    }
</style>

<div class="container mt-5">
    <div class="main-container flex-column flex-lg-row">

        <!-- Left Panel (Movie Info) -->
        <div class="left-panel mb-4 mb-lg-0">
            <img src="{{ asset('imgs/' . $movie->image_path) }}" alt="{{ $movie->name }}">
            <h3 class="mt-3">{{ $movie->name }}</h3>
            <p><strong>Production:</strong> {{ $movie->production }}</p>
            <p><strong>Genre:</strong> {{ $movie->genre }}</p>
            <p><strong>Release Date:</strong> {{ $movie->released_date }}</p>

            <div class="rating-stars">
                <strong>Average Rating:</strong>
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= floor($movie->average_rating))
                        <i class="fas fa-star"></i>
                    @elseif ($i - 0.5 <= $movie->average_rating)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                @endfor
                {{ number_format($movie->average_rating, 1) }}
            </div>

            @if ($edit_mode)
                <a href=" {{ route('review.edit', $movie->id) }}" class="btn btn-warning mt-4 w-100">Edit Your Review</a>
            @else
                <a href="{{ route('review', $movie->id) }}" class="btn btn-primary mt-4 w-100">Add New Review</a>
            @endif

            <form method="POST" action="{{ route('movie.delete', $movie->id) }}" style="display:inline-block;" class="w-100">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger mt-4 w-100">Delete Movie</button>
            </form>
        </div>

        <!-- Right Panel (Reviews Section) -->
        <div class="right-panel">
            <!-- Display total number of reviews -->
            <h5 class="text-end"><i class="fas fa-users"></i> {{ count($reviews) }} Reviews</h5>

            <!-- Scrollable Reviews Section -->
            <div class="scrollable-section">
                @foreach($reviews as $review)
                    <div class="review-card">
                        <div class="reviewer-name">Reviewer Name: {{ $review->name }}</div>
                        <div class="rating-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $review->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                           <span>{{ number_format($review->rating, 0) }}</span>
                        </div>
                        <small class="text-muted">Reviewed on: {{ $review->date }}</small>
                        <p class="mt-2">{{ $review->review_text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Alert for Username Change Notification -->
@if (session('username_modified'))
    <script>
        window.onload = function() {
            alert("{{ session('username_modified') }}");
        };
    </script>
@endif


@endsection