<?php

namespace App\Models;

use App\System\Model;
use DateTime;

class Product extends Model
{
	public ?int $id;
	public ?string $code;
	public ?int $categoryid;
	public ?int $priceid;
	public ?int $qualityid;
	public ?string $name;
	public ?string $content;
	public ?string $seourl;
	public ?int $addid;
	public ?int $updateid;
	public ?int $active;
	public ?int $taxtype;
	public ?float $prices;


	public function insert()
	{
		// LAST_INSERT_ID()+1
		try {
			$this->db->beginTransaction();
			$status = $this->db->prepare("INSERT INTO products (code, categoryid, qualityid, name, content, seourl, addid, addtime, updateid, updatetime, active )
					VALUES (:code, :categoryid, :qualityid, :name, :content, :seourl, :addid, CURRENT_TIMESTAMP(), :updateid, CURRENT_TIMESTAMP(), 1)");
			$status->bindParam(':code', $this->code, ($this->code == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':categoryid', $this->categoryid, ($this->categoryid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':qualityid', $this->qualityid, ($this->qualityid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':name', $this->name, ($this->name == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':content', $this->content, ($this->content == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':seourl', $this->seourl, ($this->seourl == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':addid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':updateid', $this->addid, ($this->addid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->execute();
			$lastInsertId = $this->db->lastInsertId();
			$this->id = $lastInsertId;
			$this->db->commit();
		} catch (\Throwable $th) {
			$this->db->rollBack();
		}
	}

	public function update()
	{
		try {
			$this->db->beginTransaction();
			$status = $this->db->prepare(
				"UPDATE products SET
				code = IFNULL(:code,code),
                categoryid = IFNULL(:categoryid,categoryid), 
                qualityid = IFNULL(:qualityid,qualityid), 
                name = IFNULL(:name,name),
				content = IFNULL(:content,content),
				seourl = IFNULL(:seourl,seourl),
                updateid = :updateid, 
                updatetime =CURRENT_TIMESTAMP()
			where id = :id");

			$status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':code', $this->code, ($this->code == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':categoryid', $this->categoryid, ($this->categoryid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':qualityid', $this->qualityid, ($this->qualityid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->bindParam(':name', $this->name, ($this->name == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':content', $this->content, ($this->content == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':seourl', $this->seourl, ($this->seourl == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
			$status->bindParam(':updateid', $this->updateid, ($this->updateid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
			$status->execute();
			$this->db->commit();
		} catch (\Throwable $th) {
			$this->db->rollBack();
		}
	}

	public function delete()
	{
		$this->db->beginTransaction();
		try {
			$status = $this->db->prepare("UPDATE products set active = 0 where id = :id");
			$status->bindParam(":id", $this->id, \PDO::PARAM_INT);
			$status->execute();
			$this->db->commit();
		} catch (\Throwable $th) {
			$this->db->rollBack();
		}
	}


	public function get()
	{
		$status = $this->db->prepare
		("	SELECT p.* FROM products p
			WHERE (:id is null or p.id = :id) and
				(:code is null or p.code = :code) and
				(:categoryid is null or p.categoryid = :categoryid) and
				(:seourl is null or p.seourl = :seourl) and
				active = 1");
		$status->bindParam(':id', $this->id, ($this->id == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->bindParam(':code', $this->categoryid, ($this->categoryid == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
		$status->bindParam(':categoryid', $this->categoryid, ($this->categoryid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));
		$status->bindParam(':seourl', $this->seourl, ($this->seourl == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);

	}

	public function getList(int $sayfa,int $limit){
		$statuscount = $this->db->prepare("select count(*) as total from products");
		$statuscount->execute();
		$total = $statuscount->fetchAll(\PDO::FETCH_ASSOC);

		$offset = ($limit * ($sayfa-1)) > $total[0]["total"] ? $total[0]["total"] : ($limit * ($sayfa-1));

		$status = $this->db->prepare(
			"SELECT  c.name as 'categoryname', 
					(SELECT p2.prices from productprices p2 where p2.productid =p.id order by p2.updatetime desc limit 1) as 'prices',
					(select p2.taxtype from productprices p2 order by p2.updatetime desc limit 1) as 'taxtype',
					(select i.`path`  from images i where i.elementtypeno = 1 and i.elementtypeid=p.id limit 1) as 'imagepath',
					p.* 
			from products p 
			left join categories c on c.id = p.categoryid
			left join (select p2.* from productprices p2 order by p2.updatetime desc limit 1) p3 on p3.productid = p.id
			limit :llimit OFFSET :loffset
					");
		$status->bindParam(":llimit", $limit, \PDO::PARAM_INT);
		$status->bindParam(":loffset", $offset, \PDO::PARAM_INT);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}

	public function getListView(int $sayfa,int $limit)
	{
		$statuscount = $this->db->prepare("select count(*) as total from products");
		$statuscount->execute();
		$total = $statuscount->fetchAll(\PDO::FETCH_ASSOC);

		$offset = ($limit * ($sayfa-1)) > $total[0]["total"] ? $total[0]["total"] : ($limit * ($sayfa-1));

		$status = $this->db->prepare(
			"SELECT  c.name as 'categoryname', 
					(SELECT p2.prices from productprices p2 where p2.productid =p.id order by p2.updatetime desc limit 1) as 'prices',
					(select p2.taxtype from productprices p2 order by p2.updatetime desc limit 1) as 'taxtype',
					(select GROUP_CONCAT(path, '') from images img where img.elementtypeid =p.id  group by img.elementtypeid)  as 'imagelist',
					p.* 
			from products p 
			left join categories c on c.id = p.categoryid
			left join (select p2.* from productprices p2 order by p2.updatetime desc limit 1) p3 on p3.productid = p.id
			where (:categoryid is null or p.categoryid = :categoryid)
			limit :llimit OFFSET :loffset
					");
		$status->bindParam(":categoryid", $this->categoryid);
		$status->bindParam(":llimit", $limit, \PDO::PARAM_INT);
		$status->bindParam(":loffset", $offset, \PDO::PARAM_INT);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}

	public function getProductDetail()
	{
		

		$status = $this->db->prepare(
			"SELECT  c.name as 'categoryname', 
					 c.seourl as 'catseourl',
					(SELECT p2.prices from productprices p2 where p2.productid =p.id order by p2.updatetime desc limit 1) as 'prices',
					(select p2.taxtype from productprices p2 order by p2.updatetime desc limit 1) as 'taxtype',
					(select GROUP_CONCAT(path, '') from images img where img.elementtypeid =p.id  group by img.elementtypeid)  as 'imagelist',
					p.* 
			from products p 
			left join categories c on c.id = p.categoryid
			left join (select p2.* from productprices p2 order by p2.updatetime desc limit 1) p3 on p3.productid = p.id
			where p.seourl = :seourl
					");
		$status->bindParam(':seourl', $this->seourl);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}


	public function getProductDetailFromProductId()
	{
		

		$status = $this->db->prepare(
			"SELECT  c.name as 'categoryname', 
					 c.seourl as 'catseourl',
					(SELECT p2.prices from productprices p2 where p2.productid =p.id order by p2.updatetime desc limit 1) as 'prices',
					(select p2.taxtype from productprices p2 order by p2.updatetime desc limit 1) as 'taxtype',
					(select GROUP_CONCAT(path, '') from images img where img.elementtypeid =p.id  group by img.elementtypeid)  as 'imagelist',
					p.* 
			from products p 
			left join categories c on c.id = p.categoryid
			left join (select p2.* from productprices p2 order by p2.updatetime desc limit 1) p3 on p3.productid = p.id
			where p.id = :id
					");
		$status->bindParam(':id', $this->id);
		$status->execute();
		return $status->fetchAll(\PDO::FETCH_OBJ);
	}

	/**
	 * @return array
	 */
	public function rules(): array
	{
		return [
			'name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 100]],
			'categoryid' => [self::RULE_REQUIRED]
		];
	}
}
