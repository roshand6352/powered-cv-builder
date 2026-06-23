<x-dashboard.layout title="Profile">
    <x-slot name="header">
        <x-dashboard.page-header
            title="Profile"
            description="Manage your account settings and preferences."
            class="mb-0"
        />
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="dash-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('web.profile.partials.update-profile-photo-form')
            </div>
        </div>

        <div class="dash-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('web.profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="dash-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('web.profile.partials.update-password-form')
            </div>
        </div>

        <div class="dash-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('web.profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-dashboard.layout>
