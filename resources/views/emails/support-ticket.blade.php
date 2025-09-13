@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }} Support
        @endcomponent
    @endslot

    # New Support Ticket
    
    **From:** {{ $ticket['name'] }} ({{ $ticket['email'] }})
    
    **Category:** {{ ucfirst($ticket['category']) }}
    
    **Priority:** {{ ucfirst($ticket['priority']) }}
    
    **Subject:** {{ $ticket['subject'] }}
    
    **Message:**
    
    {{ $ticket['message'] }}
    
    @if(isset($ticket['attachments']) && count($ticket['attachments']) > 0)
        
        **Attachments:**
        @foreach($ticket['attachments'] as $attachment)
            - {{ $attachment['name'] }} ({{ $attachment['size'] / 1024 }} KB)
        @endforeach
    @endif
    
    ---
    
    This is an automated message from the {{ config('app.name') }} support system.
    
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        @endcomponent
    @endslot
@endcomponent
