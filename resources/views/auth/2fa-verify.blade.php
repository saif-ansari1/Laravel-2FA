<x-guest-layout>
    <h1>Verify Two-Factor Authentication</h1>
    <form method="POST" action="{{ route('2fa.verify') }}">
        @csrf
        <label for="one_time_password">Enter OTP from Google Authenticator</label>
        <input type="text" name="one_time_password">
        <button type="submit">Verify</button>
    </form>
</x-guest-layout>