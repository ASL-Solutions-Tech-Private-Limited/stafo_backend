@extends('admin.layouts.layout')

@section('title', 'Edit FAQ')

@section('content')
    <div class="container">
        <h3 class="mb-3">Edit FAQ</h3>
        <form action="{{ route('faq.update', $faq->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="question" class="form-label">Question</label>
                <input type="text" name="question" id="question" class="form-control"
                    value="{{ old('question', $faq->question) }}" required>
            </div>

            <div class="mb-3">
                <label for="answer" class="form-label">Answer</label>
                <textarea name="answer" id="answer" class="form-control" rows="5"
                    required>{{ old('answer', $faq->answer) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update FAQ</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#answer'))
            .catch(error => {
                console.error(error);
            });
    </script>

@endsection