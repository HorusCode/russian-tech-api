<?php


namespace App\Services;


use Carbon\Carbon;
use Imagick;

class PhotoService
{
    protected $imagick;
    protected $path;
    protected $format = 'png';

    public function __construct()
    {
        $this->imagick = new Imagick();
        $this->path = public_path('\photos\api\\');
    }

    /**
     * @param $image
     * @param string $format
     * @return mixed
     * @throws \ImagickException
     */
    public function uploadImage($image, $format = '')
    {
        if ($format !== '') {
            $this->format = $format;
        }

        if (is_string($image)) {
            $this->imagick = $this->convertBase64ToImage($image);
            return $this->saveImagickFile();
        } else {
            return $this->saveRequestFile($image);
        }
    }

    /**
     * @param $image
     * @return Imagick
     * @throws \ImagickException
     */
    private function convertBase64ToImage($image)
    {
        if (strpos($image, ';base64') !== false) {
            [, $image] = explode(';', $image);
            [, $image] = explode(',', $image);
        }
        $binaryData = base64_decode($image);
        return $this->convertBinaryToFormat($binaryData);
    }

    /**
     * @param $bin
     * @return Imagick
     * @throws \ImagickException
     */
    private function convertBinaryToFormat($bin)
    {
        $this->imagick->readImageBlob($bin);
        $this->imagick->setImageFormat($this->format);
        $this->imagick->setImageFilename(time());
        return $this->imagick;
    }

    private function saveImagickFile()
    {
        $fullName = $this->imagick->getImageFilename() . '.' . $this->imagick->getImageFormat();
        $this->imagick->writeImage($this->path . $fullName);
        return $fullName;
    }


    private function saveRequestFile($image)
    {
        $fullName = time().'.'.$image->getClientOriginalExtension();
        $image->move($this->path, $fullName);
        return $fullName;
    }

    public function removeImage(string $filename)
    {
        if (file_exists($this->path . $filename)) {
            unlink($this->path . $filename);
        }
        return true;
    }

    public function allData($arr)
    {
        $data = $arr->map(function ($item) {
           return [
               'id' => $item->id,
               'name' => $item->name,
               'owner_id' => $item->owner_id,
               'url'=> asset("photos/api/$item->filename"),
               'users' => $item->users()->get()->pluck('id')
           ];
        });
        return $data;
    }
}
