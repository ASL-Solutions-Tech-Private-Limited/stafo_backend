@forelse ($categories as $index => $category)
    @php
        $comm = $commissions->get($category->id);
        $comper = $comm && $comm->comper !== null ? $comm->comper : '';
        $gstper = $comm && $comm->gstper !== null ? $comm->gstper : '';
        $issurcharge = $comm && $comm->issurcharge == '1';
        $isflat = $comm && $comm->isflat == '1';
    @endphp
    <tr class="commission-row" data-billtypeid="{{ $category->id }}" data-category-name="{{ $category->name }}">
        <td class="text-center align-middle text-muted small" style="width: 50px;">
            {{ $index + 1 }}
        </td>
        <td class="align-middle fw-medium text-dark" style="min-width: 220px;">
            {{ $category->name }}
        </td>
        <td class="text-center align-middle" style="width: 140px;">
            <input type="number" 
                   step="0.01" 
                   min="0" 
                   max="100" 
                   class="form-control form-control-sm text-center commission-input" 
                   id="com_{{ $companyId }}_{{ $category->id }}" 
                   name="comper" 
                   placeholder="0" 
                   value="{{ $comper !== '' ? $comper : '' }}">
        </td>
        <td class="text-center align-middle" style="width: 110px;">
            <input type="number" 
                   step="0.01" 
                   min="0" 
                   max="100" 
                   class="form-control form-control-sm text-center gst-input" 
                   id="gst_{{ $companyId }}_{{ $category->id }}" 
                   name="gstper" 
                   placeholder="0" 
                   value="{{ $gstper !== '' ? $gstper : '' }}">
        </td>
        <td class="text-center align-middle" style="width: 120px;">
            <div class="form-check d-inline-block m-0">
                <input class="form-check-input surcharge-checkbox" 
                       type="checkbox" 
                       id="issurch_{{ $companyId }}_{{ $category->id }}" 
                       name="issurcharge" 
                       value="1" 
                       style="cursor: pointer; width: 1.15rem; height: 1.15rem;"
                       {{ $issurcharge ? 'checked' : '' }}>
            </div>
        </td>
        <td class="text-center align-middle" style="width: 110px;">
            <div class="form-check d-inline-block m-0">
                <input class="form-check-input flat-checkbox" 
                       type="checkbox" 
                       id="isflat_{{ $companyId }}_{{ $category->id }}" 
                       name="isflat" 
                       value="1" 
                       style="cursor: pointer; width: 1.15rem; height: 1.15rem;"
                       {{ $isflat ? 'checked' : '' }}>
            </div>
        </td>
        <td class="text-center align-middle" style="width: 110px;">
            <button type="button" 
                    class="btn btn-sm btn-success px-3 py-1 btn-save-row fw-semibold" 
                    id="but_{{ $companyId }}_{{ $category->id }}" 
                    data-company-id="{{ $companyId }}" 
                    data-billtypeid="{{ $category->id }}"
                    onclick="saveSingleRow({{ $companyId }}, {{ $category->id }})">
                <i class="fa-solid fa-floppy-disk me-1"></i> SAVE
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">
            <i class="fa-solid fa-circle-exclamation me-1"></i> No BBPS bill types found matching the criteria.
        </td>
    </tr>
@endforelse
