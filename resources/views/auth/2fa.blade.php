<x-guest-layout>
                <div class="text-center" >
                    <h1><b>{{ __('Two Factor Authentication') }}</b></h1>
                </div>
                    {{-- <div class="card-body"> --}}
                        <p>{{ __('To enable two-factor authentication, please scan the QR code below with your Google Authenticator app.') }}</p>

                        <br>
                        <div class="text-center" >
                            <div style="display: inline-block;">
                                {!! $qrCodeImage !!}
                            </div>
                        </div>
                        <br>
                        <p>{{ __('If you cannot scan the QR code, you can enter the secret key manually:') }}</p>
                        <p><strong>{{ $user->google2fa_secret }}</strong></p>
                        <br>
                        <p>{{ __('After scanning the QR code, enter the OTP from the Google Authenticator app to complete the setup.') }}</p>
                        
                        
                        <form method="POST" action="{{ route('2fa.verify') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="one_time_password" class="col-md-4 col-form-label text-md-right">{{ __('One Time Password') }}</label>

                                <div class="col-md-6">
                                    <input id="one_time_password" type="text" class="form-control @error('one_time_password') is-invalid @enderror" name="one_time_password" required autofocus>

                                    @error('one_time_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                    <x-primary-button class="ms-4">
                                        {{ __('Verify') }}
                                    </x-primary-button>
                            </div>
                        </form>
               
</x-guest-layout>