<?php /* Master Modal Ver. : 1.2  */
class MasterModel extends CI_Model{

    /* Get Paging Rows */
    public function pagingRows($data){
        $draw = $data['draw'];
		$start = $data['start'];
		$rowperpage = $data['length']; // Rows display per page
		$searchValue = $data['search']['value'];		
		
		/********** Total Records without Filtering ***********/
		{
            if(isset($data['select'])):
                if(!empty($data['select'])):
                    $this->db->select($data['select']);
                endif;
            endif;
    
            if(isset($data['join'])):
                if(!empty($data['join'])):
                    foreach($data['join'] as $key=>$value):
                        $this->db->join($key,$value);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['leftJoin'])):
                if(!empty($data['leftJoin'])):
                    foreach($data['leftJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['where'])):
                if(!empty($data['where'])):
                    foreach($data['where'] as $key=>$value):
                        $this->db->where($key,$value);
                    endforeach;
                endif;            
            endif;
            if(isset($data['whereFalse'])):
                if(!empty($data['whereFalse'])):
                    foreach($data['whereFalse'] as $key=>$value):
                        $this->db->where($key,$value,false);
                    endforeach;
                endif;            
            endif;
            if(isset($data['customWhere'])):
                if(!empty($data['customWhere'])):
                    foreach($data['customWhere'] as $value):
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            $this->db->where($data['tableName'].'.is_delete',0);
    
            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;

            if(isset($data['where_not_in'])):
                if(!empty($data['where_not_in'])):
                    foreach($data['where_not_in'] as $key=>$value):
                        $this->db->where_not_in($key,$value,false);
                    endforeach;
                endif;
            endif;

    		if (isset($data['having'])) :
				if (!empty($data['having'])) :
					foreach ($data['having'] as $value) :
						$this->db->having($value);
					endforeach;
				endif;
			endif;

    		if(isset($data['group_by'])):
                if(!empty($data['group_by'])):
                    foreach($data['group_by'] as $key=>$value):
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;
    		
            $totalRecords = $this->db->get($data['tableName'])->num_rows();
            //print_r($this->db->last_query());
		}
        /********** End Count Total Records without Filtering ***********/
        
        
        
        /********** Count Total Records with Filtering ***********/
        {
    		if(isset($data['select'])):
                if(!empty($data['select'])):
                    $this->db->select($data['select']);
                endif;
            endif;
    
            if(isset($data['join'])):
                if(!empty($data['join'])):
                    foreach($data['join'] as $key=>$value):
                        $this->db->join($key,$value);
                    endforeach;
                endif;
            endif;
    
            if(isset($data['leftJoin'])):
                if(!empty($data['leftJoin'])):
                    foreach($data['leftJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['where'])):
                if(!empty($data['where'])):
                    foreach($data['where'] as $key=>$value):
                        $this->db->where($key,$value);
                    endforeach;
                endif;            
            endif;

			if(isset($data['whereFalse'])):
				if(!empty($data['whereFalse'])):
					foreach($data['whereFalse'] as $key=>$value):
						$this->db->where($key,$value);
					endforeach;
				endif;            
			endif;

            if(isset($data['customWhere'])):
                if(!empty($data['customWhere'])):
                    foreach($data['customWhere'] as $value):
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;
            $this->db->where($data['tableName'].'.is_delete',0);
    
            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;

            if(isset($data['where_not_in'])):
                if(!empty($data['where_not_in'])):
                    foreach($data['where_not_in'] as $key=>$value):
                        $this->db->where_not_in($key,$value,false);
                    endforeach;
                endif;
            endif;

    		if (isset($data['having'])) :
				if (!empty($data['having'])) :
					foreach ($data['having'] as $value) :
						$this->db->having($value);
					endforeach;
				endif;
			endif;
			
    		$c=0;
    		// General Search
    		if(!empty($searchValue)):
                if(isset($data['searchCol'])):
                    if(!empty($data['searchCol'])):
                        $this->db->group_start();
    						foreach($data['searchCol'] as $key=>$value):
    						    if(!empty($value)){
        							if($key == 0):
        								$this->db->like($value,str_replace(" ", "%", $searchValue),'both',false);
        							else:
        								$this->db->or_like($value,str_replace(" ", "%", $searchValue),'both',false);
        							endif;
    						    }
    						endforeach;
                        $this->db->group_end();
                    endif;
                endif;
    		endif;
    		
    		// Column Search
    		if(isset($data['searchCol'])):
    			if(!empty($data['searchCol'])):
    				foreach($data['searchCol'] as $key=>$value):
    					if(!empty($value)){
    						$csearch = $data['columns'][$key]['search']['value'];
    						if(!empty($csearch)){$this->db->like($value,$csearch);}
    					}
    				endforeach;
    			endif;
    		endif;
    		
    		if(isset($data['group_by'])):
                if(!empty($data['group_by'])):
                    foreach($data['group_by'] as $key=>$value):
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;
    		
    		$totalRecordwithFilter = $this->db->get($data['tableName'])->num_rows();
    		//print_r($this->db->last_query());
        }
        /********** End Count Total Records with Filtering ***********/
		
		
        /********** Total Records with Filtering ***********/
        {
            if(isset($data['select'])):
                if(!empty($data['select'])):
                    $this->db->select($data['select']);
                endif;
            endif;
    
            if(isset($data['join'])):
                if(!empty($data['join'])):
                    foreach($data['join'] as $key=>$value):
                        $this->db->join($key,$value);
                    endforeach;
                endif;
            endif;  
            
            if(isset($data['leftJoin'])):
                if(!empty($data['leftJoin'])):
                    foreach($data['leftJoin'] as $key=>$value):
                        $this->db->join($key,$value,'left');
                    endforeach;
                endif;
            endif;
    
            if(isset($data['where'])):
                if(!empty($data['where'])):
                    foreach($data['where'] as $key=>$value):
                        $this->db->where($key,$value);
                    endforeach;
                endif;            
            endif;

			if(isset($data['whereFalse'])):
				if(!empty($data['whereFalse'])):
					foreach($data['whereFalse'] as $key=>$value):
						$this->db->where($key,$value);
					endforeach;
				endif;            
			endif;
            
            if(isset($data['customWhere'])):
                if(!empty($data['customWhere'])):
                    foreach($data['customWhere'] as $value):
                        $this->db->where($value);
                    endforeach;
                endif;
            endif;

            $this->db->where($data['tableName'].'.is_delete',0);
    
            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;

            if(isset($data['where_not_in'])):
                if(!empty($data['where_not_in'])):
                    foreach($data['where_not_in'] as $key=>$value):
                        $this->db->where_not_in($key,$value,false);
                    endforeach;
                endif;
            endif;
    
    		$c=0;
    		// General Search
    		if(!empty($searchValue)):
                if(isset($data['searchCol'])):
                    if(!empty($data['searchCol'])):
                        $this->db->group_start();
                        foreach($data['searchCol'] as $key=>$value):
                            if(!empty($value)){
                                if($key == 0):
                                    $this->db->like($value,str_replace(" ", "%", $searchValue),'both',false);
                                else:
                                    $this->db->or_like($value,str_replace(" ", "%", $searchValue),'both',false);
                                endif;
                            }
                        endforeach;
                        $this->db->group_end();
                    endif;
                endif;
    		endif;
    		
    		// Column Search
    		if(isset($data['searchCol'])):
    			if(!empty($data['searchCol'])):
    				foreach($data['searchCol'] as $key=>$value):
    					if(!empty($value)){
    						$csearch = $data['columns'][$key]['search']['value'];
    						if(!empty($csearch)){$this->db->like($value,$csearch);}
    					}
    				endforeach;
    			endif;
    		endif;
            
            if(isset($data['order_by'])):
                if(!empty($data['order_by'])):
                    foreach($data['order_by'] as $key=>$value):
                        $this->db->order_by($key,$value);
                    endforeach;
                endif;
            endif;
    
            
    		if (isset($data['having'])) :
				if (!empty($data['having'])) :
					foreach ($data['having'] as $value) :
						$this->db->having($value);
					endforeach;
				endif;
			endif;


            if(isset($data['group_by'])):
                if(!empty($data['group_by'])):
                    foreach($data['group_by'] as $key=>$value):
                        $this->db->group_by($value);
                    endforeach;
                endif;
            endif;
    
            $resultData = $this->db->limit($rowperpage, $start)->get($data['tableName'])->result();
            //print_r($this->db->last_query());
        }
        /********** End Total Records with Filtering ***********/
        
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordwithFilter,
            "data" => $resultData
        ]; 
        return $response;
    }

    /* Get All Rows */
    public function rows($data){

        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;
        
        if(isset($data['whereFalse'])):
            if(!empty($data['whereFalse'])):
                foreach($data['whereFalse'] as $key=>$value):
                    $this->db->where($key,$value,false); 
                endforeach;
            endif;            
        endif;
        
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if(isset($data['where_not_in'])):
            if(!empty($data['where_not_in'])):
                foreach($data['where_not_in'] as $key=>$value):
                    $this->db->where_not_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if (isset($data['having'])) :
			if (!empty($data['having'])) :
				foreach ($data['having'] as $value) :
					$this->db->having($value);
				endforeach;
			endif;
		endif;

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value,'both',false);
                    else:
                        $this->db->or_like($key,$value,'both',false);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['columnSearch'])):
            if(!empty($data['columnSearch'])):                
                $this->db->group_start();
                foreach($data['columnSearch'] as $key=>$value):
                    $this->db->like($key,$value);
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['order_by_field'])):
            if(!empty($data['order_by_field'])):
                foreach($data['order_by_field'] as $key=>$value):
                    $this->db->order_by("FIELD(".$key.", ".implode(",",$value).")", '', false);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;

		if(isset($data['limit'])):
            if(!empty($data['limit'])):
                $this->db->limit($data['limit']);
            endif;
        endif;

        if(isset($data['start']) && isset($data['length'])):
            if(!empty($data['length'])):
                $this->db->limit($data['length'],$data['start']);
            endif;
        endif;
        
        if(isset($data['all'])):
            if(!empty($data['all'])):
                foreach($data['all'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        else:
            $this->db->where($data['tableName'].'.is_delete',0);
        endif;
        
        //$this->db->where($data['tableName'].'.is_delete',0);
        $result = $this->db->get($data['tableName'])->result();
        //print_r($this->db->last_query());
        return $result;
    }

    /* Get Single Row */
    public function row($data){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;

        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if(isset($data['where_not_in'])):
            if(!empty($data['where_not_in'])):
                foreach($data['where_not_in'] as $key=>$value):
                    $this->db->where_not_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value);
                    else:
                        $this->db->or_like($key,$value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if (isset($data['having'])) :
            if (!empty($data['having'])) :
                foreach ($data['having'] as $value) :
                    $this->db->having($value);
                endforeach;
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;

		if(isset($data['limit'])):
            if(!empty($data['limit'])):
                $this->db->limit($data['limit']);
            endif;
        endif;
		
		$result = $this->db->get($data['tableName'])->row();
 		//print_r($this->db->last_query());
        return $result;
    }

    /* Get Specific Row. Like : SUM,MAX,MIN,COUNT ect... */
    public function specificRow($data){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;

        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        
        $this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if(isset($data['where_not_in'])):
            if(!empty($data['where_not_in'])):
                foreach($data['where_not_in'] as $key=>$value):
                    $this->db->where_not_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value);
                    else:
                        $this->db->or_like($key,$value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if (isset($data['having'])) :
            if (!empty($data['having'])) :
                foreach ($data['having'] as $value) :
                    $this->db->having($value);
                endforeach;
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
            
        if(isset($data['resultType'])):
            if($data['resultType'] == "numRows")
                return $this->db->get($data['tableName'])->num_rows();            
            if($data['resultType'] == "resultRows")
                return $this->db->get($data['tableName'])->result();
        endif;

        $result =  $this->db->get($data['tableName'])->row();
		// print_r($this->db->last_query());
		return $result;
    }

    /* Save and Update Row */
    public function store($tableName,$data,$msg = "Record"){
        $id = $data['id'];
        unset($data['id']);
        if(empty($id)):
            $data['created_by'] = (isset($data['created_by']))?$data['created_by']:$this->loginId;
            $data['created_at'] = date("Y-m-d H:i:s");

            $this->db->insert($tableName,$data);
            $insert_id = $this->db->insert_id();
            $result = ['status'=>1,'message'=>$msg." saved Successfully.",'insert_id'=>$insert_id,'id'=>$insert_id];
        else:
            unset($data['created_by']);
            $data['updated_by'] = $this->loginId;
            $data['updated_at'] = date("Y-m-d H:i:s");
            
            $this->db->where('id',$id);
            $this->db->update($tableName,$data);
            $result = ['status'=>1,'message'=>$msg." updated Successfully.",'insert_id'=>-1,'id'=>$id];
        endif;

        return $result;
    }

    /* Update Row */
    public function edit($tableName,$where,$data,$msg = "Record"){
        $data['updated_by'] = $this->loginId;
        $data['updated_at'] = date("Y-m-d H:i:s");

        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
        $this->db->update($tableName,$data);
        return ['status'=>1,'message'=>$msg." updated Successfully.",'insert_id'=>-1];
    }

    /* Update Row */
    public function editCustom($tableName,$customWhere,$data,$where=Array()){
        $data['updated_by'] = $this->loginId;
        $data['updated_at'] = date("Y-m-d H:i:s");

        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;

		if(isset($customWhere)):
            if(!empty($customWhere)):
                foreach($customWhere as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->update($tableName,$data);
        return ['status'=>1,'message'=>"Record updated Successfully.",'insert_id'=>-1];
    }

    /* Set Deleteed Flage */
    public function trash($tableName,$where,$msg = "Record"){
        $data['updated_by'] = $this->loginId;
        $data['updated_at'] = date("Y-m-d H:i:s");
        $data['is_delete'] = 1;

        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
        $this->db->update($tableName,$data);
        return ['status'=>1,'message'=>$msg." deleted Successfully."];
    }

    /* Delete Recored Permanent */
    public function remove($tableName,$where,$msg = ""){
        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
        $this->db->delete($tableName);
        return ['status'=>1,'message'=>$msg." deleted Successfully."];
    }  
    
    /* Custom Set OR Update Row */
    public function setValue($data){
		if(!empty($data['where'])):
			if(isset($data['where'])):
				if(!empty($data['where'])):
					foreach($data['where'] as $key=>$value):
						$this->db->where($key,$value);
					endforeach;
				endif;            
			endif;

            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;

            if(isset($data['where_not_in'])):
                if(!empty($data['where_not_in'])):
                    foreach($data['where_not_in'] as $key=>$value):
                        $this->db->where_not_in($key,$value,false);
                    endforeach;
                endif;
            endif;

            if(isset($data['order_by'])):
                if(!empty($data['order_by'])):
                    foreach($data['order_by'] as $key=>$value):
                        $this->db->order_by($key,$value);
                    endforeach;
                endif;
            endif;
			
			if(isset($data['set'])):
				if(!empty($data['set'])):
					foreach($data['set'] as $key=>$value):
						$v = explode(',',$value);
						$setVal = "`".$v[0]."` ".$v[1];
						$this->db->set($key, $setVal, FALSE);
					endforeach;
				endif;            
			endif;

            if(isset($data['update'])):
				if(!empty($data['update'])):
					foreach($data['update'] as $key=>$value):
						$this->db->set($key, $value, FALSE);
					endforeach;
				endif;            
			endif;
            
            $this->db->update($data['tableName']);
            return ['status'=>1,'message'=>"Record updated Successfully.",'qry'=>$this->db->last_query()];
        endif;
		return ['status'=>0,'message'=>"Record updated Successfully.",'qry'=>"Query not fired"];
    }

	/* Print Executed Query */
    public function printQuery(){  print_r($this->db->last_query());exit; }	

	/* Company Information */
	public function getCompanyInfo(){
		$data['tableName'] = 'company_info';
        $data['select'] = "company_info.*,bcountry.name as company_country, bstate.name as company_state, bstate.gst_statecode as company_state_code, bcity.name as company_city, dcountry.name as delivery_country, dstate.name as delivery_state, dstate.gst_statecode as delivery_state_code, dcity.name as delivery_city";

        $data['leftJoin']['countries as bcountry'] = "company_info.company_country_id = bcountry.id";
        $data['leftJoin']['states as bstate'] = "company_info.company_state_id = bstate.id";
        $data['leftJoin']['cities as bcity'] = "company_info.company_city_id = bcity.id";

        $data['leftJoin']['countries as dcountry'] = "company_info.delivery_country_id = dcountry.id";
        $data['leftJoin']['states as dstate'] = "company_info.delivery_state_id = dstate.id";
        $data['leftJoin']['cities as dcity'] = "company_info.delivery_city_id = dcity.id";

		$data['where']['company_info.id'] = 1;
		return $this->row($data);
	}

    /* Save Comapny Information */
    public function saveCompanyInfo($postData){
        try{
            $this->db->trans_begin();

            $result = $this->store('company_info',$postData,'Company Info');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    /* 
    *   Created BY : Milan Chauhan
    *   Created AT : 05-05-2023
    *   Required Param : columnName (array)
    *   Not : if check any other condition on particular table and column then post data like this $data['table_condition']['{TABLE NAME}']['{CONDITION TYPE}']['{COLUMN NAME}'] = '{COLUMN VALUE}';
    *       CONDITION TYPE includs where,where_in and where_not_in
    */
    public function checkUsage($postData){
        if(!empty($postData['columnName'])):
            $columnName = implode("','",$postData['columnName']);
            $result = $this->db->query("SELECT DISTINCT TABLE_NAME,COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME IN ('$columnName') AND TABLE_SCHEMA='".MASTER_DB."'")->result();
            //print_r($result);exit;
            foreach($result as $row):
                $queryData = array();
                $queryData['tableName'] = $row->TABLE_NAME;
                $queryData['where'][$row->COLUMN_NAME] = $postData['value'];

                if(isset($postData['table_condition']) && !empty($postData['table_condition'])):
                    if(array_key_exists($row->TABLE_NAME, $postData['table_condition'])):

                        if(!empty($postData['table_condition'][$row->TABLE_NAME]['where']) && array_key_exists($row->COLUMN_NAME, $data['table_condition'][$row->TABLE_NAME]['where'])):
                            foreach($postData['table_condition'][$row->TABLE_NAME]['where'][$row->COLUMN_NAME] as $key=>$value):
                                $queryData['where'][$key] = $value;
                            endforeach;
                        endif;

                        if(!empty($postData['table_condition'][$row->TABLE_NAME]['where_in']) && array_key_exists($row->COLUMN_NAME, $postData['table_condition'][$row->TABLE_NAME]['where_in'])):
                            foreach($postData['table_condition'][$row->TABLE_NAME]['where_in'][$row->COLUMN_NAME] as $key=>$value):
                                $queryData['where_in'][$key] = $value;
                            endforeach;
                        endif;

                        if(!empty($postData['table_condition'][$row->TABLE_NAME]['where_not_in']) && array_key_exists($row->COLUMN_NAME, $postData['table_condition'][$row->TABLE_NAME]['where_not_in'])):
                            foreach($postData['table_condition'][$row->TABLE_NAME]['where_not_in'][$row->COLUMN_NAME] as $key=>$value):
                                $queryData['where_not_in'][$key] = $value;
                            endforeach;
                        endif;

                    endif;
                endif;

                $queryData['resultType'] = "numRows";
                $res = $this->specificRow($queryData);

                if($res > 0): break; endif;
            endforeach;
            //print_r($res);exit;
            if($res > 0): return true; endif;
        endif;
        return false;
    }
}
?>