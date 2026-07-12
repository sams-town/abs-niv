@extends('templates.login')
@section('container')
    <form class="tf-form" action="{{ url('/forgot-password/link') }}" method="POST">
        @csrf
        <h1>{{ $title }}</h1>
        <div class="group-input">
            <label>Email</label>
            <input type="email" placeholder="email" class="@error('email') is-invalid @enderror" value="{{ old('email') }}" name="email">
            @error('email')
              <div class="invalid-feedback">
                  {{ $message }}
              </div>
            @enderror
        </div>

        <button type="submit" class="tf-btn accent large">Send Reset Link</button>
    </form>
    <p class="mb-9 fw-3 text-center ">Already have an Account? <a href="{{ url('/') }}" class="auth-link-rg" >Sign in</a></p>
@endsection
