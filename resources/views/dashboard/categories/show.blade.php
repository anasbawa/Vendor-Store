@extends('layouts.dashboard')

@section('title', $category->name)

@section('breadcrumb')
@parent
<li class="breadcrumb-item active">Categories</li>
<li class="breadcrumb-item active">{{ $category->name }}</li>
@endsection

@section('content')

<table class="table">
    <thead>
        <tr>
            <th></th>
            <th>Name</th>
            <th>Store</th>
            <th>status</th>
            <th>Created_at</th>
        </tr>
    </thead>
    <tbody>

        @php
            // Eager loading store relation
            $products = $category->products()->with('store')->paginate(5);
        @endphp

        @forelse ($products as $product )
        <tr>
            <td><img src="{{ $product->image }}" alt="" height="50" width="50"></td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->store->name }}</td>
            <td>{{ $product->status }}</td>
            <td>{{ $product->created_at }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5">No products Found</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $products->links() }}

@endsection
