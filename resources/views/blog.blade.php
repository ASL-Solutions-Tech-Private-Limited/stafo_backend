@extends('frontend.layouts.master')
@section('title', 'Blogs | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'Blogs')
@section('content') 

<div class="container">
        <div class="row">
          <div class="col-12 col-lg-7 mb-6 mb-lg-0">
            <!-- Blog Card -->
             @if($blogs->count()>0)
              @foreach($blogs as $blog)
            <div class="card border-0 shadow bg-transparent">
              @if($blog->image == null)
                <img class="card-img-top" src="{{ asset('img/no_image.jpg') }}" height="300" alt="{{ $blog->title }}">
              @else
              <img class="card-img-top" src="{{ asset('uploads/blog/'.$blog->image) }}" alt="{{ $blog->title }}">
              @endif
              <div class="card-body p-4">
                <div>
                  <div class="d-inline-block bg-light text-center px-2 py-1 rounded me-2"><span
                      class="text-primary">{{ date('d',strtotime($blog->date)) }}</span>
                    {{ date('M',strtotime($blog->date)) }}</div> <a class="d-inline-block btn-link" href="#">{{ $blog->category->name}}</a>
                </div>
                <h2 class="h5 my-3">
                  <a class="link-title" href="{{route('blogDetails',[$blog->id,$blog->slug])}}">{{ $blog->title }}</a>
                </h2>
                <p>{{ $blog->short_description }}</p>
                <ul class="list-inline mb-0">
                  <li class="list-inline-item pe-3"> <a href="#" class="list-group-item-action"><i
                        class="lar la-user-circle me-1 text-primary ic-1x"></i> {{ $blog->auther }}</a>
                  </li>
                  <li class="list-inline-item pe-3"> <a href="#" class="list-group-item-action"><i
                        class="las la-eye me-1 text-primary ic-1x"></i> {{ $blog->views}}</a>
                  </li>
                  <!-- <li class="list-inline-item"> <a href="#" class="list-group-item-action"><i
                        class="lar la-comments me-1 text-primary ic-1x"></i> 125</a>
                  </li> -->
                </ul>
              </div>
            </div>
            <hr class="my-5">
            @endforeach
            @else
              <div class="alert alert-info" role="alert">
                No blogs available at the moment.
              </div>
            @endif

            <nav aria-label="Page navigation" class="mt-6">
               {{ $blogs->links('pagination::bootstrap-4') }}
            </nav>
          </div>
          <div class="col-12 col-lg-4 ms-auto">
            <form class="my-2 my-lg-0 row g-0">
              <input class="form-control me-sm-2 col" type="search" placeholder="Search" aria-label="Search">
              <button class="btn btn-primary my-2 my-sm-0 col-auto" type="submit">Search Blog</button>
            </form>
            <div class="mt-5 mb-5 p-4 rounded" data-bg-color="#d0faec">
              <h4 class="mb-3">Recent Stories</h4>
              @if($recentblogs->count()>0)
                @foreach($recentblogs as $recentblog)
                  <article>
                    <div class="row align-items-center">
                      <div class="col-sm-4">
                        @if($recentblog->image == null)
                          <img class="card-img-top" src="{{ asset('img/no-image.png') }}" alt="{{ $recentblog->title }}">
                        @else
                        <img src="{{ asset('uploads/blog/'.$recentblog->image) }}" class="rounded img-fluid shadow"
                          alt="{{ $recentblog->title }}">
                        @endif
                      </div>
                      <div class="col-sm-8">
                        <h5 class="h6">
                          <a class="link-title" href="{{route('blogDetails',[$recentblog->id,$recentblog->slug])}}">{{ $recentblog->title }}</a>
                        </h5> 
                        <a class="d-inline-block text-muted" href="#">{{ $recentblog->created_at->format('d M Y') }}</a>
                      </div>
                    </div>
                  </article>
                @endforeach
              @else
                <p>No recent blogs available.</p>
              @endif
              
            </div>
            <div class="mb-5 p-4 rounded" data-bg-color="#ffeff8">
              <h4 class="mb-3">Categories</h4>
              <ul class="list-unstyled list-group list-group-flush">
                @if($categories->count()>0)
                  @foreach($categories as $category)
                  <li class="mb-3"> <a class="list-group-item list-group-item-action border-0" href="#">
                      {{ $category->name }}
                      <span class="badge bg-primary font-weight-normal p-2 rounded float-end">{{ $category->blogs->count() }}</span>
                    </a>
                  </li>
                  @endforeach
                @endif
              </ul>
            </div>
            <div class="p-4 rounded" data-bg-color="#d3f6fe">
              <h4 class="mb-3">Tags</h4>
              <div>
                @if($tags->count()>0)
                  @foreach($tags as $tag)
                    <a class="btn-link rounded d-inline-block p-2 bg-white m-1" href="#">{{ $tag->name }}</a>
                  @endforeach
                @endif
                
              </div>
            </div>
          </div>
        </div>
      </div>
      
@endsection