<!-- This is a page to display a list of productions with their average rating -->

@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Production List</h2>
    <div class="row">
        @foreach($productions as $production)
        <div class="col-12 mb-4">
            <a href="{{ route('production', ['name' => urlencode($production->production)]) }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ $production->production }}</h5>
                        <p class="rating-stars home mb-0 d-flex align-items-center">
                            <!-- Dynamic Star Rating -->
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($production->average_rating))
                                    <i class="fas fa-star"></i> <!-- Filled Star -->
                                @elseif ($i - 0.5 <= $production->average_rating)
                                    <i class="fas fa-star-half-alt"></i> <!-- Half Filled Star -->
                                @else
                                    <i class="far fa-star"></i> <!-- Empty Star -->
                                @endif
                            @endfor
                            <!-- Display numeric value of the average rating -->
                            <span class="ms-2">{{ number_format($production->average_rating, 1) }}</span>
                        </p>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
