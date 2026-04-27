@extends('admin.layouts.layout')

@section('title', 'Create FAQ')
@section('css')

@endsection
@section('content')
    <div class="container">
        <h3 class="mb-3">Create FAQ</h3>

        <form action="{{ route('faq.store') }}" method="POST" id="faq-form">
            @csrf
            <div class="mb-3">
                <label for="question" class="form-label">Question</label>
                <input type="text" name="question" id="question" class="form-control" value="{{ old('question') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="answer" class="form-label">Answer</label>
                <textarea id="answer" name="answer" class="form-control" rows="5"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save FAQ</button>
        </form>
    </div>
@endsection

@section('scripts')


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


@endsection