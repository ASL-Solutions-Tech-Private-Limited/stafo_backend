@extends('frontend.layouts.master')
@section('title', 'Frequently Asked Questions (FAQs) | STAFO HRMS')
@section('heading', 'Frequently Asked Questions')

@section('content')

    <!-- FAQ Main Section -->
    <section class="py-5 bg-light-subtle position-relative overflow-hidden">
        <div class="container py-lg-4">
            
            <!-- Section Header -->
            <!-- <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
                    <i class="fa-solid fa-circle-question me-1"></i> Help & Knowledge Hub
                </span>
                <h2 class="fw-bold text-dark mb-2">Frequently Asked Questions</h2>
                <p class="text-muted mx-auto" style="max-width: 650px;">
                    Find quick answers to common questions about STAFO HRMS, payroll automation, biometric & GPS attendance, security, and integrations.
                </p>
            </div> -->

            <div class="row align-items-start g-4 g-lg-5">
                <!-- Left Column: Dynamic Interactive Topic Image & Quick Support Card -->
                <div class="col-12 col-lg-5 position-sticky" style="top: 100px;">
                    <!-- Dynamic Featured Image Card -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                        <div class="position-relative bg-light text-center p-3" style="min-height: 280px; display: flex; align-items: center; justify-content: center;">
                            @php
                                $firstFaqImage = $faq->first() && $faq->first()->image ? asset('uploads/faq/' . $faq->first()->image) : asset('main/images/features-one.webp');
                            @endphp
                            <img id="faqDynamicImg" src="{{ $firstFaqImage }}" alt="FAQ Topic" class="img-fluid rounded-3 shadow-sm transition-all" style="max-height: 270px; width: 100%; object-fit: cover; transition: all 0.35s ease-in-out;">
                            
                            <span id="faqDynamicBadge" class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 text-white px-3 py-2 rounded-pill shadow-sm">
                                {{ $faq->first() ? $faq->first()->question : 'STAFO HRMS' }}
                            </span>
                        </div>
                        <div class="card-body p-4 text-center">
                            <h5 class="fw-bold mb-1" id="faqCardTitle">Explore STAFO Features</h5>
                            <p class="text-muted small mb-0" id="faqCardDesc">Click on any FAQ category on the right to view answers and details.</p>
                        </div>
                    </div>

                    <!-- Quick Support Contact Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center" style="background: linear-gradient(135deg, #1d274b 0%, #304179 100%); color: #ffffff;">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle" style="width: 54px; height: 54px;">
                                <i class="fa-solid fa-headset fs-3 text-white"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: #ffffff !important;">Still have questions?</h5>
                        <p class="small mb-3" style="color: rgba(255, 255, 255, 0.85) !important;">Our HRMS solution specialists are ready to help you 24/7 with any query or customized onboarding demo.</p>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <a href="tel:+918389039361" class="btn btn-primary btn-sm rounded-pill px-3 text-white" style="background-color: #4361ee; border-color: #4361ee; color: #ffffff !important;">
                                <i class="fa-solid fa-phone me-1"></i> +91 8389039361
                            </a>
                            <a href="mailto:sales@stafo.in" class="btn btn-outline-light btn-sm rounded-pill px-3" style="border-color: rgba(255, 255, 255, 0.6); color: #ffffff !important;">
                                <i class="fa-solid fa-envelope me-1"></i> sales@stafo.in
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Accordion List -->
                <div class="col-12 col-lg-7">
                    <div class="accordion custom-faq-accordion" id="faqAccordion">
                        @foreach($faq as $index => $item)
                            @php
                                $itemImage = $item->image ? asset('uploads/faq/' . $item->image) : asset('main/images/features-one.webp');
                            @endphp
                            <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                                <h2 class="accordion-header" id="heading{{ $item->id }}">
                                    <button class="accordion-button border-0 p-4 bg-white fw-bold text-dark fs-6 {{ $index == 0 ? '' : 'collapsed' }}" 
                                        type="button"
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $item->id }}" 
                                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                        aria-controls="collapse{{ $item->id }}"
                                        data-img="{{ $itemImage }}"
                                        data-title="{{ $item->question }}"
                                        onclick="updateFaqShowcase(this)">
                                        <div class="d-flex align-items-center gap-3 w-100 me-2">
                                            @if($item->image)
                                                <img src="{{ $itemImage }}" alt="Icon" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; min-width: 38px;">
                                                    {{ $index + 1 }}
                                                </div>
                                            @endif
                                            <span class="text-dark">{{ $item->question }}</span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $item->id }}" 
                                    class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                    aria-labelledby="heading{{ $item->id }}"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body px-4 pb-4 pt-1 bg-white border-top">
                                        <!-- Inline Image inside Accordion for mobile/responsive context -->
                                        @if($item->image)
                                            <div class="d-lg-none mb-3 text-center">
                                                <img src="{{ $itemImage }}" alt="{{ $item->question }}" class="img-fluid rounded-3 shadow-sm border" style="max-height: 200px; width: 100%; object-fit: cover;">
                                            </div>
                                        @endif
                                        <div class="faq-rich-content text-muted lh-base">
                                            {!! $item->answer !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Script for FAQ Image Sync -->
    <script>
        function updateFaqShowcase(btn) {
            const imgSrc = btn.getAttribute('data-img');
            const title = btn.getAttribute('data-title');
            
            const dynamicImg = document.getElementById('faqDynamicImg');
            const dynamicBadge = document.getElementById('faqDynamicBadge');
            const cardTitle = document.getElementById('faqCardTitle');

            if (dynamicImg && imgSrc) {
                dynamicImg.style.opacity = '0.3';
                setTimeout(() => {
                    dynamicImg.src = imgSrc;
                    dynamicImg.style.opacity = '1';
                }, 150);
            }

            if (dynamicBadge && title) {
                dynamicBadge.innerText = title;
            }

            if (cardTitle && title) {
                cardTitle.innerText = title;
            }
        }
    </script>

    <style>
        .custom-faq-accordion .accordion-button:not(.collapsed) {
            color: #4361ee !important;
            background-color: #f8faff !important;
            box-shadow: none !important;
        }
        .custom-faq-accordion .accordion-button:focus {
            box-shadow: none !important;
            border-color: transparent !important;
        }
        .custom-faq-accordion .faq-rich-content h4 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1d274b;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .custom-faq-accordion .faq-rich-content h4:first-child {
            margin-top: 0.5rem;
        }
        .custom-faq-accordion .faq-rich-content p {
            margin-bottom: 1rem;
            color: #555;
            font-size: 0.95rem;
        }
    </style>

@endsection
