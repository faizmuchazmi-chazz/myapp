<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Web_Model extends Crud_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = 'tmst_web';
		$this->colSearch = array('name', 'url', 'tag');
		$this->colOrder = ['order' => 'ASC', 'category' => 'ASC', 'name' => 'ASC'];
	}

	protected function list_query($where, $do_filter = true)
	{
		$this->db->select([
			$this->tableName . '.*',
		]);
		$this->db->from($this->tableName);

		parent::list_query($where, $do_filter);
	}

	function find($where = [], $offset = null, $limit = null)
	{
		$combined_query = "(SELECT tmst_web.id, tmst_web.name, tmst_web.url, tmst_web.tag, tmst_web.category, tmst_web.icon, tmst_web.order, tmst_web.description, tmst_web.icon_width, tmst_web.icon_height, tmst_web.is_active, 'web' AS source FROM tmst_web WHERE tmst_web.is_active = 1)";
		$result = $this->db->query($combined_query . " ORDER BY (CASE WHEN category='Socmed' THEN 0 WHEN category='Menu' THEN 1 WHEN category='Web' THEN 2 WHEN category='Menu' THEN 3 WHEN category='PTA Surabaya' THEN 4 WHEN category='Badilag' THEN 5 WHEN category='MA' THEN 6 WHEN category='Lain-lain' THEN 7 ELSE 99 END) ASC, (CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END) ASC, `order` ASC, `name` ASC")->result();

		// Apply limit and offset after combining
		if ($limit && $offset) {
			$result = array_slice($result, $offset, $limit);
		} else if ($limit) {
			$result = array_slice($result, 0, $limit);
		}

		return $result;
	}
}
