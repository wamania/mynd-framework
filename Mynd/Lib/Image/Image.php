<?php

namespace Mynd\Lib\Image;

class Image
{
    private $image;

    private $extension;

    private $size;

    public function __construct($filename, $extension = null)
    {
        $this->image = null;

        if (is_null($extension)) {
            $this->extension = self::getExtension($filename);
        } else {
            $this->extension = $extension;
        }

        // redimensionnement
        switch($this->extension) {
            case 'jpg':
            case 'jpeg':
                $this->image = imagecreatefromjpeg($filename);
                break;
            case 'png':
                $this->image = imagecreatefrompng($filename);
                break;
            case 'gif':
                $this->image = imagecreatefromgif($filename);
        }

        // taille de l'image original
        $this->size = getimagesize($filename);
    }

    /**
     * Renvoi une image redimensionnée pour tenir dans un carré de $width x $width
     * @param $width
     * @param $height
     * @return unknown_type
     */
    public function getBoxedImage($width, $height, $newFile, $jpegQuality = 100)
    {
        if (is_null($this->image)) {
            return false;
        }

        // on crée la nouvelle image
        $newImage = imagecreatetruecolor($width, $height);

        if (in_array($this->extension, array('gif', 'png'))) {
            // on sauvegarde le canal alpha (pour la transparence)
            imagesavealpha($newImage, true);

            // on crée un noir qui servira pour la transparence
            $colorImage = imagecolorallocatealpha($newImage,0x00,0x00,0x00,127);

        } else {
            $colorImage = imagecolorallocate($newImage,0xFF,0xFF,0xFF);
        }

        // fond transparent
        imagefill($newImage, 0, 0, $colorImage);

        $originalDiagonale = $this->size[0] / $this->size[1];
        $newDiagonale = $width / $height;

        // le nouveau ratio est plus grand, donc on élargit l'image.
        // Donc on redimensionne la largeur pour rentrer dans $width, puis on applique le même ratio à la largeur
        if ($newDiagonale < $originalDiagonale) {

            // on calcul les ratio entre largeur actuelle et largeur voulue
            $ratioImage = $this->size[0] / $width;

            // on applique le meme ratio sur la hauteur (permet de conserver les proportions)
            $heightImage = floor($this->size[1] / $ratioImage);

            $offsetImage = floor(($height - $heightImage) / 2);

            imagecopyresampled($newImage , $this->image, 0, $offsetImage, 0, 0, $width, $heightImage, $this->size[0],$this->size[1]);


        } else {
            // on calcul les ratio entre largeur actuelle et largeur voulue
            $ratioImage = $this->size[1] / $height;

            // on applique le meme ratio sur la hauteur (permet de conserver les proportions)
            $widthImage = floor($this->size[0] / $ratioImage);

            $offsetImage = floor(($width - $widthImage) / 2);
            imagecopyresampled($newImage , $this->image, $offsetImage, 0, 0, 0, $widthImage, $height, $this->size[0],$this->size[1]);
        }

        $extension = self::getExtension($newFile);

        switch($extension) {
            case 'jpg':
            case 'jpeg':
                return imagejpeg($newImage, $newFile, $jpegQuality);
                break;
            case 'png':
                return imagepng($newImage, $newFile);
                break;
            case 'gif':
                return imagegif($newImage, $newFile);
        }

        return false;
    }

    public function getWidthedImage($width, $newFile, $jpegQuality = 100)
    {
        if (is_null($this->image)) {
            return false;
        }

        // si plus petite, alors on zappe
        if ($this->size[0] < $width) {
            $width = $this->size[0];
        }
        $ratio = $this->size[0] / $this->size[1];
        $newHeight = $width / $ratio;

        // on crée la nouvelle image
        $newImage = imagecreatetruecolor($width, $newHeight);

        // on crée un noir qui servira pour la transparence
        $colorImage = imagecolorallocate($newImage,0x00,0x00,0x00);

        // fond transparent
        imagefill($newImage, 0, 0, $colorImage);

        imagecopyresampled($newImage , $this->image, 0, 0, 0, 0, $width, $newHeight, $this->size[0],$this->size[1]);

        switch($this->extension) {
            case 'jpg':
            case 'jpeg':
                return imagejpeg($newImage, $newFile, $jpegQuality);
                break;
            case 'png':
                return imagepng($newImage, $newFile);
                break;
            case 'gif':
                return imagegif($newImage, $newFile);
        }

        return false;
    }

    public function getHeightedImage($height, $newFile, $jpegQuality = 100)
    {
        if (is_null($this->image)) {
            return false;
        }

        // si plus petite, alors on zappe
        if ($this->size[1] < $height) {
            $height = $this->size[1];
        }
        $ratio = $this->size[0] / $this->size[1];
        $newWidth = $height * $ratio;

        // on crée la nouvelle image
        $newImage = imagecreatetruecolor($newWidth, $height);

        // on crée un noir qui servira pour la transparence
        $colorImage = imagecolorallocate($newImage,0x00,0x00,0x00);

        // fond transparent
        imagefill($newImage, 0, 0, $colorImage);

        imagecopyresampled($newImage , $this->image, 0, 0, 0, 0, $newWidth, $height, $this->size[0],$this->size[1]);

        switch($this->extension) {
            case 'jpg':
            case 'jpeg':
                return imagejpeg($newImage, $newFile, $jpegQuality);
                break;
            case 'png':
                return imagepng($newImage, $newFile);
                break;
            case 'gif':
                return imagegif($newImage, $newFile);
        }

        return false;
    }

    public function avatarize($width, $height, $params, $newFile)
    {
        // on crée la nouvelle image
        $newImage = imagecreatetruecolor($width, $height);

        $newWidth = $params['x2'] - $params['x1'];
        $newHeight = $params['y2'] - $params['y1'];

        imagecopyresampled($newImage , $this->image, 0, 0, $params['x1'], $params['y1'], $width, $height, $newWidth, $newHeight);

        return imagepng($newImage, $newFile);
    }

    private function rgb2array($rgb)
    {
        return array(
            base_convert(substr($rgb, 0, 2), 16, 10),
            base_convert(substr($rgb, 2, 2), 16, 10),
            base_convert(substr($rgb, 4, 2), 16, 10),
        );
    }

    public static function getExtension($filename)
    {
        return substr(strrchr($filename, '.'), 1);
    }
}