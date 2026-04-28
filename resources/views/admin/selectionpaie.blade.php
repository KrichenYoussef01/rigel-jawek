@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    @section('main-class', 'flex-1 flex overflow-hidden p-0 m-0')
    @livewire('admin-dashboard')

@endsection