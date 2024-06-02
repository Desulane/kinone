@extends('layout.app')
@section('title', 'Подтвердите email')
@section('content')
    @include('partials.header')
    <p>Необходимо подтверждение email</p>
    <a href='{{route('verification.send')}}'>
        Отправить повторно
    </a>
@endsection
