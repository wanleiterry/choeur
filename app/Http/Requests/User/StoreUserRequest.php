<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Request;

class StoreUserRequest extends FormRequest
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

    private function getSegmentFromEnd()
    {
        $uri = Request::getRequestUri();
        preg_match('{[^/]+(?!.*/)[^.json]}', $uri, $matches);
        return $matches[0];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role_id' => 'required|min:1|integer',
//            'username' => 'required|max:60|unique:user,username,' . $this->getSegmentFromEnd() . ',id',
            'password' => 'required|max:100',
            'confirm_password' => 'same:password',
            'avatar' => 'required',
            'mobile' => 'required',
            'email' => 'required|email',
            'sex' => 'required|integer|min:0|max:1',
            'status' => 'required|integer|min:0|max:1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(){
        return [
            'required' => 'A title is required',
        ];
    }
}
