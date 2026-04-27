@extends('frontend.layouts.master')
@section('title', $data['title'].' | STAFO - Leading HRMS Solution Provider in India')
@section('heading', $data['title'])
@section('content') 
<section>
        <div class="container">
          {!! $data['long_description'] !!}
        </div>
    
    </section>
@endsection