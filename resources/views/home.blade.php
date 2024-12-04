@extends('layouts.master')

@section('content')
<div class="container mt-3" style="padding-bottom: 30px">
  
  <!-- Sorting Form for Screens >= 992px -->
  <form method="GET" action="{{ route('home') }}" class="d-none d-lg-block mb-4">
    <div class="row justify-content-end">
      <div class="col-auto">
        <label for="sort_by" class="form-label mb-0" style="font-size: 0.9rem;">Sort By:</label>
        <select name="sort_by" id="sort_by" class="form-control form-control-sm">
          <option value="review_count" {{ request('sort_by') == 'review_count' ? 'selected' : '' }}>Number of Reviews</option>
          <option value="average_rating" {{ request('sort_by') == 'average_rating' ? 'selected' : '' }}>Average Rating</option>
        </select>
      </div>

      <div class="col-auto">
        <label for="order" class="form-label mb-0" style="font-size: 0.9rem;">Order:</label>
        <select name="order" id="order" class="form-control form-control-sm">
          <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
          <option value="desc" {{ request('order', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
        </select>
      </div>

      <div class="col-auto d-flex align-items-end">
        <button type="submit" class="btn btn-outline-primary btn-sm">Apply</button>
      </div>
    </div>
  </form>

  <!-- Filter Button for Screens < 992px -->
  <div class="d-block d-lg-none text-end mb-4">
    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
      <i class="fas fa-filter"></i> Filter
    </button>
  </div>

  <!-- Filter Modal for Smaller Screens -->
  <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="GET" action="{{ route('home') }}">
            <div class="mb-3">
              <label for="sort_by_modal" class="form-label" style="font-size: 0.9rem;">Sort By:</label>
              <select name="sort_by" id="sort_by_modal" class="form-control">
                <option value="review_count" {{ request('sort_by') == 'review_count' ? 'selected' : '' }}>Number of Reviews</option>
                <option value="average_rating" {{ request('sort_by') == 'average_rating' ? 'selected' : '' }}>Average Rating</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="order_modal" class="form-label" style="font-size: 0.9rem;">Order:</label>
              <select name="order" id="order_modal" class="form-control">
                <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                <option value="desc" {{ request('order', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
              </select>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary btn-sm">Apply</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3" style="row-gap: 30px;">
    
    <!-- Loop through each movie and display its details -->
    @foreach($movies as $movie)
    <div class="col">
      <a href="{{ route('movie', ['id' => $movie->id]) }}" class="text-decoration-none">
        <div class="card h-100">
          <img src="{{ asset('imgs/' . $movie->image_path) }}" class="card-img-top" alt="{{$movie->name}}">
          <div class="card-body">
            <h5 class="card-title text-dark">{{ $movie->name }}</h5>
            <p class="rating-stars home">
              <!-- Dynamic Star Rating -->
              @for ($i = 1; $i <= 5; $i++)
                @if ($i <= floor($movie->average_rating))
                  <i class="fas fa-star"></i>
                @elseif ($i - 0.5 <= $movie->average_rating)
                  <i class="fas fa-star-half-alt"></i>
                @else
                  <i class="far fa-star"></i>
                @endif
              @endfor
              <!-- To format a numeric value with a specified number of decimal places -->
              <span> {{ number_format($movie->average_rating, 1) }}</span>
            </p>
            <p class="review-count"><i class="fas fa-users"></i> {{ $movie->review_count }} Reviews</p>
          </div>
        </div>
      </a>
    </div>
    @endforeach

  </div>
</div>
@endsection
