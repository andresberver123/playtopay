<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class MY_Model extends CI_Model
{
    protected $table;
    protected $slaves;
    protected $searchable_fields;

	function __construct()
	{
		parent::__construct();
                $this->table = NULL;
                $this->slaves = array();
                $this->searchable_fields = array();
	}

        /**
	 * Format the results of querys
         * 
	 * @param $objs: query dataset
	 * @return array of results
	 */
        protected function __format_results ($objs)
        {
            if ($this->table==NULL) return NULL;

            if($objs->num_rows() == 0) return array();
            else return $objs->result_array();
        }

         /**
	 * Save data related to the querys
         *
	 * @param $description: string
	 * @return 
	 */
        protected function __log ($description)
        {
            if ($this->table==NULL) return NULL;

            $id = $this->session->userdata('user_id');

            if (($id=='')||($id==null)) $id=0;

            $this->db->query ("INSERT INTO log (user_id, date, description) VALUES ($id, NOW(), '$description')");
        }

        /**
	 * Default List method
         *
         * @param <int> $id
         * @param <int> $limit
         * @param <int> $offset
         * @param <bool> $ordered
         * @return <array> list of rows
         */
	

        /**
	 * Default method to add new rows
	 * @param <array> $data
	 * @return bool or index of row inserted
	 */
        public function add ($data){
            if ($this->table==NULL) return FALSE;

            $r = $this->db->insert($this->table, $data);
            
            $id = $this->db->insert_id();

            $this->__log("insert on table: ".$this->table." inserted row id: $id params: data=$data");

            return $r ? $id : false;
        }

        /**
	 * Default method to update a row
         * @param int $id
	 * @param int $data
	 * @return bool
	 */
        public function update($id, $data)
	{
            if ($this->table==NULL) return FALSE;
            
            $r = $this->db->where('id', $id)->update($this->table, $data);

            $this->__log("update on table: ".$this->table." updated row id: $id params: data=$data");

            return $r ? true : false;
	}

        /**
	 *
	 * Default method to delete rows
         * @param int $id
	 * @return bool
	 */
	public function delete($id)
	{
            if ($this->table==NULL) return FALSE;
            
            $r = $this->db->where('id', $id)->delete($this->table);

            $stables = array_keys($this->slaves);

            foreach ($stables as $tn) $this->db->where($this->slaves[$tn], $id)->delete($tn);

            $this->__log("delete on table: ".$this->table." deleted row id: $id");

            return $r ? true : false;
	}

        /**
	 *
	 * Default method to search rows
         * @param array $keyword
         * @param bool $is_or
	 * @return bool
	 */
	public function search($keyword, $is_or = true, $limit = false, $offset = 0, $ordered = true, $table = false, $extraWhere = false)
	{
            if ($this->table==NULL) return NULL;

            if($ordered) $this->db->order_by('id', "desc");

            if($limit) $this->db->limit($limit, $offset);

            if($extraWhere) $this->db->where($extraWhere);

           $keywords = explode(' ', $keyword);

           $criteria = array();
           foreach ($this->searchable_fields as $sf)
              foreach ($keywords as $key)$criteria[$sf]=$key;
            
            if ($is_or) $this->db->or_like($criteria);
            else $this->db->like($criteria);
            
            if  (!$table) $objs = $this->db->from($this->table)->get();
            else $objs = $this->db->from($table)->distinct()->get();

            $this->__log("search on table: ".((!$table) ? $this->table : $table)." params: keyword=$keyword, is_or=$is_or");

            return $this->__format_results ($objs);
	}
}
?>
