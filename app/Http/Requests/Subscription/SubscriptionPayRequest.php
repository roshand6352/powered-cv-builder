<?php

namespace App\Http\Requests\Subscription;

use App\Models\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubscriptionPayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'integer'],
            'plan_name' => ['required', 'string', 'max:255'],
            'plan_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Plan|null $plan */
            $plan = $this->route('plan');

            if (! $plan instanceof Plan || $validator->errors()->isNotEmpty()) {
                return;
            }

            if ((int) $this->input('plan_id') !== (int) $plan->id) {
                $validator->errors()->add('plan_id', 'Selected plan does not match checkout.');
            }

            if ((string) $this->input('plan_name') !== (string) $plan->name) {
                $validator->errors()->add('plan_name', 'Plan name has changed. Please review checkout.');
            }

            if ((float) $this->input('plan_price') !== (float) $plan->price) {
                $validator->errors()->add('plan_price', 'Plan price has changed. Please review checkout.');
            }
        });
    }
}
