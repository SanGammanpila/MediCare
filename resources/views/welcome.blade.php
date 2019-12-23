@extends('layouts.app')

<style>
.container1{
    height: 100vh;
    text-align: center;

}
.bg {
  background-image: url("{{ asset('imgs/landing_main.jpg') }}");
  height: 88vh;
}

.center{

  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;

  width:100%;
  display:flex;
  align-items: center;
  justify-content: center;
}

.heading { 
  font-family: 'Helvetica Neue', sans-serif;
  font-size: 10rem; 
  font-weight: bold;
  letter-spacing: -1px;
  line-height: 1;
  text-align: center;
  color: #222;
  text-shadow: 5px 5px white;

}

</style>

@section('content')

<div class="container1">
    <div class="row">
        <div class="col">
          <div class="bg center">
            <h1 class="heading"> Medi Care </h1>
        </div>
    </div>
        <div class="col-3 center">
         <form method="POST" action="{{route('login') }}">
            @csrf
            <div class="form-group">
                <label>Email address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label >Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
            
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label">Remember me</label>
            </div>
            {{-- <button type="submit" class="btn btn-primary">Log In</button> --}} 
            <button type="submit" class="btn btn-primary">
                {{ __('Login') }}
            </button>

            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
            </form>
        </div>
    </div>
</div>

@endsection
