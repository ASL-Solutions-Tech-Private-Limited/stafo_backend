@extends('frontend.layouts.master')
@section('title', 'Blogs | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'Blogs')
@section('content') 

<section class="pt-0">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <!-- Blog Card -->
              <div class="card border-0 bg-transparent">
                @if($blog->image == null)
                    <img class="card-img-top" src="{{ asset('img/no_image.jpg') }}" height="300" alt="{{ $blog->title }}">
                @else
                    <img class="shadow" src="{{ asset('uploads/blog/'.$blog->image) }}" alt="Blog Image">
                @endif
                <div class="card-body px-0">
                  <div>
                    <div class="d-inline-block bg-light text-center px-2 py-1 rounded me-2">
                      <span class="text-primary">{{ date('d',strtotime($blog->date)) }}</span> {{ date('M',strtotime($blog->date)) }}
                    </div>
                    <a class="d-inline-block btn-link" href="#">{{ $blog->category->name}}</a>
                  </div>

                  <h2 class="h5 my-4">
                    <a class="link-title" href="#">
                      {{ $blog->title }}
                    </a>
                  </h2>

                  {!! $blog->short_description !!}

                  <blockquote class="card bg-primary border-0 p-5 mt-5 text-white">
                    "This HR solution helped us scale remote operations without worrying about manual tracking or
                    compliance issues. Everything is in one place."
                    <span class="mt-2 fst-italic font-w-6">- Dipankar Sarkar, HR Head at Stafo</span>
                  </blockquote>

                  <div class="d-md-flex justify-content-between">
                    <div class="d-flex align-items-center">
                      <h6 class="mb-0 me-4">Share It:</h6>
                      <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                          <a class="border rounded px-2 py-1 text-dark" target="_blank"
                           href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}">
                          <i class="la la-facebook"></i>
                          </a>
                        </li>
                        <li class="list-inline-item">
                          <a class="border rounded px-2 py-1 text-dark" target="_blank"
                           href="https://www.instagram.com/?url={{ urlencode(Request::url()) }}">
                          <i class="la la-instagram"></i>
                          </a>
                        </li>
                        <li class="list-inline-item">
                          <a class="border rounded px-2 py-1 text-dark" target="_blank"
                           href="https://twitter.com/intent/tweet?url={{ urlencode(Request::url()) }}&text={{ urlencode($blog->title) }}">
                          <i class="fa-brands fa-x-twitter"></i>
                          </a>
                        </li>
                        <li class="list-inline-item">
                          <a class="border rounded px-2 py-1 text-dark" target="_blank"
                           href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Request::url()) }}&title={{ urlencode($blog->title) }}">
                          <i class="la la-linkedin"></i>
                          </a>
                        </li>
                      </ul>
                    </div>
                    <div class="d-flex align-items-center text-md-end mt-5 mt-md-0">
                      <h6 class="mb-0 me-4">Tags:</h6>
                      <ul class="list-inline mb-0">
                        @if($blog->tags->count()>0)
                  @foreach($blog->tags as $tag)
                   
                    <li class="list-inline-item">
                          <a class="btn-link rounded d-inline-block p-2 bg-light m-1" href="#">{{ $tag->name }}</a>
                        </li>
                  @endforeach
                @endif
                      </ul>
                    </div>
                  </div>

                  <div class="mt-6 shadow p-5">
                    <div class="mb-4">
                      <h2>All Comments</h2>
                    </div>

                    @if($blog->comments->count()>0)
                  @foreach($blog->comments as $comment)

                    <div class="row border p-4 my-5 rounded">
                      <div class="mb-4 mb-md-0 col-md-auto">
                        <img class="img-fluid rounded shadow" alt="image" src="{{ asset('assets/images/thumbnail/01.jpg') }}">
                      </div>
                      <div class="col-md">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                          <h6 class="mb-0">{{ $comment->name }}</h6>
                          <small class="text-muted">
                            {{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}
                          </small>
                        </div>
                        {!! $comment->comment !!}
                      </div>
                    </div>

                  @endforeach
                  @endif
                  </div>

                  <div class="post-comments mt-5">
                    <div class="mb-4">
                      <h2>Leave A Comment</h2>
                    </div>
                    <form  class="row" method="post" action="{{ route('blogCommentStore', $blog->id) }}" data-toggle="validator">
                    @csrf()  
                    <div class="messages"></div>
                      <div class="form-group col-sm-6">
                        <input id="form_name" type="text" name="name" class="form-control" placeholder="Name" required
                          data-error="Name is required.">
                        <div class="help-block with-errors"></div>
                      </div>
                      <div class="form-group col-sm-6">
                        <input id="form_email" type="email" name="email" class="form-control" placeholder="Email"
                          required data-error="Valid email is required.">
                        <div class="help-block with-errors"></div>
                      </div>
                      <div class="form-group mb-0 col-sm-12">
                        <textarea id="form_message" name="comment" class="form-control h-auto"
                          placeholder="Your Comment" rows="4" required
                          data-error="Please, leave us a message."></textarea>
                        <div class="help-block with-errors"></div>
                      </div>
                      <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary mt-5">Post Comment</button>
                      </div>
                    </form>
                  </div>
                </div>

                <!-- End Blog Card -->
              </div>
            </div>
          </div>
      </section>


@endsection