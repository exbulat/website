@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Подтверждение вашей электронной почты') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('Ссылка для подтверждения электронной почты была отправлена на ваш адрес электронной почты.') }}
                        </div>
                    @endif

                    {{ __('Перед продолжением, пожалуйста, проверьте ваш адрес электронной почты для ссылки на подтверждение.') }}
                    {{ __('Если вы не получили электронное письмо') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('нажмите здесь, чтобы запросить другой') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
