@extends('admin.layouts.layout')
@section('title', 'Add CMS Page')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access align-items-center mb-3">
                <div class="user-welcome">
                    <h3 class="mb-1">Add CMS Page</h3>
                    <p class="text-muted mb-0">Create new page or import raw HTML directly.</p>
                </div>
                <a href="{{ route('cms.list') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <form action="{{ route('cms.store') }}" method="post" enctype="multipart/form-data" id="cmsForm">
                @csrf
                <div class="new-user-form card p-4 shadow-sm border-0">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label fw-semibold">Title<span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter Title" required>
                            <span class="error error-title"></span>
                        </div>

                        <!-- Short Description -->
                        <div class="col-md-6 mb-3">
                            <label for="short_description" class="form-label fw-semibold">Short Description</label>
                            <textarea name="short_description" id="short_description" class="form-control"
                                placeholder="Enter Short Description" rows="1"></textarea>
                            <span class="error error-short-description"></span>
                        </div>

                        <!-- Meta Title -->
                        <div class="col-md-6 mb-3">
                            <label for="meta_title" class="form-label fw-semibold">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control"
                                placeholder="Enter Meta Title">
                            <span class="error error-meta-title"></span>
                        </div>

                        <!-- Meta Description -->
                        <div class="col-md-6 mb-3">
                            <label for="meta_description" class="form-label fw-semibold">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" class="form-control"
                                placeholder="Enter Meta Description" rows="1"></textarea>
                            <span class="error error-meta-description"></span>
                        </div>

                        <!-- Long Description (Rich & HTML Editor) -->
                        <div class="col-md-12 mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2 bg-light p-2 rounded border">
                                <div>
                                    <label for="long_description" class="form-label fw-bold mb-0">
                                        <i class="fa-solid fa-file-lines text-primary me-1"></i> Content (HTML / Rich Text)
                                    </label>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Hidden File Input for Direct HTML Upload -->
                                    <input type="file" id="htmlFileInput" accept=".html,.htm,.txt" style="display: none;">
                                    
                                    <button type="button" class="btn btn-sm btn-primary text-white" id="btnUploadHtml">
                                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Direct HTML Upload (.html)
                                    </button>

                                    <button type="button" class="btn btn-sm btn-outline-dark" id="btnToggleMode">
                                        <i class="fa-solid fa-code me-1"></i> <span id="toggleModeText">Switch to Raw HTML Mode</span>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearContent" title="Clear Content">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="alert alert-info py-2 px-3 mb-2 small d-flex align-items-center justify-content-between">
                                <span>
                                    <i class="fa-solid fa-circle-info me-1"></i> 
                                    <strong>HTML Tip:</strong> Aap `.html` file direct upload kar sakte hain, toolbar me <code>&lt;&gt;</code> button se HTML paste kar sakte hain, ya "Switch to Raw HTML Mode" click karke raw code paste kar sakte hain.
                                </span>
                            </div>

                            <!-- Visual TinyMCE Container -->
                            <div id="visualEditorContainer">
                                <textarea name="long_description" id="long_description" class="form-control"
                                    rows="12"></textarea>
                            </div>

                            <!-- Raw HTML Code Mode Container (Initially Hidden) -->
                            <div id="rawHtmlContainer" style="display: none;">
                                <textarea id="rawHtmlEditor" class="form-control font-monospace" rows="18"
                                    style="background-color: #1e1e2f; color: #f8f9fa; font-family: 'Consolas', 'Courier New', monospace; font-size: 13px; line-height: 1.5;"
                                    placeholder="<!-- Paste or type your direct HTML code here -->"></textarea>
                            </div>

                            <span class="error error-long-description"></span>
                        </div>

                        <!-- Image -->
                        <div class="col-md-12 mb-4">
                            <label for="image" class="form-label fw-semibold">Featured Image</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <span class="error error-image"></span>
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('cms.list') }}" class="btn btn-outline-danger">Cancel</a>
                            <button class="btn btn-primary text-white px-4 submit" type="submit">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save CMS
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
@endsection

