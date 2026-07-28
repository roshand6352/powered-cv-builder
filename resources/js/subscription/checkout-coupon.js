export function registerCheckoutCoupon() {
    window.Alpine.data('checkoutCoupon', (config) => ({
        planId: config.planId,
        planPrice: Number(config.planPrice || 0),
        symbol: config.symbol || 'R',
        validateUrl: config.validateUrl,
        removeUrl: config.removeUrl,
        code: config.applied?.code || '',
        applied: Boolean(config.applied?.valid || config.applied?.success),
        loading: false,
        errorMessage: '',
        successMessage: config.applied?.message || 'Coupon applied successfully.',
        discountAmount: Number(config.applied?.discount_amount || 0),
        total: Number(config.applied?.final_total ?? config.applied?.total ?? config.planPrice ?? 0),
        appliedCode: config.applied?.code || '',
        formatted: config.applied?.formatted || null,

        get formattedPlanPrice() {
            return this.formatted?.plan_price || this.money(this.planPrice);
        },

        get formattedDiscount() {
            return this.formatted?.discount_amount || this.money(this.discountAmount);
        },

        get formattedTotal() {
            return this.formatted?.total || this.money(this.total);
        },

        money(amount) {
            return `${this.symbol} ${Number(amount || 0).toFixed(2)}`;
        },

        async applyCoupon() {
            if (this.loading || this.applied) {
                return;
            }

            const code = String(this.code || '').trim();

            if (! code) {
                this.errorMessage = 'Please enter a discount code.';

                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const { data } = await window.axios.post(this.validateUrl, {
                    code,
                    plan_id: this.planId,
                });

                this.applied = true;
                this.appliedCode = data.code;
                this.code = data.code;
                this.successMessage = data.message || 'Coupon applied successfully.';
                this.discountAmount = Number(data.discount_amount || 0);
                this.total = Number(data.final_total ?? data.total ?? this.planPrice);
                this.formatted = data.formatted || null;
            } catch (error) {
                const message = error?.response?.data?.message
                    || error?.response?.data?.errors?.code?.[0]
                    || 'This code is not valid or has expired.';
                this.errorMessage = message;
                this.applied = false;
                this.discountAmount = 0;
                this.total = this.planPrice;
                this.formatted = null;
                this.appliedCode = '';
            } finally {
                this.loading = false;
            }
        },

        async removeCoupon() {
            if (this.loading) {
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const { data } = await window.axios.post(this.removeUrl, {
                    plan_id: this.planId,
                });

                this.applied = false;
                this.appliedCode = '';
                this.code = '';
                this.discountAmount = 0;
                this.total = Number(data.total ?? this.planPrice);
                this.formatted = data.formatted || null;
                this.successMessage = '';
            } catch (error) {
                this.errorMessage = 'Unable to remove coupon. Please try again.';
            } finally {
                this.loading = false;
            }
        },
    }));
}
