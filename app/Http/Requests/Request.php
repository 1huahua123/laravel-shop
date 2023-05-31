<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{

    /**
     * @return true
     */
    public function authorize()
    {
        return true;
    }

}
