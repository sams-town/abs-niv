@extends('templates.login')
@section('container')
    <form class="tf-form" action="{{ route('password.update') }}" method="POST">
        @csrf
        <h1>{{ $title }}</h1>
        <div class="group-input">
            <label>Email</label>
            <input type="email" class="@error('email') is-invalid @enderror" value="{{ old('email', request('email')) }}" name="email" readonly>
            @error('email')
              <div class="invalid-feedback">
                  {{ $message }}
              </div>
            @enderror
        </div>

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="group-input">
            <label>New Password</label>
            <input type="password" class="@error('password') is-invalid @enderror" value="{{ old('password') }}" name="password">
            @error('password')
              <div class="invalid-feedback">
                  {{ $message }}
              </div>
            @enderror
        </div>

        <div class="group-input">
            <label>Confirm Password</label>
            <input type="password" class="@error('password_confirmation') is-invalid @enderror" value="{{ old('password_confirmation') }}" name="password_confirmation">
            @error('password_confirmation')
              <div class="invalid-feedback">
                  {{ $message }}
              </div>
            @enderror
        </div>

        <button type="submit" class="tf-btn accent large">Send Reset Link</button>
    </form>
@endsection
