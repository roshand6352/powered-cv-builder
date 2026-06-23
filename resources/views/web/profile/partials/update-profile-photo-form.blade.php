<section>
    <header>
        <h2 class="text-lg font-semibold text-navy">
            {{ __('Profile Photo') }}
        </h2>

        <p class="mt-1 text-sm text-muted">
            {{ __('Upload a profile photo. JPG, PNG, WebP or GIF up to 2MB.') }}
        </p>
    </header>

    <div class="mt-6 flex flex-col gap-6 sm:flex-row sm:items-center">
        <x-user-avatar :user="$user" size="xl" class="!ring-line" />

        <div class="flex flex-col gap-3">
            <form method="post" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="flex-1">
                    <x-input-label for="photo" :value="__('Choose photo')" />
                    <input
                        id="photo"
                        name="photo"
                        type="file"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        class="mt-1 block w-full text-sm text-body file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100"
                        required
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                </div>
                <x-primary-button>{{ __('Upload') }}</x-primary-button>
            </form>

            @if ($user->avatar_url)
                <form method="post" action="{{ route('profile.photo.destroy') }}">
                    @csrf
                    @method('delete')
                    <x-danger-button>{{ __('Remove Photo') }}</x-danger-button>
                </form>
            @endif

            @if (session('status') === 'photo-updated')
                <p class="text-sm font-medium text-brand-700">{{ __('Photo updated.') }}</p>
            @elseif (session('status') === 'photo-removed')
                <p class="text-sm font-medium text-brand-700">{{ __('Photo removed.') }}</p>
            @endif
        </div>
    </div>
</section>
