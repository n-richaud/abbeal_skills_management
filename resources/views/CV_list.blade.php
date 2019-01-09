


@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
        @foreach($fileList as $File => $v)
   

    <div class="card">
                <div class="card-header"> {{ $File }} </div>

                <div class="card-body">
                   <p>In Folder - {{$v['dirname']}}</p>
                   <p><a href="{{route('export', ['basename' => $v['basename']])}}">Importer le dossier</a></p>
                </div>
                
            </div>
    <!-- @foreach ($v as $key => $value) 
        {{ $key . ' => ' . $value }}

    @endforeach -->
@endforeach
            
        </div>
    </div>
</div>
@endsection