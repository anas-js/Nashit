<?php

namespace App\Http\Requests\CoursesController;

use App\Http\Requests\BaseReq;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class UploadReq extends BaseReq
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "image" => ['required', 'file', 'dimensions:min_width=92,min_height=50,max_height=5000,max_width=5000', 'max:10240', 'mimetypes:image/jpeg,image/png', 'mimes:png,jpg,jpeg'],
            'x' => ['required', 'numeric', "min:0", "max:5000"],
            'y' => ['required', 'numeric', "min:0", "max:5000"],
            'width' => ['required', 'numeric', "min:92", "max:5000"],
            'height' => ['required', 'numeric', "min:50", "max:5000"],
        ];
    }
}
