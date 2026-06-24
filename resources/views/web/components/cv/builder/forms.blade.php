<div
    class="space-y-8"
    @input.capture="onBuilderFieldChange($event)"
    @change.capture="onBuilderFieldChange($event)"
>
    @php
        $aiPlaceholderSuffix = ' and click Improve to let AI make it more professional.';
    @endphp

    <template x-if="Object.keys(errors).length">
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Please fix the following errors:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                <template x-for="(messages, field) in errors" :key="field">
                    <li x-text="`${field}: ${Array.isArray(messages) ? messages[0] : messages}`"></li>
                </template>
            </ul>
        </div>
    </template>

    {{-- Personal Info --}}
    <section x-show="showPersonalInfoSection()" data-builder-section="personal_details" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="personal_details" title="Personal Info" />

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700">Full name</label>
                <input type="text" x-model="document.personal_details.full_name" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700">Job title</label>
                <input type="text" x-model="document.personal_details.job_title" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Email</label>
                <input type="email" x-model="document.personal_details.email" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Phone</label>
                <input type="text" x-model="document.personal_details.phone" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Location</label>
                <input type="text" x-model="document.personal_details.location" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Website</label>
                <input type="url" x-model="document.personal_details.website" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700">LinkedIn</label>
                <input type="url" x-model="document.personal_details.linkedin" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
        </div>
    </section>

    {{-- Profile Image --}}
    <section x-show="showProfileImageSection()" data-builder-section="personal_details" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Profile Image</h2>
                <p class="mt-1 text-sm text-slate-500" x-text="profilePhotoPositionHint()"></p>
            </div>
        </div>

        <div class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-center">
            <div class="shrink-0">
                <template x-if="profilePhotoUrl()">
                    <img
                        :src="profilePhotoUrl()"
                        alt="Profile preview"
                        class="h-28 w-28 rounded-full border-2 border-slate-200 object-cover shadow-sm"
                    >
                </template>
                <template x-if="! profilePhotoUrl()">
                    <div class="flex h-28 w-28 items-center justify-center rounded-full border-2 border-dashed border-slate-300 bg-slate-50 text-slate-400">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                </template>
            </div>

            <div class="flex flex-col gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Upload photo</label>
                    <input
                        type="file"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        data-preview-field="personal_details.photo_preview"
                        class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100"
                    >
                    <p class="mt-1 text-xs text-slate-500">JPG, PNG, WebP or GIF. Recommended square image, at least 400×400px.</p>
                    <p x-show="profileImageError" x-text="profileImageError" class="mt-2 text-xs text-red-600"></p>
                </div>

                <button
                    type="button"
                    x-show="profilePhotoUrl()"
                    @click="removeProfilePhoto()"
                    class="inline-flex w-fit items-center rounded-lg border border-red-200 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                >
                    Remove photo
                </button>
            </div>
        </div>
    </section>

    {{-- About --}}
    <section x-show="showAboutSection()" data-builder-section="about" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="about" title="About" />

        <div class="mt-4">
            <div class="mb-2 flex items-start justify-between gap-3">
                <label class="block text-sm font-medium text-slate-700">Professional summary</label>
                <x-cv.builder.ai-button
                    action="aiImproveSummary('about-summary')"
                    field-key-expr="'about-summary'"
                    size="xs"
                />
            </div>
            <div
                class="cv-rich-text-field mt-1"
                x-data="richTextField({
                    get: () => document.personal_details.summary,
                    set: (value) => { document.personal_details.summary = value; },
                    minHeight: '160px',
                    placeholder: @js('Write a short professional summary'.$aiPlaceholderSuffix),
                })"
            >
                <div x-ref="editor" data-rich-text-mount></div>
            </div>
        </div>
    </section>

    {{-- Experience --}}
    <section x-show="showBuilderSection('experience')" data-builder-section="experience" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="experience" title="Experience" />

        <div class="mt-4 space-y-4">
            <template x-for="(item, index) in document.experience" :key="item.id">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900" x-text="`Experience #${index + 1}`"></h3>
                        <button type="button" @click="removeItem('experience', index)" class="text-sm font-medium text-red-600 hover:text-red-700">Remove</button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Company</label>
                            <input type="text" x-model="item.company" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Position</label>
                            <input type="text" x-model="item.position" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Location</label>
                            <input type="text" x-model="item.location" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" x-model="item.current" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                Currently working here
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Start date</label>
                            <input type="text" x-model="item.start_date" placeholder="Jan 2020" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">End date</label>
                            <input type="text" x-model="item.end_date" placeholder="Dec 2023" :disabled="item.current" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 disabled:bg-slate-100">
                        </div>
                        <div class="sm:col-span-2">
                            <div class="flex items-start justify-between gap-3">
                                <label class="block text-sm font-medium text-slate-700">Description</label>
                                <x-cv.builder.ai-button
                                    action="aiImproveExperience(index, 'experience-' + index)"
                                    field-key-expr="'experience-' + index"
                                    size="xs"
                                />
                            </div>
                            <div
                                class="cv-rich-text-field mt-1"
                                x-data="richTextField({
                                    get: () => item.description,
                                    set: (value) => { item.description = value; },
                                    minHeight: '140px',
                                    placeholder: @js('Describe your responsibilities and achievements'.$aiPlaceholderSuffix),
                                })"
                            >
                                <div x-ref="editor" data-rich-text-mount></div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem('experience')" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-brand-300 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                + Add experience
            </button>
        </div>
    </section>

    {{-- Education --}}
    <section x-show="showBuilderSection('education')" data-builder-section="education" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="education" title="Education" />

        <div class="mt-4 space-y-4">
            <template x-for="(item, index) in document.education" :key="item.id">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900" x-text="`Education #${index + 1}`"></h3>
                        <button type="button" @click="removeItem('education', index)" class="text-sm font-medium text-red-600 hover:text-red-700">Remove</button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Institution</label>
                            <input type="text" x-model="item.institution" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Degree</label>
                            <input type="text" x-model="item.degree" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Field of study</label>
                            <input type="text" x-model="item.field" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Start date</label>
                            <input type="text" x-model="item.start_date" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">End date</label>
                            <div class="mt-1 flex flex-col gap-2 sm:flex-row sm:items-center">
                                <input
                                    type="text"
                                    x-model="item.end_date"
                                    :disabled="item.in_progress"
                                    class="block w-full flex-1 rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 disabled:bg-slate-100"
                                >
                                <label class="inline-flex shrink-0 items-center gap-2 text-sm text-slate-700">
                                    <input
                                        type="checkbox"
                                        x-model="item.in_progress"
                                        @change="if (item.in_progress) item.end_date = ''"
                                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                    >
                                    In progress
                                </label>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="flex items-start justify-between gap-3">
                                <label class="block text-sm font-medium text-slate-700">Description</label>
                                <x-cv.builder.ai-button
                                    action="aiImproveRichText(item.description, (text) => item.description = text, 'education-' + index)"
                                    field-key-expr="'education-' + index"
                                    size="xs"
                                />
                            </div>
                            <div
                                class="cv-rich-text-field mt-1"
                                x-data="richTextField({
                                    get: () => item.description,
                                    set: (value) => { item.description = value; },
                                    minHeight: '120px',
                                    placeholder: @js('Describe your studies, honours, or activities'.$aiPlaceholderSuffix),
                                })"
                            >
                                <div x-ref="editor" data-rich-text-mount></div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem('education')" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-brand-300 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                + Add education
            </button>
        </div>
    </section>

    {{-- Skills --}}
    <section x-show="showBuilderSection('skills')" data-builder-section="skills" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="skills" title="Skills" />

        <div class="mt-4 space-y-4">
            <template x-for="(item, index) in document.skills" :key="item.id">
                <div class="grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-[1fr_1fr_auto]">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Skill</label>
                        <input type="text" x-model="item.name" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Level</label>
                        <input type="text" x-model="item.level" placeholder="Advanced" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div class="flex items-end">
                        <button type="button" @click="removeItem('skills', index)" class="rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Remove</button>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem('skills')" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-brand-300 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                + Add skill
            </button>
        </div>
    </section>

    {{-- Projects --}}
    <section x-show="showBuilderSection('projects')" data-builder-section="projects" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="projects" title="Projects" />

        <div class="mt-4 space-y-4">
            <template x-for="(item, index) in document.projects" :key="item.id">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900" x-text="`Project #${index + 1}`"></h3>
                        <button type="button" @click="removeItem('projects', index)" class="text-sm font-medium text-red-600 hover:text-red-700">Remove</button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Project name</label>
                            <input type="text" x-model="item.name" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">URL</label>
                            <input type="url" x-model="item.url" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Start date</label>
                            <input type="text" x-model="item.start_date" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">End date</label>
                            <div class="mt-1 flex flex-col gap-2 sm:flex-row sm:items-center">
                                <input
                                    type="text"
                                    x-model="item.end_date"
                                    :disabled="item.in_progress"
                                    class="block w-full flex-1 rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 disabled:bg-slate-100"
                                >
                                <label class="inline-flex shrink-0 items-center gap-2 text-sm text-slate-700">
                                    <input
                                        type="checkbox"
                                        x-model="item.in_progress"
                                        @change="if (item.in_progress) item.end_date = ''"
                                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                    >
                                    In progress
                                </label>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="flex items-start justify-between gap-3">
                                <label class="block text-sm font-medium text-slate-700">Description</label>
                                <x-cv.builder.ai-button
                                    action="aiImproveRichText(item.description, (text) => item.description = text, 'projects-' + index)"
                                    field-key-expr="'projects-' + index"
                                    size="xs"
                                />
                            </div>
                            <div
                                class="cv-rich-text-field mt-1"
                                x-data="richTextField({
                                    get: () => item.description,
                                    set: (value) => { item.description = value; },
                                    minHeight: '140px',
                                    placeholder: @js('Describe the project, your role, and outcomes'.$aiPlaceholderSuffix),
                                })"
                            >
                                <div x-ref="editor" data-rich-text-mount></div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem('projects')" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-brand-300 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                + Add project
            </button>
        </div>
    </section>

    {{-- Languages --}}
    <section x-show="showBuilderSection('languages')" data-builder-section="languages" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="languages" title="Languages" />

        <div class="mt-4 space-y-4">
            <template x-for="(item, index) in document.languages" :key="item.id">
                <div class="grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-[1fr_1fr_auto]">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Language</label>
                        <input type="text" x-model="item.name" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Proficiency</label>
                        <input type="text" x-model="item.proficiency" placeholder="Fluent" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div class="flex items-end">
                        <button type="button" @click="removeItem('languages', index)" class="rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Remove</button>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem('languages')" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-brand-300 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                + Add language
            </button>
        </div>
    </section>

    {{-- Certifications --}}
    <section x-show="showBuilderSection('certifications')" data-builder-section="certifications" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="certifications" title="Certifications" />

        <div class="mt-4 space-y-4">
            <template x-for="(item, index) in document.certifications" :key="item.id">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900" x-text="`Certification #${index + 1}`"></h3>
                        <button type="button" @click="removeItem('certifications', index)" class="text-sm font-medium text-red-600 hover:text-red-700">Remove</button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Name</label>
                            <input type="text" x-model="item.name" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Issuer</label>
                            <input type="text" x-model="item.issuer" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Date</label>
                            <input type="text" x-model="item.date" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">URL</label>
                            <input type="url" x-model="item.url" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem('certifications')" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-brand-300 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                + Add certification
            </button>
        </div>
    </section>

    {{-- References --}}
    <section x-show="showBuilderSection('references')" data-builder-section="references" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="references" title="References" />

        <div class="mt-4 space-y-4">
            <template x-for="(item, index) in document.references" :key="item.id">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900" x-text="`Reference #${index + 1}`"></h3>
                        <button type="button" @click="removeItem('references', index)" class="text-sm font-medium text-red-600 hover:text-red-700">Remove</button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Name</label>
                            <input type="text" x-model="item.name" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Company</label>
                            <input type="text" x-model="item.company" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" x-model="item.email" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Phone</label>
                            <input type="text" x-model="item.phone" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Relationship</label>
                            <input type="text" x-model="item.relationship" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem('references')" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-brand-300 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                + Add reference
            </button>
        </div>
    </section>

    {{-- Custom sections (e.g. Clients) --}}
    <section x-show="showBuilderSection('custom_sections')" data-builder-section="custom_sections" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <x-cv.builder.section-header section="custom_sections" title="Custom Sections" />

        <div class="mt-4 space-y-6">
            <template x-for="(section, sectionIndex) in document.custom_sections" :key="section.id">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold text-slate-900" x-text="`Custom section #${sectionIndex + 1}`"></h3>
                        <button type="button" @click="removeCustomSection(sectionIndex)" class="text-sm font-medium text-red-600 hover:text-red-700">Remove section</button>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700">Section title</label>
                        <input type="text" x-model="section.title" placeholder="Clients, Awards, etc." class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, itemIndex) in section.items" :key="item.id">
                            <div class="rounded-lg border border-slate-200 bg-white p-3">
                                <div class="mb-3 flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500" x-text="`Item #${itemIndex + 1}`"></span>
                                    <button type="button" @click="removeCustomItem(sectionIndex, itemIndex)" class="text-xs font-medium text-red-600 hover:text-red-700">Remove</button>
                                </div>
                                <div class="grid gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700">Title</label>
                                        <input type="text" x-model="item.title" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                    </div>
                                    <div>
                                        <div class="mb-1 flex items-start justify-between gap-3">
                                            <label class="block text-sm font-medium text-slate-700">Content</label>
                                            <x-cv.builder.ai-button
                                                action="aiImproveRichText(item.content, (text) => item.content = text, 'custom-' + sectionIndex + '-' + itemIndex)"
                                                field-key-expr="'custom-' + sectionIndex + '-' + itemIndex"
                                                size="xs"
                                            />
                                        </div>
                                        <div
                                            class="cv-rich-text-field mt-1"
                                            x-data="richTextField({
                                                get: () => item.content,
                                                set: (value) => { item.content = value; },
                                                minHeight: '120px',
                                                placeholder: @js('Write the item content'.$aiPlaceholderSuffix),
                                            })"
                                        >
                                            <div x-ref="editor" data-rich-text-mount></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addCustomItem(sectionIndex)" class="mt-3 inline-flex items-center gap-2 rounded-lg border border-dashed border-brand-300 px-3 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                        + Add item
                    </button>
                </div>
            </template>

            <button type="button" @click="addCustomSection()" class="inline-flex items-center gap-2 rounded-lg border border-dashed border-brand-300 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                + Add custom section
            </button>
        </div>
    </section>
</div>
