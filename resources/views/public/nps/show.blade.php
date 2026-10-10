<x-tabler.public :title="__('How was your experience?')" :meta-description="__('Rate your experience working with our team.')">
    @push('head')
        <meta name="robots" content="noindex, nofollow">
    @endpush

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-2 text-primary"><x-ti name="star" class="icon icon-lg" /></div>
                    <h1 class="card-title justify-content-center">{{ __('How was your experience?') }}</h1>
                    <p class="text-muted mb-4">{{ __('How likely are you to recommend working with our team to a friend or colleague?') }}</p>

                    <form method="POST" action="{{ route('nps.submit', $token) }}" x-data="{ score: null }">
                        @csrf
                        <div class="d-flex flex-wrap justify-content-center gap-1 mb-2">
                            @for($i = 0; $i <= 10; $i++)
                                <button type="button"
                                    @click="score = {{ $i }}"
                                    :class="score === {{ $i }} ? 'btn-primary' : ''"
                                    class="btn btn-sm">{{ $i }}</button>
                            @endfor
                        </div>
                        <input type="hidden" name="score" :value="score" required>
                        @error('score')<div class="invalid-feedback d-block text-center">{{ $message }}</div>@enderror

                        <div class="d-flex justify-content-between text-muted mb-3">
                            <span>&larr; {{ __('Not satisfied') }}</span>
                            <span>{{ __('Very satisfied') }} &rarr;</span>
                        </div>

                        <div x-show="score !== null" x-transition class="text-start mb-3">
                            <x-tabler.field :label="__('Tell us a bit more (optional)')" name="comment">
                                <textarea id="comment" name="comment" rows="3" maxlength="2000" class="form-control" placeholder="{{ __('What made you give that score?') }}">{{ old('comment') }}</textarea>
                            </x-tabler.field>
                        </div>

                        <button type="submit" :disabled="score === null" class="btn btn-primary"><x-ti name="send" /> {{ __('Send Feedback') }}</button>
                    </form>
                </div>
            </div>
            <p class="text-center text-muted mt-3">{{ __('Powered by') }} <a href="/" class="fw-bold">Invocely</a></p>
        </div>
    </div>
</x-tabler.public>
