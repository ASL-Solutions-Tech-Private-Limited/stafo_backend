@extends('admin.layouts.layout')

@section('title', 'Notification')
@section('content')

    <div class="container">
        <h3 class="mb-3">Send Notification</h3>
        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ route('notification.send') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
                <label for="notification">Company</label>
                <span id="selectAllBtn">✅ Select All</span>
                <span id="deselectAllBtn" style="display:none;">❌ Deselect All</span>

                <select name="company_id[]" id="company_id" multiple required>  
                                
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                    @endforeach
                </select> 
            </div>
            <div class="form-group mb-3">
                <label for="notification">Notification</label>
                <input type="text" name="notification" id="notification" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="image">Image</label>(Max Size 2MB)
                <input type="file" name="image" id="image" class="form-control">
                
            </div>
            
            <button type="submit" class="btn btn-primary mt-3">Send</button>
        </form>
    </div>
 
@endsection
@section('scripts')
<script>
    const select = new TomSelect('#company_id', {
      plugins: ['remove_button'],
      closeAfterSelect: false,
      onItemAdd(value) {
        if (value === '__select_all__') {
          this.removeItem('__select_all__', true);

          const allValues = Object.keys(this.options).filter(v => v !== '__select_all__');
          this.setValue(allValues);
        }
      },
      render: {
        option(data, escape) {
          if (data.value === '__select_all__') {
            return `<div style="font-weight:bold; color:#007bff;">${escape(data.text)}</div>`;
          }
          return `<div>${escape(data.text)}</div>`;
        }
      }
    });

    // Select All button
    document.getElementById('selectAllBtn').addEventListener('click', () => {
      const values = Object.keys(select.options).filter(v => v !== '__select_all__');
      select.setValue(values);
      $('#deselectAllBtn').show();
      $('#selectAllBtn').hide();
    });

    // Deselect All button
    document.getElementById('deselectAllBtn').addEventListener('click', () => {
      select.clear(); // Clears all selections
      $('#deselectAllBtn').hide();
      $('#selectAllBtn').show();
    });
  </script>
  @endsection