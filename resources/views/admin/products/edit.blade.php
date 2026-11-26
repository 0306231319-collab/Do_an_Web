@extends('admin.layout')
@section('title', 'Sửa sản phẩm')
@section('content')
<h1 class="text-2xl font-bold mb-4">Sửa sản phẩm: {{ $product->name }}</h1>
@include('admin.products._form')
@endsection
