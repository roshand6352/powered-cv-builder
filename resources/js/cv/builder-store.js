// LIVE PREVIEW FIX: global reactive builder store — preview reads ONLY from this state

export function registerBuilderStore() {
    window.Alpine.store('builderStore', {
        document: null,
        sections: {},

        bind(document, sections) {
            this.document = document;
            this.sections = sections;
        },

        get profile() {
            return this.document?.personal_details ?? {};
        },

        get experience() {
            return this.document?.experience ?? [];
        },

        get education() {
            return this.document?.education ?? [];
        },

        get projects() {
            return this.document?.projects ?? [];
        },

        get skills() {
            return this.document?.skills ?? [];
        },

        get languages() {
            return this.document?.languages ?? [];
        },

        get certifications() {
            return this.document?.certifications ?? [];
        },

        get references() {
            return this.document?.references ?? [];
        },

        get customSections() {
            return this.document?.custom_sections ?? [];
        },
    });
}
