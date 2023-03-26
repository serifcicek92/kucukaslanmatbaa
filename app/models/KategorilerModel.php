<?php

namespace App\Models;

use App\System\Model;



class Kategoriler extends Model{

    

	function rules(): array {

	    return [];

        // return [

        //     'baslik'=>[self::RULE_REQUIRED],

        //     'metrekare'=>[self::RULE_REQUIRED],

        //     'mulktipi'=>[self::RULE_REQUIRED],

        //     'teklifturu'=>[self::RULE_REQUIRED],

        //     'adres'=>[self::RULE_REQUIRED],

        //     'il'=>[self::RULE_REQUIRED],

        //     'ilce'=>self::RULE_REQUIRED

        // ];

    }



    public function get($ustKategoriId = null,$url = null)

    {

        $status = $this->db->prepare("call sel_menuler(:lustid, :lurl)");

        $status->bindParam(':lustid',$ustKategoriId,($ustKategoriId == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));

        $status->bindParam(':lurl',$url,($url == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));

        $status->execute();

        return $status->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getSubCategories(){

        return $this->db->query("CALL sel_submenuler()")->fetchAll(\PDO::FETCH_ASSOC);

    }



    public function getParentTreeList($categoriid)

    {

        $status = $this->db->prepare("CALL get_parent_treelist(:lid)");

        $status->bindParam(':lid',$categoriid);

        $status->execute();

        return $status->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getChildrenTreeList($categoriid)

    {

        $status = $this->db->prepare("CALL sel_children_treelist(:lid)");

        $status->bindParam(':lid',$categoriid);

        $status->execute();

        return $status->fetchAll(\PDO::FETCH_ASSOC);

    }

}