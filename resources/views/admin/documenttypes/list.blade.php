@extends('admin.layouts.layout')

@section('title', 'Document Type List')
@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('errors'))
        <div class="alert alert-success">
            {{ session('errors') }}
        </div>
    @endif
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <div class="row">
                <div class="col-6">
                    <h3 class="mb-4">Document Type List</h3>
                </div>
                <div class="col-6">
                    <span style="float: right;">
                        <a href="{{ route('document_add') }}" class="btn btn-primary"> Add </a>
                    </span>
                </div>
            </div>
            <div class="table-responsive table-same">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">Document Name</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($documenttype))
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($documenttype as $document)
                                <tr>
                                    <td scope="row"> {{ $i++ }}</td>
                                    <td>{{ $document->document_name }}</td>
                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('document_edit', $document->id) }}" title="Edit"></title><i
                                                    class="fa fa-solid fa-pen"></i></a>
                                            <!-- <a href="{{ route('document_delete', $document->id) }}" title="Delete"></title>
                                                <i class="fa fa-trash"></i></a> -->
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