@section('scripts')
    <!-- TinyMCE CDN with full plugins (Code, HTML, Preview, Fullscreen, etc.) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>

    <script>
        $(document).ready(function() {
            let isRawMode = false;

            // Initialize TinyMCE
            tinymce.init({
                selector: '#long_description',
                height: 500,
                menubar: true,
                plugins: [
                    'code', 'fullscreen', 'preview', 'visualblocks', 'visualchars',
                    'table', 'lists', 'link', 'image', 'charmap', 'anchor',
                    'searchreplace', 'wordcount', 'autolink', 'help'
                ],
                toolbar: 'code fullscreen preview | undo redo | blocks fontfamily fontsize | ' +
                         'bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | ' +
                         'bullist numlist | link image table | removeformat',
                content_style: 'body { font-family: "Plus Jakarta Sans", sans-serif; font-size: 14px; line-height: 1.6; color: #333; }',
                extended_valid_elements: '*[*]',
                valid_elements: '*[*]',
                valid_children: '+body[style|link|script]',
                allow_html_in_named_anchor: true,
                cleanup: false,
                verify_html: false,
                convert_urls: false,
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save();
                    });
                }
            });

            // HTML File Upload Trigger
            $('#btnUploadHtml').on('click', function() {
                $('#htmlFileInput').click();
            });

            // Handle HTML File Selection
            $('#htmlFileInput').on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(evt) {
                    const htmlContent = evt.target.result;

                    if (tinymce.get('long_description')) {
                        tinymce.get('long_description').setContent(htmlContent);
                    }
                    $('#long_description').val(htmlContent);
                    $('#rawHtmlEditor').val(htmlContent);

                    Swal.fire({
                        icon: 'success',
                        title: 'HTML File Imported!',
                        text: 'File "' + file.name + '" has been successfully loaded into the editor.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                };
                reader.readAsText(file);
                $(this).val('');
            });

            // Mode Toggle (Visual <-> Raw HTML)
            $('#btnToggleMode').on('click', function() {
                isRawMode = !isRawMode;

                if (isRawMode) {
                    let currentContent = '';
                    if (tinymce.get('long_description')) {
                        currentContent = tinymce.get('long_description').getContent();
                    } else {
                        currentContent = $('#long_description').val();
                    }
                    $('#rawHtmlEditor').val(currentContent);

                    $('#visualEditorContainer').hide();
                    $('#rawHtmlContainer').show();
                    $('#toggleModeText').text('Switch to Visual Editor');
                    $(this).removeClass('btn-outline-dark').addClass('btn-dark text-white');
                } else {
                    const rawContent = $('#rawHtmlEditor').val();
                    if (tinymce.get('long_description')) {
                        tinymce.get('long_description').setContent(rawContent);
                    }
                    $('#long_description').val(rawContent);

                    $('#rawHtmlContainer').hide();
                    $('#visualEditorContainer').show();
                    $('#toggleModeText').text('Switch to Raw HTML Mode');
                    $(this).removeClass('btn-dark text-white').addClass('btn-outline-dark');
                }
            });

            // Clear content button
            $('#btnClearContent').on('click', function() {
                Swal.fire({
                    title: 'Clear all content?',
                    text: "This will empty the editor.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, clear it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (tinymce.get('long_description')) {
                            tinymce.get('long_description').setContent('');
                        }
                        $('#long_description').val('');
                        $('#rawHtmlEditor').val('');
                    }
                });
            });

            // Form Submit Sync
            $('#cmsForm').on('submit', function() {
                if (isRawMode) {
                    const rawContent = $('#rawHtmlEditor').val();
                    $('#long_description').val(rawContent);
                } else {
                    if (tinymce.get('long_description')) {
                        tinymce.get('long_description').save();
                    }
                }
            });
        });
    </script>
@endsection