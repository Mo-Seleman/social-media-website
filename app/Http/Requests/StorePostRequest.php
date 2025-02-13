<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Http\UploadedFile;

class StorePostRequest extends FormRequest
{

    public static array $extentions = [   
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'mp3', 'wav', 'mp4',
        'doc', 'docx', 'pdf', 'csv', 'xls', 'xlsx',
        'zip', 'avif'
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string'],
            'attachments' => [
                'array',
                'max:50',
                function ($attribute, $value, $fail){
                    $totalSize = collect($value)->sum(fn(UploadedFile $file) => $file->getSize());

                    if($totalSize > 1 * 1024 * 1024 * 1024){
                        $fail('The total size of all files must not exceed 1GB');
                    }
                },    
            ],
            'attachments.*' => [
                'file',
                File::types(self::$extentions),
            ],
            'user_id' => ['numeric']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->user()->id,
            'body' => $this->input('body') ?: ''
        ]);
    }

    public function messages()
    {
        return [
            'attachments.*.file' => 'Each attachment must be a file.',
            'attachments.*.mimes' => 'Invalid file type for attachments.',
        ];
    }
}
