@if (session()->has('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@if (session()->has('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif

@if (session()->has('warning'))
    <div class="alert alert-warning" role="alert">
        {{ session('warning') }}
    </div>
@endif

@if (session()->has('info'))
    <div class="alert alert-info" role="alert">
        {{ session('info') }}
    </div>
@endif

@if (session()->has('status'))
    <div class="alert alert-info" role="alert">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
