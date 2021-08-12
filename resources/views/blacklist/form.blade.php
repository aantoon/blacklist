@extends('layouts.app')

@section('title', 'Blacklist form')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-3 py-5 mx-6">
        <h4 class="mb-3">Blacklist form</h4>

        <form class="" action="" method="post">
            <div class="col-md-5 py-2">
                  <label for="country" class="form-label">Advertiser ID</label>
                  <select class="form-select" required>
                    @foreach($advertisers as $advertiser)
                        <option value="{{ $advertiser->id }}">{{ $advertiser->id }}</option>
                    @endforeach
                  </select>
            </div>
            <div class="py-1">
                <label for="floatingTextarea">Blacklist</label>
                <textarea class="form-control" required></textarea>
            </div>

            <div class="">
                <button class="my-3 btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
