<!-- Filter Header matching Image 1 -->
<div class="row align-items-center mb-3">
    <div class="col-auto">
        <label for="modalBrandText" class="fw-bold text-secondary text-uppercase small m-0">BILL TYPE</label>
    </div>
    <div class="col-md-5 col-7">
        <input type="text" id="modalBrandText" class="form-control form-control-sm" placeholder="Search bill type...">
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" id="btnModalFilterAll" onclick="resetModalFilter()">ALL</button>
    </div>
    <div class="col text-end">
        <button type="button" class="btn btn-success btn-sm" onclick="saveAllModalRows({{ $company->id }})">
            <i class="fa-solid fa-floppy-disk me-1"></i> Save All
        </button>
    </div>
</div>

<!-- Scrollable Table matching Image 1 -->
<div class="box-content" style="padding:0px; margin:0px; max-height:380px; overflow-y:auto; border: 1px solid #dee2e6; border-radius: 6px;">
    <table class="table table-bordered table-hover m-0 align-middle" id="modalCommissionTable">
        <thead>
            <tr style="background:#c7c7c7; position: sticky; top: 0; z-index: 5;">
                <th style="text-align:center; width:40px;">#</th>
                <th style="min-width:220px; white-space:nowrap;">BILL TYPE</th>
                <th style="width:140px; text-align:center; white-space:nowrap;">COMMISSION(%)</th>
                <th style="width:100px; text-align:center; white-space:nowrap;">GST(%)</th>
                <th style="width:130px; text-align:center; white-space:nowrap;">IS SURCHARGE</th>
                <th style="width:100px; text-align:center; white-space:nowrap;">IS FLAT</th>
                <th style="text-align:center; white-space:nowrap; width:90px;">SAVE</th>
            </tr>
        </thead>
        <tbody id="modalCommissionTableBody">
            @foreach ($categories as $index => $category)
                @php
                    $comm = $commissions->get($category->id);
                    $comper = $comm ? $comm->comper : '';
                    $gstper = $comm ? $comm->gstper : '';
                    $issurcharge = $comm && $comm->issurcharge == '1';
                    $isflat = $comm && $comm->isflat == '1';
                @endphp
                <tr class="modal-commission-row" data-category-name="{{ strtolower($category->name) }}" data-billtypeid="{{ $category->id }}">
                    <td style="text-align:center;">{{ $index + 1 }}</td>
                    <td style="text-align:left;">
                        <strong>{{ $category->name }}</strong>
                    </td>
                    <td>
                        <input value="{{ $comper !== '' ? $comper : '' }}" 
                               maxlength="7" 
                               style="width:120px; text-align:center;" 
                               class="form-control form-control-sm mx-auto modal-comper" 
                               type="number" 
                               step="0.01" 
                               min="0"
                               id="modal_com_{{ $company->id }}_{{ $category->id }}" />
                    </td>
                    <td>
                        <input value="{{ $gstper !== '' ? $gstper : '' }}" 
                               maxlength="7" 
                               style="width:80px; text-align:center;" 
                               class="form-control form-control-sm mx-auto modal-gstper" 
                               type="number" 
                               step="0.01" 
                               min="0"
                               id="modal_gst_{{ $company->id }}_{{ $category->id }}" />
                    </td>
                    <td style="text-align:center;">
                        <input type="checkbox" 
                               class="form-check-input modal-issurch" 
                               id="modal_issurch_{{ $company->id }}_{{ $category->id }}" 
                               {{ $issurcharge ? 'checked' : '' }} />
                    </td>
                    <td style="text-align:center;">
                        <input type="checkbox" 
                               class="form-check-input modal-isflat" 
                               id="modal_isflat_{{ $company->id }}_{{ $category->id }}" 
                               {{ $isflat ? 'checked' : '' }} />
                    </td>
                    <td style="text-align:center;">
                        <button type="button" 
                                class="btn btn-success btn-sm px-2" 
                                id="modal_but_{{ $company->id }}_{{ $category->id }}" 
                                onclick="saveModalRow({{ $company->id }}, {{ $category->id }})">
                            SAVE
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $('#modalBrandText').on('keyup', function () {
        const q = $(this).val().toLowerCase().trim();
        $('#modalCommissionTableBody tr.modal-commission-row').each(function () {
            const name = $(this).data('category-name') || '';
            $(this).toggle(q === '' || name.includes(q));
        });
    });

    function resetModalFilter() {
        $('#modalBrandText').val('').trigger('keyup');
    }

    function saveModalRow(companyId, billtypeid) {
        const comper = $(`#modal_com_${companyId}_${billtypeid}`).val();
        const gstper = $(`#modal_gst_${companyId}_${billtypeid}`).val();
        const issurcharge = $(`#modal_issurch_${companyId}_${billtypeid}`).is(':checked') ? 1 : 0;
        const isflat = $(`#modal_isflat_${companyId}_${billtypeid}`).is(':checked') ? 1 : 0;
        const btn = $(`#modal_but_${companyId}_${billtypeid}`);

        const origHtml = btn.html();
        btn.prop('disabled', true).html('...');

        $.ajax({
            url: "{{ route('admin.bbps.commission.save') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                company_id: companyId,
                billtypeid: billtypeid,
                comper: comper,
                gstper: gstper,
                issurcharge: issurcharge,
                isflat: isflat
            },
            success: function (res) {
                btn.prop('disabled', false).html('✓ SAVED').removeClass('btn-success').addClass('btn-outline-success');
                setTimeout(() => {
                    btn.removeClass('btn-outline-success').addClass('btn-success').html(origHtml);
                }, 1800);
            },
            error: function () {
                btn.prop('disabled', false).html(origHtml);
                alert('Failed to save. Please try again.');
            }
        });
    }

    function saveAllModalRows(companyId) {
        const rows = [];
        $('#modalCommissionTableBody tr.modal-commission-row').each(function () {
            const billtypeid = $(this).data('billtypeid');
            const comper = $(this).find('.modal-comper').val();
            const gstper = $(this).find('.modal-gstper').val();
            const issurcharge = $(this).find('.modal-issurch').is(':checked') ? 1 : 0;
            const isflat = $(this).find('.modal-isflat').is(':checked') ? 1 : 0;

            rows.push({
                billtypeid: billtypeid,
                comper: comper,
                gstper: gstper,
                issurcharge: issurcharge,
                isflat: isflat
            });
        });

        $.ajax({
            url: "{{ route('admin.bbps.commission.saveAll') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                company_id: companyId,
                rows: rows
            },
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: res.message || 'All commissions updated successfully!',
                    timer: 1800,
                    showConfirmButton: false
                });
            },
            error: function () {
                alert('Failed to save all rows.');
            }
        });
    }
</script>
