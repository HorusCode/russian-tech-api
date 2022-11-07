<?php

namespace App\Http\Requests;

use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string',
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ];
    }

    protected function validationData()
    {
        $data = parent::validationData();
        if(array_key_exists('photo', $data)) {
            $data['photo'] = is_string($data['photo']) ? $this->convertToFile($data['photo']) : $data['photo'];
        }
        return $data;
    }

    private function convertToFile($value)
    {
        if(strpos($value, ';base64') !== false) {
            [, $value] = explode(';', $value);
            [, $value] = explode(',', $value);
        }
        $binaryData = base64_decode($value);
        $tmpFile = tempnam(storage_path('app/tmp'), 'base64');
        file_put_contents($tmpFile, $binaryData);
        return new File($tmpFile);
    }
}
