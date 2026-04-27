@extends('frontend.layouts.master')
@section('title', 'FAQs | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'Frequently Asked Questions')

@section('content')

    <!-- FAQ Section -->
   
<section>
    <div class="container">
        <div class="row align-items-center justify-content-between">
        <div class="col-12 col-lg-6 mb-8 mb-lg-0">
            <img src="assets/images/about/04.png" alt="Image" class="img-fluid">
        </div>
        <div class="col-12 col-lg-6 col-xl-5">
            <div class="accordion" id="accordion">
            @foreach($faq as $index => $item)
            <div class="accordion-item rounded mb-2">
                <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button border-0 mb-0 bg-transparent" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $item->id }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                    aria-controls="collapse{{ $item->id }}">
                    {{ $item->question }}
                </button>
                </h2>
                <div id="collapse{{ $item->id }}" class="accordion-collapse border-0 collapse {{ $index == 0 ? 'show' : 'hide' }}" aria-labelledby="headingOne"
                data-bs-parent="#accordion">
                <div class="accordion-body text-muted">
                        {!! $item->answer !!}
                </div>
                </div>
            </div>

            @endforeach
            </div>
        </div>
        </div>
    </div>
</section>

@endsection

