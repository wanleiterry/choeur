<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;

class StoreClusterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'parent_id' => 'required|min:0|integer',
            'name' => 'required|unique:cluster|max:60',
            'ip' => 'required|ip',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(){
        return [
            'parent_id.*' => '参数parent_id不合法',
            'name.*' => '参数name不合法',
            'ip.*' => '参数ip不合法',
        ];
    }
}
