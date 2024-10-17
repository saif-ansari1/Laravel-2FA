<x-guest-layout>
    <h1>Setup Two-Factor Authentication</h1>
    @if($qrCodeImage)
        <div class="qr-code">
            {!! $qrCodeImage !!}
        </div>
        <p>Scan this QR code with Google Authenticator</p>
    @else
        <form method="POST" action="{{ route('2fa.setup') }}">
            @csrf
            <button type="submit">Generate Secret</button>
        </form>
    @endif
</x-guest-layout>