/**
 * 백오피스 CKEditor 5 초기화 (범용)
 *
 * 사용법:
 * - textarea에 data-backoffice-ckeditor 속성 추가
 * - (선택) data-upload-url, data-upload-field 로 업로드 엔드포인트·필드명 지정
 * - 서브디렉터리 배포 시: window.BACKOFFICE_CKEDITOR_UPLOAD_URL 설정
 * - 동적 추가 후: window.initBackofficeCKEditors(컨테이너 요소 또는 document)
 * - 폼 제출 전: window.syncBackofficeCKEditorFields(폼 요소) 로 textarea 값 반영
 */
(function () {
    const FALLBACK_UPLOAD_URL = '/backoffice/upload-image';
    const DEFAULT_UPLOAD_FIELD = 'image';

    const editorByTextarea = new WeakMap();

    const SHARED_REMOVE_PLUGINS = [
        'AIAssistant',
        // Base64UploadAdapter 는 업로드 어댑터가 없을 때 base64 인라인으로 폴백하여
        // 본문에 거대한 data:image/...;base64 문자열을 박아 DB 저장에 실패시킨다.
        // 우리는 항상 커스텀 XHR 어댑터를 사용하므로 폴백을 제거한다.
        'Base64UploadAdapter',
        'CaseChange',
        'CKBox',
        'CKFinder',
        'Comments',
        'DocumentOutline',
        'EasyImage',
        'ExportPdf',
        'ExportWord',
        'FormatPainter',
        'ImportWord',
        'MathType',
        'Mention',
        'Pagination',
        'PasteFromOfficeEnhanced',
        'PresenceList',
        'RealTimeCollaborativeComments',
        'RealTimeCollaborativeRevisionHistory',
        'RealTimeCollaborativeTrackChanges',
        'RevisionHistory',
        'SlashCommand',
        'TableOfContents',
        'Template',
        'TrackChanges',
        'TrackChangesData',
        'WProofreader',
        'MultiLevelList'
    ];

    function createUploadAdapterPlugin(uploadUrl, fieldName) {
        class BackofficeUploadAdapter {
            constructor(loader) {
                this.loader = loader;
                this.xhr = null;
            }

            upload() {
                return this.loader.file.then((file) => new Promise((resolve, reject) => {
                    this._initRequest();
                    this._initListeners(resolve, reject, file);
                    this._sendRequest(file);
                }));
            }

            abort() {
                if (this.xhr) {
                    this.xhr.abort();
                }
            }

            _initRequest() {
                this.xhr = new XMLHttpRequest();
                this.xhr.open('POST', uploadUrl, true);
                this.xhr.responseType = 'json';
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (token) {
                    this.xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
                this.xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            }

            _initListeners(resolve, reject, file) {
                const genericError = `이미지 업로드에 실패했습니다: ${file.name}`;

                this.xhr.addEventListener('error', () => reject(genericError));
                this.xhr.addEventListener('abort', () => reject());
                this.xhr.addEventListener('load', () => {
                    const response = this.xhr.response;
                    if (!response || response.uploaded !== true || !response.url) {
                        reject(response?.error?.message || genericError);
                        return;
                    }
                    resolve({ default: response.url });
                });

                if (this.xhr.upload) {
                    this.xhr.upload.addEventListener('progress', (evt) => {
                        if (!evt.lengthComputable) {
                            return;
                        }
                        this.loader.uploadTotal = evt.total;
                        this.loader.uploaded = evt.loaded;
                    });
                }
            }

            _sendRequest(file) {
                const data = new FormData();
                data.append(fieldName, file);
                this.xhr.send(data);
            }
        }

        return function BackofficeUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => new BackofficeUploadAdapter(loader);
        };
    }

    function buildConfig(element) {
        const globalDefault = typeof window.BACKOFFICE_CKEDITOR_UPLOAD_URL === 'string'
            ? window.BACKOFFICE_CKEDITOR_UPLOAD_URL
            : '';
        const uploadUrl = (element.dataset.uploadUrl || globalDefault || FALLBACK_UPLOAD_URL).trim();
        const fieldName = (element.dataset.uploadField || DEFAULT_UPLOAD_FIELD).trim() || DEFAULT_UPLOAD_FIELD;
        const enableSourceEditing = element.dataset.sourceEditing === 'true';
        const toolbarItems = [
            'heading', '|',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
            'bold', 'italic', 'underline', 'strikethrough', 'removeFormat', '|',
            'alignment', '|',
            'bulletedList', 'numberedList', 'outdent', 'indent', '|',
            'link', 'uploadImage', 'insertTable', 'blockQuote', 'codeBlock', 'horizontalLine', '|',
            'undo', 'redo'
        ];
        if (enableSourceEditing) {
            toolbarItems.unshift('sourceEditing', '|');
        }

        return {
            extraPlugins: [createUploadAdapterPlugin(uploadUrl, fieldName)],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' }
                ]
            },
            toolbar: {
                items: toolbarItems,
                shouldNotGroupWhenFull: true
            },
            image: {
                toolbar: [
                    'imageTextAlternative',
                    '|',
                    'imageStyle:inline',
                    'imageStyle:block',
                    'imageStyle:side'
                ]
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
            },
            // 레거시 이관 본문 등에 포함될 수 있는 <video>/<source> 태그를 CKEditor 편집/저장 시 보존한다.
            // (기본 동작은 화이트리스트에 없는 요소를 제거하므로 명시 등록이 필요하다.)
            htmlSupport: {
                allow: [
                    { name: 'video', attributes: true, classes: true, styles: true },
                    { name: 'source', attributes: true, classes: true, styles: true }
                ]
            },
            removePlugins: [...SHARED_REMOVE_PLUGINS]
        };
    }

    function initOne(element) {
        if (!element || element.tagName !== 'TEXTAREA') {
            return;
        }
        if (typeof CKEDITOR === 'undefined' || !CKEDITOR.ClassicEditor) {
            return;
        }
        if (element.dataset.ckeditorInitialized === 'true') {
            return;
        }

        const config = buildConfig(element);
        CKEDITOR.ClassicEditor.create(element, config)
            .then((editor) => {
                element.dataset.ckeditorInitialized = 'true';
                editorByTextarea.set(element, editor);
                // CKEditor 적용 후 textarea는 보이지 않아 브라우저 HTML5 required 검증 시
                // "An invalid form control is not focusable" 가 발생한다. 필수 여부는 서버에서 검증한다.
                element.removeAttribute('required');
                // 변경 시마다 원본 textarea에 반영해 제출 직전 동기화 누락을 방지한다.
                editor.model.document.on('change:data', () => {
                    if (typeof editor.updateSourceElement === 'function') {
                        editor.updateSourceElement();
                    }
                });
                if (typeof editor.updateSourceElement === 'function') {
                    editor.updateSourceElement();
                }
            })
            .catch((error) => {
                console.error('CKEditor5 initialization failed:', error);
            });
    }

    function initAll(root) {
        const scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll('textarea[data-backoffice-ckeditor]').forEach(initOne);
    }

    /**
     * CKEditor 내용을 원본 textarea에 반영 (폼 전송 직전 호출)
     * @param {ParentNode} [root] 폼 요소를 넘기면 해당 폼 안의 에디터만 동기화
     */
    function syncFieldsToTextareas(root) {
        const scope = root && typeof root.querySelectorAll === 'function' ? root : document;
        if (!scope.querySelectorAll) {
            return;
        }
        scope.querySelectorAll('textarea[data-backoffice-ckeditor]').forEach((textarea) => {
            const editor = editorByTextarea.get(textarea);
            if (editor && typeof editor.updateSourceElement === 'function') {
                editor.updateSourceElement();
            }
        });
    }

    function runInitialInit() {
        initAll(document);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runInitialInit);
    } else {
        runInitialInit();
    }

    window.initBackofficeCKEditors = initAll;
    window.syncBackofficeCKEditorFields = syncFieldsToTextareas;
}());
