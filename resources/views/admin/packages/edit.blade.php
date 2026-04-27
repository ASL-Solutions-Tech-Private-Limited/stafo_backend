@extends('admin.layouts.layout')
@section('title', 'Edit Package')

@section('content')
    <div class="container">
        <h3 class="mb-3">Edit Package</h3>
        <form action="{{ route('packages.update', $package->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Package Name</label>
                <input type="text" name="package_name" class="form-control" value="{{ $package->package_name }}" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" id="description" class="form-control">{{ $package->description }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <label>Monthly Price</label>
                    <input type="number" name="monthly_price" class="form-control" value="{{ $package->monthly_price }}" required>
                </div>
                <div class="col-md-3">
                    <label>Quarterly Price</label>
                    <input type="number" name="quarterly_price" class="form-control" value="{{ $package->quarterly_price }}" required>
                </div>
                <div class="col-md-3">
                    <label>Half Yearly Price</label>
                    <input type="number" name="halfyearly_price" class="form-control" value="{{ $package->halfyearly_price }}" required>
                </div>
                <div class="col-md-3">
                    <label>Yearly Price</label>
                    <input type="number" name="yearly_price" class="form-control" value="{{ $package->yearly_price }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label>Monthly Discount Price</label>
                    <input type="number" name="monthly_discount_price" class="form-control" value="{{ $package->monthly_discount_price }}" required>
                </div>
                <div class="col-md-3">
                    <label>Quarterly Discount Price</label>
                    <input type="number" name="quarterly_discount_price" class="form-control" value="{{ $package->quarterly_discount_price }}" required>
                </div>
                <div class="col-md-3">
                    <label>Half Yearly Discount Price</label>
                    <input type="number" name="halfyearly_discount_price" class="form-control" value="{{ $package->halfyearly_discount_price }}" required>
                </div>
                <div class="col-md-3">
                    <label>Yearly Discount Price</label>
                    <input type="number" name="yearly_discount_price" class="form-control" value="{{ $package->yearly_discount_price }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Days</label>
                <input type="number" name="days" class="form-control" value="{{ $package->days }}">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="active" {{ $package->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $package->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Features selection -->
            <div class="mb-3">
                <label class="mb-3">Features</label>
                <div class="form-check ">
                    @foreach ($features as $feature)
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="features[{{ $feature->id }}]"
                                id="feature-{{ $feature->id }}" value="{{ $feature->id }}" {{ in_array($feature->id, $package->features->pluck('id')->toArray()) ? 'checked' : '' }}>
                            <label class="form-check-label" for="feature-{{ $feature->id }}">
                                {{ $feature->name }}
                            </label>

                            <!-- Feature Value -->
                            <input type="text" name="feature_value[{{ $feature->id }}]" class="form-control mt-2"
                                placeholder="Enter value for {{ $feature->name }}"
                                value="{{ optional($package->features->where('id', $feature->id)->first())->pivot->feature_value ?? '' }}">
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('packages.index') }}" class="btn btn-warning">Back</a>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection