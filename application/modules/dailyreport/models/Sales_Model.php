<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Sales_Model extends CI_Model {

		public function todayamount(){
			$today = date('Y-m-d');
			$this->db->select('SUM(totalamount) as amount');
			$this->db->from('customer_order');
			$this->db->where('order_date', $today);
			$this->db->where('order_status !=', 5);
			$query = $this->db->get();
        	if ($query->num_rows() > 0) {
            	$row = $query->row();
            	return $row->amount ?? 0; 
        	}
            return 0;
	    }


		public function employees(){
			$this->db->select(' e.emp_id, e.emp_name, r.emp_role_name , e.emp_salary');
			$this->db->from('emp_details e');
			$this->db->join('emp_role r', 'r.emp_role_id = e.emp_role');
			$this->db->get();
		}

		public function get_employee_salary($emp_id) {
        	$this->db->select('emp_salary');
        	$this->db->where('emp_id', $emp_id);
        	return $this->db->get('emp_details')->row()->emp_salary;
    	}



        public function todayorder(){
		$today=date('Y-m-d');
		$this->db->select('*');
        $this->db->from('customer_order');
		$this->db->where('order_date', $today);
		$this->db->where('order_status!=', 5);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();  
        }
            return 0;
	    }


		public function monthlysaleamount(){
				$year = date('Y');
				$month = date('m');
				$groupby = "GROUP BY YEAR(order_date), MONTH(order_date)";
				$amount = '';
				$wherequery = "YEAR(order_date)='$year' AND month(order_date)='$month' AND order_status!=5 GROUP BY YEAR(order_date), MONTH(order_date)";
				$this->db->select('SUM(totalamount) as amount');
				$this->db->from('customer_order');
				$this->db->where($wherequery, NULL, FALSE); 
				$query = $this->db->get();
				if ($query->num_rows() > 0) {
					$result = $query->result(); 
					foreach($result as $row){
						$amount .= $row->amount . ", ";
					}
					return trim($amount, ', ');
				}
				return 0;
			}

			public function monthlysaleorder() {
				$year = date('Y');
				$month = date('m');
				$totalorder = '';
				$wherequery = "YEAR(order_date)='$year' AND month(order_date)='$month' AND order_status!=5 GROUP BY YEAR(order_date), MONTH(order_date)";
				$this->db->select('COUNT(order_id) as totalorder');
				$this->db->from('customer_order');
				$this->db->where($wherequery, NULL, FALSE);
				$query = $this->db->get();
				if ($query->num_rows() > 0) {
				$result = $query->result();
				foreach ($result as $row) {
					$totalorder .= $row->totalorder . ", ";
				}
				return trim($totalorder, ', ');
				}
				return 0;
			}
	
    }
?>