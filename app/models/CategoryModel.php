<?php

namespace App\Models;

use App\System\Model;
use DateTime;

class Category extends Model
{


    public int $id;
    public int $code;
    public int $topid;
    public int $visibleorder;
    public string $name;
    public string $explain;
    public string $seourl;
    public int $addid;
    public DateTime $addtime;
    public int $updateid;
    public DateTime $updatetime;
    public int $active;

    function rules(): array
    {

        return [];

    }


    public function get($id = null, $topId = null, $url = null)
    {

        $status = $this->db->prepare("
        SELECT * FROM categories 
        where (:ltopid is null or topid = :ltopid ) and 
              (:lid is null or id = :lid) and
              (:lurl is null or seourl=:lurl)
        order by visibleorder");

        $status->bindParam(':lid', $id, ($id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
        $status->bindParam(':ltopid', $topId, ($topId == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
        $status->bindParam(':lurl', $url, ($url == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
        $status->execute();
        return $status->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getSubCategories()
    {

        return $this->db->query("CALL sel_submenuler()")->fetchAll(\PDO::FETCH_ASSOC);

    }



    public function getParentTreeList($categoriid)
    {

        $status = $this->db->prepare("
        with recursive  parenttree as (
            select id,name,topid,seourl, 1 as lvl
            from categories
            where id = :lid
            UNION ALL 
            select m2.id,m2.name,m2.topid,m2.seourl, t.lvl+1
            from categories m2
            inner join parenttree t on t.topid = m2.id
        )
        select * from parenttree;");

        $status->bindParam(':lid', $categoriid);

        $status->execute();

        return $status->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getChildrenTreeList($topId)
    {

        $status = $this->db->prepare("
            select * from categories where id in(
                with recursive  childrentree as (
                    select id,name,topid,seourl, 1 as lvl
                    from categories
                    where id = lparentid
                    UNION ALL 
                    select m2.id,m2.name,m2.topid,m2.seourl, t.lvl+1
                    from categories m2
                    inner join childrentree t on t.id = m2.topid 
                )
                select id from childrentree
            );
        ");

        $status->bindParam(':lparentid', $topId);
        $status->execute();

        return $status->fetchAll(\PDO::FETCH_ASSOC);

    }


}