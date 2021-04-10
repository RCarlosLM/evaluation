@extends('layouts.app')
@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">Home</li>
  </ol>
</nav>
@endsection
@section('content')

    <div class="container">
        <films>
        </films>
    </div>
@endsection
