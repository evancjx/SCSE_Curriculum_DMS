@extends('layouts.app')

@section('title', 'Import Curriculum')

@section('content')
    <form class="w-50 mx-auto" action="{{ route('Import Curriculum') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input id="file" type="file" name="file" accept=".csv"/>
        <Button type="submit">Import Curriculum</Button>
    </form>
@endsection
