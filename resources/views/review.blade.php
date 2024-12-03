<!-- This is a form page to allow user to write a review on a movie -->
@extends('layouts.master')

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <!-- Left Panel (Movie Info) -->
        <div class="col-md-5">
            <div class="review card p-4 shadow-lg" style="background-color: #f8f9fa; border-radius: 15px; max-width: 400px;">
                <img src="{{ asset('imgs/' . $movie->image_path) }}" alt="{{ $movie->name }}" class="img-fluid" style="border-radius: 15px;">
                <h3 class="mt-3 text-center">{{ $movie->name }}</h3>
                <p><strong>Production:</strong> {{ $movie->production }}</p>
                <p><strong>Genre:</strong> {{ $movie->genre }}</p>
                <p><strong>Release Date:</strong> {{ $movie->released_date }}</p>

                <div class="rating-stars d-flex align-items-center">
                    <strong>Average Rating:</strong>&nbsp;
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($movie->average_rating))
                            <i class="fas fa-star"></i>
                        @elseif ($i - 0.5 <= $movie->average_rating)
                            <i class="fas fa-star-half-alt"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                    &nbsp;<span>{{ number_format($movie->average_rating, 1) }}</span>
                </div>
            </div>
        </div>

        <!-- Right Panel (Review Form) -->
        <div class="col-md-6">
            <div class="review card p-4 shadow-lg" style="background-color: #f8f9fa; border-radius: 15px; max-width: 500px;">
                <h3 class="mb-4 text-center font-weight-bold" style="color: #333;">Write a Review</h3>

                <!-- Display Error Messages -->
                @if (session('errors'))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach (session('errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('review.create') }}">
                    @csrf
                    <!-- Movie ID (hidden) -->
                    <input type="hidden" name="movie_id" value="{{ $movie->id }}">

                    <!-- Username -->
                    @if (session('username'))
                        <div class="mb-3">
                            <label for="username" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Username</label>
                            <input type="text" name="username" class="form-control" id="username" style="border-radius: 10px; padding: 8px; font-size: 1rem;" value="{{ session('username') }}" readonly>
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="username" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Username</label>
                            <input type="text" name="username" class="form-control" id="username" style="border-radius: 10px; padding: 8px; font-size: 1rem;">
                        </div>
                    @endif

                    <!-- Rating -->
                    <div class="mb-3">
                        <label for="rating" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Rating (1-5 Stars)</label>
                        <select name="rating" class="form-select" id="rating" style="border-radius: 10px; padding: 8px; font-size: 1rem; width: 100%; border: 1px solid #ced4da; box-shadow: none;"">
                            <option value="" selected disabled>Select rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>

                    <!-- Review Text -->
                    <div class="mb-3">
                        <label for="review_text" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Review Text</label>
                        <textarea name="review_text" class="form-control" id="review_text" rows="5" style="border-radius: 10px; padding: 8px; font-size: 1rem;"></textarea>
                    </div>

                    <!-- Submit Button centered -->
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 10px; padding: 10px 30px; font-size: 1.1rem;">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Alert for Username Change Notification -->
@if (session('username_modified'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                alert("{{ session('username_modified') }}");
            }, 500); // Delay to ensure the page has fully loaded
        });
    </script>
@endif



@endsection
