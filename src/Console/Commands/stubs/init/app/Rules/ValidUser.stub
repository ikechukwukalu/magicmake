<?php

namespace App\Rules;

use App\Facades\Database;
use App\Models\Admin;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class ValidUser implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    public function __construct(private string $table, private bool $allowAdmin = false)
    { }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_null($value)) {
            $fail(trans('validation.rules.invalid'));
            return;
        }

        $user = Auth::user();

        if ($this->allowAdmin) {
            $allowedUsers = [Admin::class];
            if (in_array($user->model, $allowedUsers)) {
                return;
            }
        }

        $user_id = $user->id;

        if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }

        if (!Database::queryBuilder($this->table)->where('id', $value)
            ->where('user_id', $user_id)->exists()
        ) {
            $fail(trans('validation.rules.invalid'));
        }
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

}
