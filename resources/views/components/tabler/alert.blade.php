{{-- Session flash alerts (Tabler). Renders success/error/status + validation bag.
     $errors is only shared inside the web middleware group; error pages
     rendered by the exception handler have none — guard with isset. --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div><x-ti name="check" /></div>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div><x-ti name="alert-circle" /></div>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif
@if(session('status'))
    <div class="alert alert-info alert-dismissible" role="alert">
        <div class="d-flex">
            <div><x-ti name="info-circle" /></div>
            <div>{{ session('status') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif
@if(isset($errors) && $errors->any())
    <div class="alert alert-warning" role="alert">
        <div class="d-flex">
            <div><x-ti name="alert-circle" /></div>
            <div>
                <h4 class="alert-title">{{ __('Please fix the following errors') }}</h4>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
