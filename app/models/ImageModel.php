<?php

namespace App\Models;

use App\System\Model;
use DateTime;

class Image extends Model
{
    public int $id;
    public ?int $elementtypeno;
    public ?int $elementtypeid;
    public ?string $guuid;
    public ?string $path;
    public ?int $dimensions;
    public ?int $visibleorder;
    public ?string $title;
    public ?int $addid;
    public ?int $updateid;
    public ?int $active;



    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    public function saveImageFromUrl($url,$destinationFolder)
    {
        $imgUrl = $url;
        $fileInfo = pathinfo($imgUrl);
        $img = file_get_contents($imgUrl);
        $im = imagecreatefromstring($img);
        $originalWidth = imagesx($im);
        $originalHeight = imagesy($im);
        $resizePercentage = 0.5;
        $newWidth = $originalWidth * $resizePercentage;
        $newHeight = $originalHeight * $resizePercentage;
        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        if ($fileInfo['extension'] == 'jpg') {
            imagecopyresized($tmp, $im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            imagejpeg($tmp, $destinationFolder.'thump_'.$fileInfo['filename'].'.'.$fileInfo["extension"], -1);
            imagejpeg($im, $destinationFolder.$fileInfo['filename'] . '.' . $fileInfo["extension"],-1);
        }
        else if ($fileInfo['extension'] == 'png') {
            $background = imagecolorallocate($tmp , 0, 0, 0);
            imagecolortransparent($tmp, $background);
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            imagecopyresized($tmp, $im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            imagepng($tmp, 'thump_'.$fileInfo['filename'].'.'.$fileInfo["extension"]);
            imagepng($im, 'thump_'.$fileInfo['filename'].'.'.$fileInfo["extension"]);
        }
        else {
            // This image is neither a jpg or png
        }
        imagedestroy($tmp);
        imagedestroy($im);
        $this->path = $fileInfo['filename'] . '.' . $fileInfo["extension"];
        $this->insert();
    }
    public function saveImage($image)
    {
        if (is_array($image)) {
            $fileName = $image['tmp_name'];
            $sourceProperties = getimagesize($fileName);
            $resizeFileName = time() . explode('.',$image['name'])[0];
            $uploadPath = "assets/images/products/";
            $fileExt = pathinfo($image['name'], PATHINFO_EXTENSION);
            $uploadImageType = $sourceProperties[2];
            $sourceImageWidth = $sourceProperties[0];
            $sourceImageHeight = $sourceProperties[1];
            switch ($uploadImageType) {
                case IMAGETYPE_JPEG:
                    $resourceType = imagecreatefromjpeg($fileName);
                    $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight);
                    imagejpeg($imageLayer, $uploadPath . "thump_" . $resizeFileName . '.' . $fileExt);
                    break;

                case IMAGETYPE_GIF:
                    $resourceType = imagecreatefromgif($fileName);
                    $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight);
                    imagegif($imageLayer, $uploadPath . "thump_" . $resizeFileName . '.' . $fileExt);
                    break;

                case IMAGETYPE_PNG:
                    $resourceType = imagecreatefrompng($fileName);
                    $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight);
                    imagepng($imageLayer, $uploadPath . "thump_" . $resizeFileName . '.' . $fileExt);
                    break;

                default:
                    $imageProcess = 0;
                    break;
            }
            move_uploaded_file($fileName, $uploadPath . $resizeFileName . "." . $fileExt);
            $this->path = $resizeFileName . "." . $fileExt;
            $this->insert();
        }
    }

    public function insert()
    {

        $this->db->beginTransaction();
        try {
            $status = $this->db->prepare("INSERT INTO images 
            (elementtypeno, elementtypeid,guuid, `path`, dimensions, visibleorder, title, addid, addtime, updateid, updatetime, active) 
            VALUES (:elementtypeno, :elementtypeid,:guuid, :pathx, :dimensions, :visibleorder, :title, :addid, CURRENT_TIMESTAMP(), :updateid, CURRENT_TIMESTAMP(), 1)");
            $status->bindParam(':elementtypeno', $this->elementtypeno, ($this->elementtypeno == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':elementtypeid', $this->elementtypeid, ($this->elementtypeid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':guuid', $this->guuid, ($this->guuid == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':pathx', $this->path, ($this->path == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':dimensions', $this->dimensions, ($this->dimensions == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':visibleorder', $this->visibleorder, ($this->visibleorder == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':title', $this->title, ($this->title == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':addid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':updateid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->execute();
            $this->db->commit();
            $lastInsertId = $this->db->lastInsertId();
            $this->id = $lastInsertId;
        } catch (\Throwable $th) {
            $this->db->rollBack();
        }
    }



    public function update()
    {
        $this->db->beginTransaction();
        try {
            $status = $this->db->prepare("UPDATE images SET 
                elementtypeno = coalesce(:elementtypeno,elementtypeno),
                elementtypeid = coalesce(:elementtypeid,elementtypeid),
                `path`= coalesce(:pathx,`path`), 
                dimensions =coalesce(:dimensions,dimensions), 
                visibleorder =coalesce(:visibleorder,visibleorder), 
                title =coalesce(:title,title),
                updateid = :updateid, 
                updatetime = CURRENT_TIMESTAMP()
                where id = :id
        ");
            $status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':elementtypeno', $this->elementtypeno, ($this->elementtypeno == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':elementtypeid', $this->elementtypeid, ($this->elementtypeid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':pathx', $this->path, ($this->path == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':dimensions', $this->dimensions, ($this->dimensions == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':visibleorder', $this->visibleorder, ($this->visibleorder == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->bindParam(':title', $this->title, ($this->title == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
            $status->bindParam(':updateid', $this->updateid, ($this->updateid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
            $status->execute();
            $this->db->commit();
        } catch (\Throwable $th) {
            $this->db->rollBack();
        }
    }


    public function getFromGuuid($guuid)
    {
        $status = $this->db->prepare("select * from images where guuid = :guuid");
        $status->bindParam(':guuid', $guuid, ($guuid == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
        $status->execute();
        return $status->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getFromElementId($elementtypeno,$elementtypeid)
    {
        $status = $this->db->prepare("select * from images where elementtypeno = :elementtypeno and elementtypeid = :elementtypeid");
        $status->bindParam(':elementtypeno', $elementtypeno, ($elementtypeno == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
        $status->bindParam(':elementtypeid', $elementtypeid, ($elementtypeid == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
        $status->execute();
        return $status->fetchAll(\PDO::FETCH_ASSOC);
    }
        
    public function updateProductIdFroumGuuid()
    {
        $this->db->beginTransaction();
        try {
            $status = $this->db->prepare("UPDATE images set elementtypeid = :elementtypeid where guuid = :guuid");
            $status->bindParam(':elementtypeid', $this->elementtypeid);
            $status->bindParam(':guuid', $this->guuid);
            $status->execute();
            $this->db->commit();
        } catch (\Throwable $th) {
            $this->db->rollBack();
        }
    }


    function resizeImage($resourceType, $image_width, $image_height)
    {
        $resizeWidth = 100;
        $resizeHeight = 100;
        $imageLayer = imagecreatetruecolor($resizeWidth, $resizeHeight);
        imagecopyresampled($imageLayer, $resourceType, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $image_width, $image_height);
        return $imageLayer;
    }

    public function getFromCategorId($categoryid)
    {
        $status = $this->db->prepare(
            "SELECT * FROM images 
            where elementtypeno = 1 and
            elementtypeid in (SELECT id FROM products WHERE categoryid = :lcategoryid)"
        );
        $status->bindParam(":lcategoryid", $categoryid);
        $status->execute();
        return $status->fetchAll(\PDO::FETCH_ASSOC);
    }

    
}
