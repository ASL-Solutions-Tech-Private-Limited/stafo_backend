@extends('admin.layouts.layout')

@section('content')
    <div class="container">
          <div class="card mt-4 p-3 shadow-sm border-0 emp-data">
            <div class="card-header">
                <h3 class="card-title">Chat List</h3>
            </div>
            <div class="container">
                @foreach ($chats as $chat)
                <div class="row mt-2">
                    <div class="col-md-2">
                    @if ($chat->company->image_name)
                        <img src="{{ asset('uploads/compnay_logo/' . $chat->company->image_name) }}"
                            alt="Company Logo" class="ms-2" width="30px" height="30px" />
                    @else
                        <img src="{{ asset('uploads/compnay_logo/no-image.png') }}" alt="Current Logo"
                            class="img-thumbnail" width="30px" height="30px">
                    @endif
                    </div>
                    <div class="col-md-2">
                       <a href="{{ route('adminchat',$chat->company_id) }}"> <strong>{{ $chat->company->company_name }}</strong></a>
                    </div>
                </div>
                    <div></div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

