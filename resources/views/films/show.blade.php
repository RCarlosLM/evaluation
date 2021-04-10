@extends('layouts.app')
@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item" aria-current="page">
        <a href="/home">
            Peliculas
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        {{$film->name}}
    </li>
  </ol>
</nav>
@endsection
@section('content')

    <div class="container">
        <shifts
            :film="{{$film}}">
        </shifts>
    </div>

@endsection
