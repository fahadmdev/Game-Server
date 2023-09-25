<?php 

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can define authorization logic here if needed.
        // For example, check if the user is allowed to make this move.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'x' => 'required|integer',
            'y' => 'required|integer',
        ];
    }
}
