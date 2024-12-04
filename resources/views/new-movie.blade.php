<!-- This is a form page to allow user to create a new movie -->
@extends('layouts.master')

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <!-- Movie Creation Form Panel -->
        <div class="col-md-6">
            <div class="movie-form card p-4 shadow-lg" style="background-color: #f8f9fa; border-radius: 15px; max-width: 500px;">
                <h3 class="mb-4 text-center font-weight-bold" style="color: #333;">Create a New Movie</h3>

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

                <!-- Form for creating a new movie -->
                <form method="POST" action="{{ route('movie.create') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Movie Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Movie Name</label>
                        <input type="text" name="name" class="form-control" id="name" style="border-radius: 10px; padding: 8px; font-size: 1rem;">
                    </div>

                    <!-- Production -->
                    <div class="mb-3">
                        <label for="production" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Production</label>
                        <input type="text" name="production" class="form-control" id="production" style="border-radius: 10px; padding: 8px; font-size: 1rem;">
                    </div>

                    <!-- Genre -->
                    <div class="mb-3">
                        <label for="genre" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Genre</label>
                        <input type="text" name="genre" class="form-control" id="genre" style="border-radius: 10px; padding: 8px; font-size: 1rem;">
                    </div>

                    <!-- Release Date -->
                    <div class="mb-3">
                        <label for="released_date" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Release Date</label>
                        <input type="date" name="released_date" class="form-control" id="released_date" style="border-radius: 10px; padding: 8px; font-size: 1rem;">
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="image" class="form-label" style="font-weight: bold; font-size: 1rem; color: #555;">Movie Image</label>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="image" accept="image/*">
                        </div>
                    </div>


                    <!-- Submit Button centered -->
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 10px; padding: 10px 30px; font-size: 1.1rem;">Create Movie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
