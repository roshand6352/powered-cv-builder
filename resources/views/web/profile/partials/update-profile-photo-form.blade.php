<section>
    <header>
        <h2 class="text-lg font-semibold text-navy">
            {{ __('Profile Photo') }}
        </h2>

        <p class="mt-1 text-sm text-muted">
            {{ __('Upload a profile photo. JPG, JPEG, PNG, or WebP up to 2MB.') }}
        </p>
    </header>

    <div class="mt-6 flex flex-col gap-6 sm:flex-row sm:items-center">
        <div class="relative shrink-0">
            <div id="profile-photo-current">
                <x-user-avatar :user="$user" size="xl" class="!ring-line" />
            </div>
            <img
                id="profile-photo-preview"
                src=""
                alt="{{ __('Selected profile photo preview') }}"
                class="hidden h-24 w-24 rounded-full object-cover ring-2 ring-line"
            >
        </div>

        <div class="flex flex-col gap-3">
            <form method="post" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="flex-1">
                    <x-input-label for="photo" :value="__('Choose photo')" />
                    <input
                        id="photo"
                        name="photo"
                        type="file"
                        accept="image/jpeg,image/jpg,image/png,image/webp"
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

<script>
    (function () {
        const input = document.getElementById('photo');
        const preview = document.getElementById('profile-photo-preview');
        const current = document.getElementById('profile-photo-current');

        if (!input || !preview || !current) {
            return;
        }

        input.addEventListener('change', function () {
            const file = input.files && input.files[0];

            if (!file) {
                preview.src = '';
                preview.classList.add('hidden');
                current.classList.remove('hidden');
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {
                preview.src = event.target?.result || '';
                preview.classList.remove('hidden');
                current.classList.add('hidden');
            };

            reader.readAsDataURL(file);
        });
    })();
</script>
