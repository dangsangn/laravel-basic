@extends('admin.admin_master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header">
                    <h5 class="card-title mb-0">Reviews</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Message</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $key => $review)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $review->name }}</td>
                                <td>{{ $review->position }}</td>
                                <td>{{ $review->message }}</td>
                                <td><img src="{{ asset($review->image) }}" alt="{{ $review->name }}" width="50"></td>
                                <td>
                                    <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
