{{-- 위원회 팝업: 백오피스에서 지정한 위원회 + 게시판(target_board_slug)에만 노출 --}}
@foreach ($committeePopups as $popup)
    @if ($popup->popup_display_type === 'normal')
        <div
            hidden
            class="committee-window-popup-launcher"
            data-committee-window-popup-url="{{ route('popup.show', $popup->id) }}"
            data-committee-window-popup-name="popup_{{ $popup->id }}"
            data-committee-window-popup-features="width={{ (int) ($popup->width ?? 400) }},height={{ (int) ($popup->height ?? 300) }},left={{ (int) ($popup->position_left ?? 100) }},top={{ (int) ($popup->position_top ?? 100) }},scrollbars=yes,resizable=yes,menubar=no,toolbar=no,location=no,status=no"
        ></div>
    @else
        <div
            class="popup-layer popup-fixed committee-scope-popup"
            id="popup-{{ $popup->id }}"
            data-popup-id="{{ $popup->id }}"
            data-display-type="layer"
            data-width="{{ (int) ($popup->width ?? 400) }}"
            data-height="{{ (int) ($popup->height ?? 300) }}"
            data-top="{{ (int) ($popup->position_top ?? 100) }}"
            data-left="{{ (int) ($popup->position_left ?? 100) }}"
        >
            <div class="popup-body">
                @if ($popup->popup_type === 'image' && $popup->popup_image)
                    @if ($popup->url)
                        <a href="{{ $popup->url }}" target="{{ $popup->url_target }}">
                            <img src="{{ asset('storage/'.$popup->popup_image) }}" alt="{{ $popup->title }}">
                        </a>
                    @else
                        <img src="{{ asset('storage/'.$popup->popup_image) }}" alt="{{ $popup->title }}">
                    @endif
                @elseif ($popup->popup_type === 'html' && $popup->popup_content)
                    {!! $popup->popup_content !!}
                @endif
            </div>
            <div class="popup-footer">
                <label class="popup-today-label" data-popup-id="{{ $popup->id }}">
                    <input type="checkbox" class="popup-today-close" data-popup-id="{{ $popup->id }}">
                    1일 동안 보지 않음
                </label>
                <button type="button" class="popup-footer-close-btn" data-popup-id="{{ $popup->id }}">닫기</button>
            </div>
        </div>
    @endif
@endforeach
