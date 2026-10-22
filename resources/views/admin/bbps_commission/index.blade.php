@extends('admin.layouts.layout')

@section('title', 'BBPS Commission Setup')

@section('css')
<style>
    .bbps-filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        padding: 12px 18px;
    }
    .bbps-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .bbps-table-container {
        max-height: 540px;
        overflow-y: auto;
    }
    .bbps-table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .bbps-table thead th {
        position: sticky;
        top: 0;
        background: #f8fafc !important;
        color: #475569;
        font-weight: 700;
        font-size: 0.78rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        border-top: none;
        padding: 10px 14px;
        z-index: 5;
        white-space: nowrap;
    }
    .bbps-table tbody td {
        padding: 8px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
    }
    .bbps-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .bbps-table tbody tr.row-changed {
        background-color: #fef9c3 !important;
    }
    .bbps-table tbody tr.row-saved {
        animation: highlightSave 1.2s ease-out;
    }
    @keyframes highlightSave {
        0% { background-color: #dcfce7; }
        100% { background-color: transparent; }
    }
    .commission-input, .gst-input {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-weight: 500;
    }
    .commission-input:focus, .gst-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- Top Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold">
                <i class="fa-solid fa-percent text-primary me-2"></i>BBPS Commission Setup
            </h4>
            <p class="text-muted small mb-0">
                Company-wise commission, GST, surcharge, and flat rate configuration.
            </p>
        </div>
        <div class="mt-2 mt-sm-0">
            <a href="{{ route('company.details.list') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fa-solid fa-building me-1"></i> Companies List
            </a>
            <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold" id="btnTopSaveAll" onclick="saveAllRows()" style="{{ !$selectedCompany ? 'display: none;' : '' }}">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save All Changes
            </button>
        </div>
    </div>

    <!-- Single Row Filter Bar (Both Filters in ONE Row) -->
    <div class="bbps-filter-card mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

            <!-- Filter 1: Company Selector -->
            <div class="d-flex align-items-center flex-grow-1" style="min-width: 280px; max-width: 440px;">
                <label for="companySelect" class="form-label mb-0 me-2 fw-bold text-secondary text-nowrap small">
                    <i class="fa-solid fa-building text-primary me-1"></i> COMPANY:
                </label>
                <div class="flex-grow-1">
                    <select name="company_id" id="companySelect" class="form-select form-select-sm" onchange="onCompanyChange(this.value)">
                        <option value="">Select Company</option>
                        @foreach ($companies as $comp)
                            <option value="{{ $comp->id }}" {{ ($selectedCompany && $selectedCompany->id == $comp->id) ? 'selected' : '' }}>
                                {{ $comp->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filter 2: Bill Type Filter -->
            <div class="d-flex align-items-center flex-grow-1" style="min-width: 280px; max-width: 440px;">
                <label for="brandTextInput" class="form-label mb-0 me-2 fw-bold text-secondary text-nowrap small">
                    <i class="fa-solid fa-filter text-primary me-1"></i> BILL TYPE:
                </label>
                <div class="d-flex align-items-center flex-grow-1">
                    <input type="text" 
                           id="brandTextInput" 
                           class="form-control form-control-sm flex-grow-1" 
                           placeholder="Filter bill type..." 
                           value="{{ $search }}"
                           autocomplete="off"
                           style="height: 31px;"
                           oninput="filterTableRows(this.value)"
                           onchange="filterTableRows(this.value)"
                           onkeyup="filterTableRows(this.value)"
                           {{ !$selectedCompany ? 'disabled' : '' }}>
                    <button class="btn btn-outline-secondary btn-sm ms-2 px-2.5" 
                            type="button" 
                            id="btnClearSearch" 
                            onclick="resetFilter()" 
                            title="Clear"
                            style="height: 31px; min-width: 32px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-primary btn-sm px-3 fw-bold ms-2 text-nowrap" 
                            id="btnFilterAll" 
                            onclick="resetFilter()" 
                            style="height: 31px;"
                            {{ !$selectedCompany ? 'disabled' : '' }}>
                        ALL
                    </button>
                </div>
            </div>

            <!-- Actions: Count & Save All -->
            <div class="d-flex align-items-center gap-2 ms-auto" id="topActions" style="{{ !$selectedCompany ? 'display: none !important;' : '' }}">
                <span class="badge bg-light text-secondary border py-2 px-2.5 small fw-semibold text-nowrap">
                    <span id="visibleCount">{{ count($categories) }}</span> Bill Types
                </span>
                <button type="button" class="btn btn-success btn-sm px-3 fw-bold text-nowrap" onclick="saveAllRows()">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save All
                </button>
            </div>

        </div>
    </div>

    <!-- Table Card Container (Always in DOM, Updated via AJAX) -->
    <div class="bbps-table-card mb-4" id="tableCard">
        <!-- Scrollable Table -->
        <div class="bbps-table-container">
            <table class="table table-hover bbps-table align-middle" id="bbpsCommissionTable">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th style="min-width: 220px;">BILL TYPE</th>
                        <th class="text-center" style="width: 140px;">COMMISSION(%)</th>
                        <th class="text-center" style="width: 110px;">GST(%)</th>
                        <th class="text-center" style="width: 120px;">IS SURCHARGE</th>
                        <th class="text-center" style="width: 110px;">IS FLAT</th>
                        <th class="text-center" style="width: 110px;">SAVE</th>
                    </tr>
                </thead>
                <tbody id="commissionTableBody">
                    @if($selectedCompany)
                        @include('admin.bbps_commission.table_rows', [
                            'categories' => $categories,
                            'commissions' => $commissions,
                            'companyId' => $selectedCompany->id
                        ])
                    @else
                        <tr id="noCompanyRow">
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fa-solid fa-building text-secondary mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                                <div class="fw-bold">Please select a company to view and configure BBPS commissions.</div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Bottom Lightweight Footer -->
        <div class="d-flex flex-wrap justify-content-between align-items-center p-3 bg-light border-top" id="tableFooter" style="{{ !$selectedCompany ? 'display: none !important;' : '' }}">
            <div class="text-muted small">
                <i class="fa-solid fa-circle-info text-primary me-1"></i>
                Tip: You can edit values and click <strong>SAVE</strong> on each row or click <strong>Save All Changes</strong>.
            </div>
            <div>
                <button type="button" class="btn btn-success btn-sm px-4 fw-semibold" onclick="saveAllRows()">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save All Changes
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    const SAVE_SINGLE_URL = "{{ route('admin.bbps.commission.save') }}";
    const SAVE_ALL_URL = "{{ route('admin.bbps.commission.saveAll') }}";
    const GET_TABLE_URL = "{{ route('admin.bbps.commission.getTable') }}";
    let CURRENT_COMPANY_ID = "{{ $selectedCompany->id ?? '' }}";

    // 100% AJAX Company change handler - ZERO page reload
    function onCompanyChange(companyId) {
        if (!companyId) {
            CURRENT_COMPANY_ID = '';
            $('#commissionTableBody').html(`
                <tr id="noCompanyRow">
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fa-solid fa-building text-secondary mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                        <div class="fw-bold">Please select a company to view and configure BBPS commissions.</div>
                    </td>
                </tr>
            `);
            $('#brandTextInput').prop('disabled', true).val('');
            $('#btnFilterAll').prop('disabled', true);
            $('#topActions, #tableFooter, #btnTopSaveAll').attr('style', 'display: none !important;');
            return;
        }

        CURRENT_COMPANY_ID = companyId;

        // Update URL quietly in browser address bar without page reload
        try {
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set('company_id', companyId);
            newUrl.searchParams.delete('brandtext');
            window.history.pushState({ path: newUrl.toString() }, '', newUrl.toString());
        } catch (e) {
            console.log('History update error:', e);
        }

        // Show smooth loading spinner in table body
        $('#commissionTableBody').html(`
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    <span class="fw-semibold">Loading commission configuration...</span>
                </td>
            </tr>
        `);

        // AJAX request to fetch company's commissions
        $.ajax({
            url: GET_TABLE_URL,
            type: 'GET',
            data: {
                company_id: companyId
            },
            success: function (res) {
                if (res.status) {
                    $('#commissionTableBody').html(res.html);
                    $('#visibleCount').text(res.count);
                    $('#brandTextInput').prop('disabled', false).val('');
                    $('#btnFilterAll').prop('disabled', false);
                    $('#topActions, #tableFooter, #btnTopSaveAll').show().removeAttr('style');
                } else {
                    $('#commissionTableBody').html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger py-4">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Failed to load commissions.
                            </td>
                        </tr>
                    `);
                }
            },
            error: function (xhr) {
                let msg = 'Failed to load commissions for selected company.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $('#commissionTableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center text-danger py-4">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> ${msg}
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Filter table rows by text on change / input / keyup - 100% Client-side instant filter (zero reload)
    function filterTableRows(query) {
        if (typeof query !== 'string') {
            query = $('#brandTextInput').val() || '';
        }
        const q = query.toLowerCase().trim();
        let visibleCount = 0;
        $('#commissionTableBody tr.commission-row').each(function () {
            const categoryName = $(this).data('category-name') ? $(this).data('category-name').toString().toLowerCase() : '';
            if (q === '' || categoryName.indexOf(q) !== -1) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        $('#visibleCount').text(visibleCount);
    }

    // Reset filter ("ALL" button) - zero reload
    function resetFilter() {
        $('#brandTextInput').val('');
        filterTableRows('');
    }

    $(document).ready(function () {
        // Initialize TomSelect with explicit onChange callback
        const companySelectEl = document.getElementById('companySelect');
        if (typeof TomSelect !== 'undefined' && companySelectEl) {
            try {
                if (companySelectEl.tomselect) {
                    companySelectEl.tomselect.destroy();
                }
                new TomSelect('#companySelect', {
                    create: false,
                    maxItems: 1,
                    allowEmptyOption: true,
                    placeholder: 'Select Company',
                    sortField: { field: "text", direction: "asc" },
                    onChange: function (val) {
                        onCompanyChange(val);
                    }
                });
            } catch (err) {
                console.warn('TomSelect init skipped:', err);
            }
        }

        // On Company selection change (native fallback)
        $('#companySelect').on('change', function () {
            onCompanyChange($(this).val());
        });

        // Filter events on BILL TYPE input
        $('#brandTextInput').on('input change keyup paste search', function () {
            filterTableRows($(this).val());
        });

        // Track user modifications on inputs to highlight row
        $(document).on('input change', '.commission-input, .gst-input, .surcharge-checkbox, .flat-checkbox', function () {
            $(this).closest('tr').addClass('row-changed');
        });
    });

    // Save a single row via AJAX - zero reload
    function saveSingleRow(companyId, billtypeid) {
        const comInput = $(`#com_${companyId}_${billtypeid}`);
        const gstInput = $(`#gst_${companyId}_${billtypeid}`);
        const issurchCb = $(`#issurch_${companyId}_${billtypeid}`);
        const isflatCb = $(`#isflat_${companyId}_${billtypeid}`);
        const btn = $(`#but_${companyId}_${billtypeid}`);
        const row = btn.closest('tr');

        const comper = comInput.val();
        const gstper = gstInput.val();
        const issurcharge = issurchCb.is(':checked') ? 1 : 0;
        const isflat = isflatCb.is(':checked') ? 1 : 0;

        const originalBtnHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

        $.ajax({
            url: SAVE_SINGLE_URL,
            type: 'POST',
            data: {
                _token: CSRF_TOKEN,
                company_id: companyId,
                billtypeid: billtypeid,
                comper: comper,
                gstper: gstper,
                issurcharge: issurcharge,
                isflat: isflat
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-check me-1"></i> SAVED');
                btn.removeClass('btn-success').addClass('btn-outline-success');
                row.removeClass('row-changed').addClass('row-saved');

                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: res.message || 'Saved successfully'
                    });
                }

                setTimeout(function () {
                    btn.removeClass('btn-outline-success').addClass('btn-success').html(originalBtnHtml);
                    row.removeClass('row-saved');
                }, 2000);
            },
            error: function (xhr) {
                btn.prop('disabled', false).html(originalBtnHtml);
                let errMsg = 'Failed to save commission. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errMsg
                    });
                } else {
                    alert(errMsg);
                }
            }
        });
    }

    // Save all rows at once via AJAX - zero reload
    function saveAllRows() {
        if (!CURRENT_COMPANY_ID) {
            Swal.fire('Warning', 'Please select a company first.', 'warning');
            return;
        }

        const rowsData = [];
        $('#commissionTableBody tr.commission-row').each(function () {
            const billtypeid = $(this).data('billtypeid');
            const comper = $(this).find('.commission-input').val();
            const gstper = $(this).find('.gst-input').val();
            const issurcharge = $(this).find('.surcharge-checkbox').is(':checked') ? 1 : 0;
            const isflat = $(this).find('.flat-checkbox').is(':checked') ? 1 : 0;

            rowsData.push({
                billtypeid: billtypeid,
                comper: comper,
                gstper: gstper,
                issurcharge: issurcharge,
                isflat: isflat
            });
        });

        if (rowsData.length === 0) {
            Swal.fire('Info', 'No bill types found to save.', 'info');
            return;
        }

        const btnSaveAll = $('#btnTopSaveAll');
        const origText = btnSaveAll.html();
        btnSaveAll.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving All...');

        $.ajax({
            url: SAVE_ALL_URL,
            type: 'POST',
            data: {
                _token: CSRF_TOKEN,
                company_id: CURRENT_COMPANY_ID,
                rows: rowsData
            },
            success: function (res) {
                btnSaveAll.prop('disabled', false).html(origText);
                $('.commission-row').removeClass('row-changed').addClass('row-saved');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: res.message || 'All commissions updated successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }

                setTimeout(function () {
                    $('.commission-row').removeClass('row-saved');
                }, 2000);
            },
            error: function (xhr) {
                btnSaveAll.prop('disabled', false).html(origText);
                let errMsg = 'Failed to save commissions.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errMsg, 'error');
            }
        });
    }
</script>
@endsection
