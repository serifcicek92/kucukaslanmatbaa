<?php

namespace App\Models;



use App\System\Application;

use App\System\Model;

use Exception;

use PDO;



class SubjectModel extends Model

{



    public $author;

    public $title;

    public $subtitle;

    public $categoryId;

    public $content;

    public $url;

    

	/**

	 *

	 * @return array

	 */

	function rules(): array {

	return [

        'title'=>[self::RULE_REQUIRED,[self::RULE_MAX,'max'=>100]],

        'subtitle'=>[self::RULE_REQUIRED,[self::RULE_MAX,'max'=>150]],

        'categoryId'=>[self::RULE_REQUIRED],

        'content'=>[self::RULE_REQUIRED]

    ];

    }



    public function insert()

    {

        try {

            $this->url = Application::$app->functions->seoUrlOlustur($this->title);

            $this->author = $_SESSION["ESCLOGIN"]["USERID"];

            $status = $this->db->prepare("CALL in_subjects(:lcategoriid,:ltitle,:lsubtitle,:lcontent,:lekleyenid,:lurl)");

            $status->bindValue(':lcategoriid',$this->categoryId);

            $status->bindValue(':ltitle',$this->title);

            $status->bindValue(':lsubtitle',$this->subtitle);

            $status->bindValue(':lcontent',$this->content);

            $status->bindValue(':lekleyenid',$this->author);

            $status->bindValue(':lurl',$this->url);

            $status->execute();

            return true;

        } catch (\Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }



    public function update($url)

    {

        try {

            $this->url = Application::$app->functions->seoUrlOlustur($this->title);

            $this->author = $_SESSION["ESCLOGIN"]["USERID"];

            $status = $this->db->prepare("CALL up_subjects(:lcategoriid,:ltitle,:lsubtitle,:lcontent,:lguncelleyenid,:lurl,:loldlurl)");

            $status->bindValue(':lcategoriid',$this->categoryId);

            $status->bindValue(':ltitle',$this->title);

            $status->bindValue(':lsubtitle',$this->subtitle);

            $status->bindValue(':lcontent',$this->content);

            $status->bindValue(':lguncelleyenid',$this->author);

            $status->bindValue(':lurl',$this->url);

            $status->bindValue(':loldlurl',$url);

            $status->execute();

            return true;

        } catch (\Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }



    public function get($url)   

    {   

        try {

            $status = $this->db->prepare("CALL get_subject(null,null,:lurl)");

            $status->bindValue(':lurl',$url);

            $status->execute();

            return $status->fetch(\PDO::FETCH_OBJ);

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }

    public function getSubjectList($categoryUrl,$sayfa)

    {

        try {

            $status = $this->db->prepare("CALL get_subject_list(:categoryUrl,:lsayfa)");

            $status->bindValue(':categoryUrl',$categoryUrl);

            $status->bindValue(':lsayfa',$sayfa);

            $status->execute();

            return $status->fetchAll(\PDO::FETCH_ASSOC);

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }



    public function getFromUseridx($userid)   

    {   

        try {

            $status = $this->db->prepare("select * from users where id = :luserid");

            $status->bindValue(':luserid',$userid);

            $status->execute();

            return $status->fetchAll(\PDO::FETCH_ASSOC);

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return [];

        }

    }



    public function getAllSubject()

    {

        try {

            $status = $this->db->prepare("select * from subjects");

            $status->execute();

            return $status->fetchAll(\PDO::FETCH_ASSOC);

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }



    public function count($categoryUrl)

    {

        try {

            $status = $this->db->prepare("CALL count_subject(:categoryUrl)");

            $status->bindValue(':categoryUrl',$categoryUrl);

            $status->execute();

            return $status->fetch(\PDO::FETCH_OBJ);

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }

    public function count_comment($subjectid)

    {

        try {

            $status = $this->db->prepare("CALL count_comments(:lsubjectid)");

            $status->bindValue(':lsubjectid',$subjectid);

            $status->execute();

            return $status->fetch(\PDO::FETCH_OBJ);

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }



    public function getNewsSubject()

    {

        try {

            $status = $this->db->prepare("CALL get_subject_new()");

            $status->execute();

            return $status->fetchAll(\PDO::FETCH_ASSOC);

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }



    public function insertComments($values)

    {

        try {

            $status = $this->db->prepare(" CALL ifqesche_eschelping.in_comments(:lcommentref,:lsubjectid,:lcontent,:lekleyenid)");

            $status->bindValue(":lcommentref",$values["commentref"]);

            $status->bindValue(":lsubjectid",$values["subjectid"]);

            $status->bindValue(':lcontent',$values["content"]);

            $status->bindValue(':lekleyenid',$values["ekleyenid"]);

            $status->execute();

            return true;

        } catch (\Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }



    public function getComments($subjectid,$sayfa)

    {

        try {

            $status = $this->db->prepare("CALL sel_comments(:lsubjectid,:lsayfa)");

            $status->bindValue(':lsubjectid',$subjectid);

            $status->bindValue(':lsayfa',$sayfa);

            $status->execute();

            return $status->fetchAll(\PDO::FETCH_ASSOC);

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }



    public function aktif($url,$userid)

    {

        try {

            $status = $this->db->prepare("update subjects set guncelleyenid=:luserid, onay=1,guncellemezamani=current_timestamp where url=:lurl");

            $status->bindValue(':luserid',$userid);

            $status->bindValue(':lurl',$url);

            $status->execute();

            return true;

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }

    public function pasif($url,$userid)

    {

        try {

            $status = $this->db->prepare("update subjects set guncelleyenid=:luserid, onay=0,guncellemezamani=current_timestamp where url=:lurl");

            $status->bindValue(':luserid',$userid);

            $status->bindValue(':lurl',$url);

            $status->execute();

            return true;

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }



    public function del($url,$userid)

    {

        try {

            $status = $this->db->prepare("update subjects set guncelleyenid=:luserid, aktif=0,guncellemezamani=current_timestamp where url=:lurl");

            $status->bindValue(':luserid',$userid);

            $status->bindValue(':lurl',$url);

            $status->execute();

            return true;

        } catch (Exception $e) {

            $this->setError(true,$e->getMessage());

            return false;

        }

    }

}