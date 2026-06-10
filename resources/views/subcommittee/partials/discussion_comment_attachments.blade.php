@if (! empty($attachments))
	<div class="chat_attachments">
		@foreach ($attachments as $attachment)
			@if ($attachment['is_image'])
				<a href="{{ $attachment['url'] }}" target="_blank" rel="noopener" class="chat_attachment_image">
					<img src="{{ $attachment['url'] }}" alt="{{ $attachment['name'] }}">
				</a>
			@else
				<a href="{{ $attachment['url'] }}" target="_blank" rel="noopener" class="chat_attachment_file">
					{{ $attachment['name'] }}
				</a>
			@endif
		@endforeach
	</div>
@endif
