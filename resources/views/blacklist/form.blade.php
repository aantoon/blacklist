@extends('layouts.app')

@section('title', 'Blacklist form')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-3 py-5 mx-6">
        <h4 class="mb-3">Blacklist form</h4>
        @if(Session::has('success'))
            <div class="alert alert-success" role="alert">
              Saved sucessfuly
            </div>
        @elseif($errors->any())
            <div class="alert alert-danger" role="alert">
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
            </div>
        @endif

        <form class="" action="{{ route('blacklist.store') }}" method="post">
            <div class="col-md-5 py-2">
                  <label for="country" class="form-label">Advertiser ID</label>
                  <select class="form-select" name="advertiser_id" required>
                    @foreach($advertisers as $advertiser)
                        <option value="{{ $advertiser->id }}">{{ $advertiser->id }}</option>
                    @endforeach
                  </select>
            </div>
            <div class="py-1">
                <label for="floatingTextarea">Blacklist</label>
                <textarea class="form-control" required name="blacklist"></textarea>
            </div>

            <div class="">
                <button class="my-3 btn btn-primary" type="submit">Save</button>
            </div>
            @csrf
        </form>
    </div>
</div>
@endsection
