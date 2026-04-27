@extends('admin.layouts.layout')
@section('title', 'Edit User')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access">
                <div class="user-welcome mb-3">
                    <h3>Edit User</h3>
                </div>
                <!-- <button class="btn primary-bg"><i class="fa-solid fa-plus"></i> Add New User</button> -->
            </div>
            <form action="{{ route('cms.update', $cms->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="new-user-form">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6 mb-3">
                            <label for="title">Title<span class="red">*</span></label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title', $cms->title) }}" placeholder="Enter Title" required>
                            <span class="error error-title"><span>
                        </div>

                        <!-- Short Description -->
                        <div class="col-md-6 mb-3">
                            <label for="short_description">Short Description</label>
                            <textarea name="short_description" id="short_description" class="form-control" placeholder="Enter Short Description"
                                rows="1">{{ old('short_description', $cms->short_description) }}</textarea>
                            <span class="error error-short-description"><span>
                        </div>



                        <!-- Meta Title -->
                        <div class="col-md-6 mb-3">
                            <label for="meta_title">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control"
                                value="{{ old('meta_title', $cms->meta_title) }}" placeholder="Enter Meta Title">
                            <span class="error error-meta-title"><span>
                        </div>

                        <!-- Meta Description -->
                        <div class="col-md-6 mb-3">
                            <label for="meta_description">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" class="form-control" placeholder="Enter Meta Description"
                                rows="1">{{ old('meta_description', $cms->meta_description) }}</textarea>
                            <span class="error error-meta-description"><span>
                        </div>
                        <!-- Long Description -->
                        <div class="col-md-12 mb-3">
                            <label for="long_description">Long Description</label>
                            <textarea name="long_description" id="long_description" class="form-control" placeholder="Enter Long Description"
                                rows="5">{{ old('long_description', $cms->long_description) }}</textarea>
                            <span class="error error-long-description"><span>
                        </div>
                        <!-- Image -->
                        <div class="col-md-12 mb-3">
                            <label for="image">Image</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            @if ($cms->image)
                                <div class="mt-2">
                                    <img src="{{ asset('uploads/cms/' . $cms->image) }}" alt="Current Image" width="100">
                                </div>
                            @endif
                            <span class="error error-image"><span>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('cms.list') }}">
                                <button type="button" class="btn bg-danger text-white">Cancel</button>
                            </a>
                            <button class="btn btn-primary text-white submit" type="submit">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>

@endsection
@section('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

    <script>
        ClassicEditor
            .create(document.querySelector('#long_description'))
            .catch(error => {
                console.error(error);
            });
           
    </script>

@endsection
