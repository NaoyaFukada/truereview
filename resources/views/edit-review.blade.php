@extends('layouts.fixedMaster')

@section('content')
<div class="container mt-5">
    <div class="row gy-4">
        <!-- Left Panel (Movie Info) -->
        <div class="col-12 col-md-5">
            <div class="review card p-4 shadow-lg h-100" style="background-color: #f8f9fa; border-radius: 15px;">
                <img src="{{ asset('imgs/' . $movie->image_path) }}" alt="{{ $movie->name }}" class="img-fluid w-100" style="border-radius: 15px;">
                <h3 class="mt-3 text-center">{{ $movie->name }}</h3>
                <p><strong>Production:</strong> {{ $movie->production }}</p>
                <p><strong>Genre:</strong> {{ $movie->genre }}</p>
                <p><strong>Release Date:</strong> {{ $movie->released_date }}</p>

                <div class="rating-stars d-flex align-items-center justify-content-center">
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
        <div class="col-12 col-md-7">
            <div class="review card p-4 shadow-lg h-100" style="background-color: #f8f9fa; border-radius: 15px;">
                <h3 class="mb-4 text-center font-weight-bold" style="color: #333;">Edit Your Review</h3>

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

                <form method="POST" action="{{ route('review.update') }}">
                    @csrf
                    <!-- Movie ID (hidden) -->
                    <input type="hidden" name="movie_id" value="{{ $movie->id }}">

                    <!-- User ID (hidden) -->
                    <input type="hidden" name="user_id" value="{{ $user_review->user_id }}">

                    <!-- Username (Readonly) -->
                    <div class="mb-3">
                        <label for="username" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Username</label>
                        <input type="text" name="username" class="form-control" id="username" style="border-radius: 10px;" value="{{ $user_name }}" readonly>
                    </div>

                    <!-- Rating (Default Value from User Review) -->
                    <div class="mb-3">
                        <label for="rating" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Rating (1-5 Stars)</label>
                        <select name="rating" class="form-select" id="rating" style="border-radius: 10px;">
                            <option value="1" {{ $user_review->rating == 1 ? 'selected' : '' }}>1 - Poor</option>
                            <option value="2" {{ $user_review->rating == 2 ? 'selected' : '' }}>2 - Fair</option>
                            <option value="3" {{ $user_review->rating == 3 ? 'selected' : '' }}>3 - Good</option>
                            <option value="4" {{ $user_review->rating == 4 ? 'selected' : '' }}>4 - Very Good</option>
                            <option value="5" {{ $user_review->rating == 5 ? 'selected' : '' }}>5 - Excellent</option>
                        </select>
                    </div>

                    <!-- Review Text (Default Value from User Review) -->
                    <div class="mb-3">
                        <label for="review_text" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Review Text</label>
                        <textarea name="review_text" class="form-control" id="review_text" rows="5" style="border-radius: 10px;">{{ $user_review->review_text }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 10px;">Update Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
