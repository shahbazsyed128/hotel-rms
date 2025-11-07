<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Order extends MX_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->db->query('SET SESSION sql_mode = ""');
		$this->load->library('lsoft_setting');
		$this->load->model(array(
			'order_model',
			'logs_model'
		));
		$this->load->library('cart');
	}

	public function possetting()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('pos_setting');
		$saveid = $this->session->userdata('id');
		$data['possetting'] = $this->db->select('*')->from('tbl_posetting')->where('possettingid', 1)->get()->row();
		$data['quickorder'] = $this->db->select('*')->from('tbl_quickordersetting')->where('quickordid', 1)->get()->row();
		$data['module'] = "ordermanage";
		$data['page']   = "possetting";
		echo Modules::run('template/layout', $data);
	}
	public function settingenable()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$menuid = $this->input->post('menuid');
		$status = $this->input->post('status', true);
		$updatetready = array(
			$menuid           => $status
		);
		$this->db->where('possettingid', 1);
		$this->db->update('tbl_posetting', $updatetready);
	}
	public function quicksetting()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$menuid = $this->input->post('menuid');
		$status = $this->input->post('status', true);
		$updatetready = array(
			$menuid           => $status
		);
		$this->db->where('quickordid', 1);
		$this->db->update('tbl_quickordersetting', $updatetready);
	}

	// public function insert_customer()
	// {
	// 	$this->permission->method('ordermanage', 'create')->redirect();
	// 	$this->form_validation->set_rules('customer_name', 'Customer Name', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('email', display('email'), 'required');
	// 	$this->form_validation->set_rules('mobile', display('mobile'), 'required');
		
	// 	$savedid = $this->session->userdata('id');

	// 	$coa = $this->order_model->headcode();
	// 	if ($coa->HeadCode != NULL) {
	// 		$headcode = $coa->HeadCode + 1;
	// 	} else {
	// 		$headcode = "102030101";
	// 	}
	// 	$lastid = $this->db->select("*")->from('customer_info')
	// 		->order_by('cuntomer_no', 'desc')
	// 		->get()
	// 		->row();
	// 	$sl = $lastid->cuntomer_no;
	// 	if (empty($sl)) {
	// 		$sl = "cus-0001";
	// 	} else {
	// 		$sl = $sl;
	// 	}
	// 	$supno = explode('-', $sl);
	// 	$nextno = $supno[1] + 1;
	// 	$si_length = strlen((int)$nextno);

	// 	$str = '0000';
	// 	$cutstr = substr($str, $si_length);
	// 	$sino = $supno[0] . "-" . $cutstr . $nextno;


	// 	if ($this->form_validation->run()) {
	// 		$this->permission->method('ordermanage', 'create')->redirect();
	// 		$scan = scandir('application/modules/');
	// 		$pointsys = "";
	// 		foreach ($scan as $file) {
	// 			if ($file == "loyalty") {
	// 				if (file_exists(APPPATH . 'modules/' . $file . '/assets/data/env')) {
	// 					$pointsys = 1;
	// 				}
	// 			}
	// 		}
	// 		$data['customer']   = (object) $postData = array(
	// 			'cuntomer_no'     	=> $sino,
	// 			'membership_type'	=> $pointsys,
	// 			'customer_name'     	=> $this->input->post('customer_name', true),
	// 			'customer_email'     => $this->input->post('email', true),
	// 			'customer_phone'     => $this->input->post('mobile', true),
	// 			'customer_address'   => $this->input->post('address', true),
	// 			'favorite_delivery_address'     => $this->input->post('favaddress', true),
	// 			'is_active'        => 1,
	// 		);
	// 		$logData = array(
	// 			'action_page'         => "Add Customer",
	// 			'action_done'     	 => "Insert Data",
	// 			'remarks'             => "Customer is Created",
	// 			'user_name'           => $this->session->userdata('fullname'),
	// 			'entry_date'          => date('Y-m-d H:i:s'),
	// 		);

	// 		$c_name = $this->input->post('customer_name', true);
	// 		$c_acc = $sino . '-' . $c_name;
	// 		$createdate = date('Y-m-d H:i:s');
	// 		$data['aco']  = (object) $postData1 = array(
	// 			'HeadCode'         => $headcode,
	// 			'HeadName'         => $c_acc,
	// 			'PHeadName'        => 'Customer Receivable',
	// 			'HeadLevel'        => '4',
	// 			'IsActive'         => '1',
	// 			'IsTransaction'    => '1',
	// 			'IsGL'             => '0',
	// 			'HeadType'         => 'A',
	// 			'IsBudget'         => '0',
	// 			'IsDepreciation'   => '0',
	// 			'DepreciationRate' => '0',
	// 			'CreateBy'         => $savedid,
	// 			'CreateDate'       => $createdate,
	// 		);
	// 		$this->order_model->create_coa($postData1);
	// 		if ($this->order_model->insert_customer($postData)) {
	// 			$customerid = $this->db->select("*")->from('customer_info')->where('cuntomer_no', $sino)->get()->row();
	// 			if (!empty($pointsys)) {
	// 				$pointstable = array(
	// 					'customerid'   => $customerid,
	// 					'amount'       => 0,
	// 					'points'       => 10
	// 				);
	// 				$this->db->insert('tbl_customerpoint', $pointstable);
	// 			}
	// 			$this->logs_model->log_recorded($logData);
	// 			$this->session->set_flashdata('message', display('save_successfully'));
	// 			redirect('ordermanage/order/pos_invoice');
	// 		} else {
	// 			$this->session->set_flashdata('exception',  display('please_try_again'));
	// 		}
	// 		redirect("ordermanage/order/pos_invoice");
	// 	} else {
	// 		redirect("ordermanage/order/pos_invoice");
	// 	}
	// }



	public function insert_customer()
	{
		$this->permission->method('ordermanage', 'create')->redirect();
		$this->form_validation->set_rules('customer_name', 'Customer Name', 'required|max_length[100]');
		$this->form_validation->set_rules('email', display('email'), 'required');
		$this->form_validation->set_rules('mobile', display('mobile'), 'required');
		$this->form_validation->set_rules('customer_type', 'Customer Type', 'required|integer'); // New validation rule

		$savedid = $this->session->userdata('id');

		$coa = $this->order_model->headcode();
		if ($coa->HeadCode != NULL) {
			$headcode = $coa->HeadCode + 1;
		} else {
			$headcode = "102030101";
		}
		$lastid = $this->db->select("*")->from('customer_info')
			->order_by('cuntomer_no', 'desc')
			->get()
			->row();
		$sl = $lastid->cuntomer_no;
		if (empty($sl)) {
			$sl = "cus-0001";
		} else {
			$sl = $sl;
		}
		$supno = explode('-', $sl);
		$nextno = $supno[1] + 1;
		$si_length = strlen((int)$nextno);

		$str = '0000';
		$cutstr = substr($str, $si_length);
		$sino = $supno[0] . "-" . $cutstr . $nextno;

		if ($this->form_validation->run()) {
			$this->permission->method('ordermanage', 'create')->redirect();
			$scan = scandir('application/modules/');
			$pointsys = "";
			foreach ($scan as $file) {
				if ($file == "loyalty") {
					if (file_exists(APPPATH . 'modules/' . $file . '/assets/data/env')) {
						$pointsys = 1;
					}
				}
			}
			$data['customer'] = (object) $postData = array(
				'cuntomer_no' => $sino,
				'membership_type' => $pointsys,
				'customer_name' => $this->input->post('customer_name', true),
				'customer_email' => $this->input->post('email', true),
				'customer_phone' => $this->input->post('mobile', true),
				'customer_address' => $this->input->post('address', true),
				'favorite_delivery_address' => $this->input->post('favaddress', true),
				'is_active' => 1,
				'customer_type' => $this->input->post('customer_type', true), // New field
			);
			$logData = array(
				'action_page' => "Add Customer",
				'action_done' => "Insert Data",
				'remarks' => "Customer is Created",
				'user_name' => $this->session->userdata('fullname'),
				'entry_date' => date('Y-m-d H:i:s'),
			);

			$c_name = $this->input->post('customer_name', true);
			$c_acc = $sino . '-' . $c_name;
			$createdate = date('Y-m-d H:i:s');
			$data['aco'] = (object) $postData1 = array(
				'HeadCode' => $headcode,
				'HeadName' => $c_acc,
				'PHeadName' => 'Customer Receivable',
				'HeadLevel' => '4',
				'IsActive' => '1',
				'IsTransaction' => '1',
				'IsGL' => '0',
				'HeadType' => 'A',
				'IsBudget' => '0',
				'IsDepreciation' => '0',
				'DepreciationRate' => '0',
				'CreateBy' => $savedid,
				'CreateDate' => $createdate,
			);
			$this->order_model->create_coa($postData1);
			if ($this->order_model->insert_customer($postData)) {
				$customerid = $this->db->select("*")->from('customer_info')->where('cuntomer_no', $sino)->get()->row();
				if (!empty($pointsys)) {
					$pointstable = array(
						'customerid' => $customerid,
						'amount' => 0,
						'points' => 10
					);
					$this->db->insert('tbl_customerpoint', $pointstable);
				}
				$this->logs_model->log_recorded($logData);
				$this->session->set_flashdata('message', display('save_successfully'));
				redirect('ordermanage/order/pos_invoice');
			} else {
				$this->session->set_flashdata('exception', display('please_try_again'));
			}
			redirect("ordermanage/order/pos_invoice");
		} else {
			redirect("ordermanage/order/pos_invoice");
		}
	}

	public function insert_customerord()
	{
		$this->permission->method('ordermanage', 'create')->redirect();
		$this->form_validation->set_rules('customer_name', 'Customer Name', 'required|max_length[100]');
		$this->form_validation->set_rules('email', display('email'), 'required');
		$this->form_validation->set_rules('mobile', display('mobile'), 'required');
		$savedid = $this->session->userdata('id');

		$coa = $this->order_model->headcode();
		if ($coa->HeadCode != NULL) {
			$headcode = $coa->HeadCode + 1;
		} else {
			$headcode = "102030101";
		}
		$lastid = $this->db->select("*")->from('customer_info')
			->order_by('cuntomer_no', 'desc')
			->get()
			->row();
		$sl = $lastid->cuntomer_no;
		if (empty($sl)) {
			$sl = "cus-0001";
		} else {
			$sl = $sl;
		}
		$supno = explode('-', $sl);
		$nextno = $supno[1] + 1;
		$si_length = strlen((int)$nextno);

		$str = '0000';
		$cutstr = substr($str, $si_length);
		$sino = $supno[0] . "-" . $cutstr . $nextno;

		if ($this->form_validation->run()) {

			$this->permission->method('ordermanage', 'create')->redirect();
			$data['customer']   = (object) $postData = array(
				'cuntomer_no'     	=> $sino,
				'customer_name'     	=> $this->input->post('customer_name', true),
				'customer_email'     => $this->input->post('email', true),
				'customer_phone'     => $this->input->post('mobile', true),
				'customer_address'   => $this->input->post('address', true),
				'favorite_delivery_address'     => $this->input->post('favaddress', true),
				'is_active'        => 1,
			);
			$logData = array(
				'action_page'         => "Add Customer",
				'action_done'     	 => "Insert Data",
				'remarks'             => "Customer is Created",
				'user_name'           => $this->session->userdata('fullname'),
				'entry_date'          => date('Y-m-d H:i:s'),
			);
			$c_name = $this->input->post('customer_name', true);
			$c_acc = $sino . '-' . $c_name;
			$createdate = date('Y-m-d H:i:s');
			$data['aco']  = (object) $postData1 = array(
				'HeadCode'         => $headcode,
				'HeadName'         => $c_acc,
				'PHeadName'        => 'Customer Receivable',
				'HeadLevel'        => '4',
				'IsActive'         => '1',
				'IsTransaction'    => '1',
				'IsGL'             => '0',
				'HeadType'         => 'A',
				'IsBudget'         => '0',
				'IsDepreciation'   => '0',
				'DepreciationRate' => '0',
				'CreateBy'         => $savedid,
				'CreateDate'       => $createdate,
			);
			$this->order_model->create_coa($postData1);
			if ($this->order_model->insert_customer($postData)) {
				$this->logs_model->log_recorded($logData);
				$this->session->set_flashdata('message', display('save_successfully'));
				redirect('ordermanage/order/neworder');
			} else {
				$this->session->set_flashdata('exception',  display('please_try_again'));
			}
			redirect("ordermanage/order/neworder");
		} else {
			redirect("ordermanage/order/neworder");
		}
	}
	public function getcustomerdiscount($cid)
	{
		$settinginfo = $this->order_model->settinginfo();
		$customerinfo = $this->order_model->read('*', 'customer_info', array('customer_id' => 1));
		$mtype = $this->order_model->read('*', 'membership', array('id' => $customerinfo->membership_type));
		if ($settinginfo->discount_type == 0) {
		}
	}
	public function neworder($id = null)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('add_order');
		$saveid = $this->session->userdata('id');
		$data['intinfo'] = "";

		$data['categorylist']   = $this->order_model->category_dropdown();
		$data['curtomertype']   = $this->order_model->ctype_dropdown();
		$data['waiterlist']     = $this->order_model->waiter_dropdown();
		$data['tablelist']     = $this->order_model->table_dropdown();
		$data['customerlist']   = $this->order_model->customer_dropdown();
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);

		$data['module'] = "ordermanage";
		$data['page']   = "addorder";
		echo Modules::run('template/layout', $data);
	}
	public function pos_invoice()
	{
		if ($this->permission->method('ordermanage', 'create')->access() == FALSE) {
			redirect('dashboard/home');
		}

		$data['title'] = "posinvoiceloading";
		$saveid = $this->session->userdata('id');
		$data['categorylist']  = $this->order_model->category_dropdown();
		$data['allcategorylist']  = $this->order_model->allcat_dropdown();
		$data['customerlist']  = $this->order_model->customer_dropdown();
		$data['paymentmethod'] = $this->order_model->pmethod_dropdown();
		$data['curtomertype']  = $this->order_model->ctype_dropdown();
		$data['thirdpartylist']  = $this->order_model->thirdparty_dropdown();
		$data['banklist']      = $this->order_model->bank_dropdown();
		$data['terminalist']   = $this->order_model->allterminal_dropdown();
		$data['waiterlist']    = $this->order_model->waiter_dropdown();
		$data['tablelist']     = $this->order_model->table_dropdown();
		$data['itemlist']      =  $this->order_model->allfood2();
		$data['ongoingorder']  = $this->order_model->get_ongoingorder();
		$data['possetting'] = $this->order_model->read('*', 'tbl_posetting', array('possettingid' => 1));
		$data['possetting2'] = $this->order_model->read('*', 'tbl_quickordersetting', array('quickordid' => 1));
		$data['soundsetting'] = $this->order_model->read('*', 'tbl_soundsetting', array('soundid' => 1));
		$settinginfo = $this->order_model->settinginfo();
		$data['cashinfo'] = $this->db->select('*')->from('tbl_cashregister')->where('userid', $saveid)->where('status', 0)->order_by('id', 'DESC')->get()->row();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "posorder";
		echo Modules::run('template/layout', $data);
	}
	public function getongoingorder($id = null, $table = null)
	{
		if ($id == null) {
			$data['ongoingorder']  = $this->order_model->get_ongoingorder();
		} else {
			if (empty($table)) {
				$data['ongoingorder']  = $this->order_model->get_unique_ongoingorder_id($id);
			} else {
				$data['ongoingorder']  = $this->order_model->get_unique_ongoingtable_id($id);
			}
		}
		$this->load->view('ongoingorder_ajax', $data);
	}
	public function kitchenstatus()
	{
		$data['kitchenorder']  = $this->order_model->get_orderlist();
		$this->load->view('kitchen_ajax', $data);
	}
	public function itemlist()
	{
		$orderid = $this->input->post('orderid');
		$data['itemlist']  = $this->order_model->get_itemlist($orderid);
		$data['allcancelitem'] = $this->order_model->get_cancelitemlist($orderid);
		$this->load->view('item_ajax', $data);
	}
	public function showtodayorder()
	{
		$this->load->view('todayorder');
	}

	public function showtodayguestorder()
	{
		$this->load->view('todayguestorder');
	}

	public function showtodayemployeeorder()
	{
		$this->load->view('todayemployeeorder');
	}

	public function showtodayemployeeorder2()
	{
		$this->load->view('todayemployeeorder2');
	}

	public function showtodaycharityorder()
	{
		$this->load->view('todaycharityorder');
	}

	public function showtodaymanageexpenses()
	{	
		$categories = $this->order_model->get_categories();
		$this->load->view('todaymanageexpenses', compact('categories'));
	}

	public function addcategory(){
		$category_name = $this->input->get('category_name');
		if ($category_name) {
			$this->order_model->add_category($category_name);
			echo json_encode(['success' => true, 'message' => 'Category added successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Invalid category name.']);
		}
	}

	public function getcategories(){
		$categories = $this->order_model->get_categories();
		echo json_encode($categories);
	}

	public function getCategoryEntities(){
		$category_id = $this->input->get('category_id');
		if ($category_id) {
			$entities = $this->order_model->get_entities_by_category($category_id);
			echo json_encode($entities);
		} else {
			echo json_encode([]);
		}
	}


	public function addCategoryEntity(){
		$category_id = $this->input->get('category_id');
		$entity_name = $this->input->get('name');
		$item_name = $this->input->get('item_name');
		$price = $this->input->get('price');
		$unit = $this->input->get('unit');

		if ($category_id && $entity_name) {
			$data = [
				'category_id' => $category_id,
				'user_id'=> null,
				'employee_his_id'=> null,
				'customer_id'=> null,
				'entity_name' => $entity_name,
				'contact_info'=> null,
			];
			$this->order_model->add_category_entity($data);
			$entity_id = $this->db->insert_id();

			$item_data = [
				'entity_id' => $entity_id,
				'item_name' => $item_name,
				'unit' => $unit,
				'price' => $price,	
			];

			$this->order_model->add_entity_item_rate($item_data);
			
			echo json_encode(['success' => true, 'message' => 'Entity added successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Invalid input.']);
		}
	}


	public function addexpense(){

		$category_id = $this->input->get('category_id');
		$entity_id = $this->input->get('entity_id');
		$rate_id = $this->input->get('rate_id');
		$product_id = $this->input->get('product_id');
		$price = $this->input->get('rate');
		$quantity = $this->input->get('qty');
		$total_amount = $this->input->get('amount');
		$description = $this->input->get('description');
		$reason = $this->input->get('reason');
		$status = 1;
		$created_at = date('Y-m-d H:i:s');

		// Validation: description and reason can be null, but if set, must be string
		if ($description !== null && !is_string($description)) {
			$errors[] = 'Description must be a string or null.';
		}
		if ($reason !== null && !is_string($reason)) {
			$errors[] = 'Reason must be a string or null.';
		}

		if(!$category_id || !is_numeric($category_id) || $category_id <= 0){
			$errors[] = 'Invalid category ID.';
		}
		if(!$entity_id || !is_numeric($entity_id) || $entity_id <= 0){
			$errors[] = 'Invalid entity ID.';
		}
		if(!$rate_id || !is_numeric($rate_id) || $rate_id <= 0){
			$errors[] = 'Invalid rate ID.';
		}
		if(!$price || !is_numeric($price) || $price < 0){
			$errors[] = 'Invalid price.';
		}
		if(!$quantity || !is_numeric($quantity) || $quantity <= 0){
			$errors[] = 'Invalid quantity.';
		}
		if(!$total_amount || !is_numeric($total_amount) || $total_amount < 0){
			$errors[] = 'Invalid total amount.';
		}


		$errors = [];

		if (empty($errors)) {
			$data = [
				'category_id' => (int)$category_id,
				'entity_id' => (int)$entity_id,
				'rate_id' => (int)$rate_id,
				'product_id' => (int)$product_id,
				'price' => (float)$price,
				'quantity' => (float)$quantity,
				'total_amount' => (float)$total_amount,
				'description' => $description,
				'status' => 1,
				'reason' => null,
				'expense_date' => date('Y-m-d'),
				'created_at' => date('Y-m-d H:i:s'),
			];
			$this->order_model->add_expense($data);
			echo json_encode(['success' => true, 'message' => 'Expense added successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
		}
	}

	public function get_expenses(){
		$saveid = $this->session->userdata('id');
		$checkuser = $this->db->select('*')->from('tbl_cashregister')->where('userid', $saveid)->where('status', 0)->order_by('id', 'DESC')->get()->row();
		if(!$checkuser){
			echo json_encode(['success' => false, 'message' => 'No open cash register found for the user.','error_code' => 4,]);
			return;
		}
		$expenses = $this->order_model->get_expenses($checkuser->opendate);
		echo json_encode($expenses);
	}


	// public function addProduct() {
	// 	$entity_id = $this->input->get('entity_id');
	// 	$product_name = $this->input->get('product_name');
	// 	$sale_price = $this->input->get('price');
	// 	$unit = $this->input->get('unit');

	// 	if ($entity_id && $product_name && $sale_price) {
	// 		// Insert new product into the products table
	// 		$data = [
	// 			'entity_id' => $entity_id,
	// 			'product_name' => $product_name,
	// 			'unit' => $unit,
	// 			'created_at' => date('Y-m-d H:i:s')
	// 		];
			
	// 		$this->db->insert('products', $data);
	// 		$product_id = $this->db->insert_id();

	// 		// Insert product price into the product_prices table
	// 		$price_data = [
	// 			'product_id' => $product_id,
	// 			'sale_price' => $sale_price,
	// 			'purchase_price' => 0, // Assuming purchase price is not provided
	// 			'valid_from' => date('Y-m-d H:i:s'),
	// 			'valid_to' => NULL,
	// 			'created_at' => date('Y-m-d H:i:s')
	// 		];

	// 		$this->db->insert('product_prices', $price_data);

	// 		echo json_encode(['success' => true, 'message' => 'Product added successfully.']);
	// 	} else {
	// 		echo json_encode(['success' => false, 'message' => 'Invalid input.']);
	// 	}
	// }

	// public function updateProduct() {
	// 	$product_id = $this->input->get('product_id');
	// 	$product_name = $this->input->get('product_name');
	// 	$new_sale_price = $this->input->get('price');
	// 	$unit = $this->input->get('unit');

	// 	if ($product_id && $product_name && $new_sale_price) {
	// 		// Fetch the current product data
	// 		$product = $this->db->get_where('products', ['product_id' => $product_id])->row();

	// 		// If the product exists, update the name and insert a new price
	// 		if ($product) {
	// 			// 1. Update the product name and unit
	// 			$update_product_data = [
	// 				'product_name' => $product_name,
	// 				'unit' => $unit,  // Optional: Update the unit if provided
	// 			];

	// 			$this->db->where('product_id', $product_id)
	// 					->update('products', $update_product_data);

	// 			// 2. Close the previous price by setting `valid_to` to the current timestamp
	// 			$current_price = $this->db->order_by('valid_from', 'DESC')
	// 									->get_where('product_prices', ['product_id' => $product_id, 'valid_to' => NULL])
	// 									->row();

	// 			if ($current_price) {
	// 				$this->db->where('price_id', $current_price->price_id)
	// 						->update('product_prices', ['valid_to' => date('Y-m-d H:i:s')]);

	// 				// 3. Insert the new price record with the new sale price
	// 				$price_data = [
	// 					'product_id' => $product_id,
	// 					'sale_price' => $new_sale_price,
	// 					'purchase_price' => 0,  // Assuming purchase price is not provided
	// 					'valid_from' => date('Y-m-d H:i:s'),
	// 					'valid_to' => NULL,  // The new price is valid indefinitely until the next price change
	// 					'created_at' => date('Y-m-d H:i:s')
	// 				];

	// 				$this->db->insert('product_prices', $price_data);

	// 				echo json_encode(['success' => true, 'message' => 'Product name and price updated successfully.']);
	// 			} else {
	// 				echo json_encode(['success' => false, 'message' => 'Current price not found for this product.']);
	// 			}
	// 		} else {
	// 			echo json_encode(['success' => false, 'message' => 'Product not found.']);
	// 		}
	// 	} else {
	// 		echo json_encode(['success' => false, 'message' => 'Invalid input.']);
	// 	}
	// }

	public function deleteProduct() {
		$product_id = $this->input->get('product_id');

		if ($product_id && is_numeric($product_id) && $product_id > 0) {
			// Call model method to delete product
			$this->order_model->delete_product($product_id);
			echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
		}
	}

	// Add new product

	public function addProduct() {
        $entity_id = $this->input->get('entity_id');
        $product_name = $this->input->get('product_name');
        $sale_price = $this->input->get('price');
        $unit = $this->input->get('unit');

        if ($entity_id && $product_name && $sale_price) {
            // Call model method to add product
            $product_id = $this->order_model->addProduct($entity_id, $product_name, $sale_price, $unit);

            if ($product_id) {
                echo json_encode(['success' => true, 'message' => 'Product added successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding product.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        }
    }

    // Update product name and price
    public function updateProduct() {
        $product_id = $this->input->get('product_id');
        $product_name = $this->input->get('product_name');
        $new_sale_price = $this->input->get('price');
        $unit = $this->input->get('unit');

        if ($product_id && $product_name && $new_sale_price) {
            // Call model method to update product
            $updated = $this->order_model->updateProduct($product_id, $product_name, $new_sale_price, $unit);

            if ($updated) {
                echo json_encode(['success' => true, 'message' => 'Product name and price updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating product.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        }
    }

	public function getProductsByEntity()
	{
		// Make sure we always send JSON
		$this->output->set_content_type('application/json');

		$entity_id = (int)$this->input->get('entity_id');
		if (!$entity_id) {
			return $this->output->set_output(json_encode([]));
		}

		$products = $this->order_model->get_products_by_entity($entity_id);
		return $this->output->set_output(json_encode($products));
	}


	public function deleteexpense(){
		$expense_id = $this->input->get('expense_id');
		$reason = $this->input->get('reason');
		if ($expense_id && is_numeric($expense_id) && $expense_id > 0 && !empty($reason)) {
			
			$this->order_model->delete_expense($expense_id, $reason);
			echo json_encode(['success' => true, 'message' => 'Expense deleted successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Invalid expense ID or reason is required.']);
		}
	}


	public function showonlineorder()
	{
		$this->load->view('onlineordertable');
	}
	public function showqrorder()
	{
		$this->load->view('qrordertable');
	}
	public function ongoingtable_name()
	{
		$name = $this->input->get('q');
		$tablewiseorderdetails  = $this->order_model->get_unique_ongoingorder($name);

		echo json_encode($tablewiseorderdetails);
	}
	public function ongoingtablesearch()
	{
		$name = $this->input->get('q');
		$tablewiseorderdetails  = $this->order_model->get_unique_ongoingtable($name);
		echo json_encode($tablewiseorderdetails);
	}
	public function getitemlist()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('supplier_edit');
		$prod = $this->input->post('product_name', true);
		$isuptade = $this->input->post('isuptade', true);
		$catid = $this->input->post('category_id');
		$getproduct = $this->order_model->searchprod($catid, $prod);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		if (!empty($getproduct)) {
			$data['itemlist'] = $getproduct;
			$data['module'] = "ordermanage";
			if ($isuptade == 1) {
				$data['page']   = "getfoodlistup";
				$this->load->view('ordermanage/getfoodlistup', $data);
			} else {
				$data['page']   = "getfoodlist";
				$this->load->view('ordermanage/getfoodlist', $data);
			}
		} else {
			echo 420;
		}
	}
	public function getitemlistdroup()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$prod = $this->input->get('q');
		$getproduct = $this->order_model->searchdropdown($prod);
		echo json_encode($getproduct);
	}
	public function getitemdata()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('supplier_edit');
		$prod = $this->input->post('product_id');
		$getproduct  = $this->order_model->productinfo($prod);
		return json_encode($getproduct);
	}
	public function itemlistselect()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('supplier_edit');
		$id = $this->input->post('id');
		$data['itemlist']   = $this->order_model->findById($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "foodlist";
		$this->load->view('ordermanage/foodlist', $data);
	}
	public function addtocart()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$catid = $this->input->post('catid');
		$pid = $this->input->post('pid');
		$sizeid = $this->input->post('sizeid');
		$isgroup = $this->input->post('isgroup');
		$myid = $catid . $pid . $sizeid;
		$itemname = $this->input->post('itemname', true);
		$size = $this->input->post('varientname', true);
		$qty = $this->input->post('qty', true);
		$price = $this->input->post('price', true);
		$addonsid = $this->input->post('addonsid');
		$allprice = $this->input->post('allprice', true);
		$adonsunitprice = $this->input->post('adonsunitprice', true);
		$adonsqty = $this->input->post('adonsqty', true);
		$adonsname = $this->input->post('adonsname', true);
		if (empty($isgroup)) {
			$isgroup1 = 0;
		} else {
			$isgroup1 = $this->input->post('isgroup', true);
		}

		if (!empty($addonsid)) {
			$aids = $addonsid;
			$aqty = $adonsqty;
			$aname = $adonsname;
			$aprice = $adonsunitprice;
			$atprice = $allprice;
			$grandtotal = $price;
		} else {
			$grandtotal = $price;
			$aids = '';
			$aqty = '';
			$aname = '';
			$aprice = '';
			$atprice = '0';
		}

		$data_items = array(
			'id'      	=> $myid,
			'pid'     	=> $pid,
			'name'    	=> $itemname,
			'sizeid'    	=> $sizeid,
			'isgroup'    => $isgroup1,
			'size'    	=> $size,
			'qty'     	=> $qty,
			'price'   	=> $grandtotal,
			'addonsid'   => $aids,
			'addonname'  => $aname,
			'addonupr'   => $aprice,
			'addontpr'   => $atprice,
			'addonsqty'  => $aqty,
			'itemnote'	=> ""
		);

		$this->cart->insert($data_items);

		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "cartlist";
		$this->load->view('ordermanage/cartlist', $data);
	}
	public function srcposaddcart($pid = null)
	{
		$insert_new = TRUE;
		$bag = $this->cart->contents();
		$getproduct = $this->order_model->getuniqeproduct($pid);
		$this->db->select('*');
		$this->db->from('menu_add_on');
		$this->db->where('menu_id', $pid);
		$query = $this->db->get();

		$getadons = "";
		if ($query->num_rows() > 0 || $getproduct->is_customqty == 1) {
			$getadons = 1;
		} else {
			$getadons =  0;
		}
		foreach ($bag as $item) {

			// check product id in session, if exist update the quantity
			if ($item['pid'] == $pid) { // Set value to your variable
				if ($getadons == 0) {

					echo 'adons';
					exit;

					// set $insert_new value to False
					$insert_new = FALSE;
				} else {
					echo 'adons';
					exit;
				}
				break;
			}
		}
		if ($insert_new) {
			$this->permission->method('ordermanage', 'read')->redirect();
			$pid = $getproduct->ProductsID;
			$catid = $getproduct->CategoryID;
			$sizeid = $getproduct->variantid;;
			$myid = $catid . $pid . $sizeid;
			$itemname = $getproduct->ProductName . '-' . $getproduct->itemnotes;
			$size = $getproduct->variantName;
			$qty = 1;
			$price = isset($getproduct->price) ? $getproduct->price : 0;



			if ($getadons == 0) {
				$grandtotal = $price;
				$aids = '';
				$aqty = '';
				$aname = '';
				$aprice = '';
				$atprice = '0';
			} else {

				echo 'adons';
				exit;
			}

			$data_items = array(
				'id'      	=> $myid,
				'pid'     	=> $pid,
				'name'    	=> $itemname,
				'sizeid'    	=> $sizeid,
				'size'    	=> $size,
				'qty'     	=> $qty,
				'price'   	=> $grandtotal,
				'addonsid'   => $aids,
				'addonname'  => $aname,
				'addonupr'   => $aprice,
				'addontpr'   => $atprice,
				'addonsqty'  => $aqty,
				'itemnote'	=> ""
			);
			//print_r($data_items);

			//$this->cart->insert($data_items);
		}
		$this->permission->method('ordermanage', 'read')->redirect();
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "cartlist";
		$this->load->view('ordermanage/poscartlist', $data);
	}
	/*show adons product*/
	public function adonsproductadd($id = null)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$getproduct = $this->order_model->getuniqeproduct($id);
		$data['item']         = $this->order_model->findid($getproduct->ProductsID, $getproduct->variantid);
		$data['addonslist']   = $this->order_model->findaddons($getproduct->ProductsID);
		$data['varientlist']   = $this->order_model->findByvmenuId($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "posaddonsfood";
		$this->load->view('ordermanage/posaddonsfood', $data);
	}
	public function additemnote()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$foodnote = $this->input->post('foodnote', true);
		$rowid = $this->input->post('rowid', true);
		$qty = $this->input->post('qty', true);
		$data = array(
			'rowid'    => $rowid,
			'qty'      => $qty,
			'itemnote' => $foodnote
		);
		$this->cart->update($data);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['module'] = "ordermanage";
		$data['page']   = "poscartlist";
		$this->load->view('ordermanage/poscartlist', $data);
	}
	public function addnotetoupdate()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$foodnote = $this->input->post('foodnote', true);
		$rowid = $this->input->post('rowid', true);
		$orderid = $this->input->post('orderid', true);
		$group = $this->input->post('group', true);
		$data = array('notes' => $foodnote);
		if ($group > 0) {
			$this->db->where('order_id', $orderid);
			$this->db->where('groupmid', $group);
			$this->db->update('order_menu', $data);
		} else {
			$this->db->where('row_id', $rowid);
			$this->db->update('order_menu', $data);
		}
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['orderinfo']  	   = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$data['iteminfo']       = $this->order_model->customerorder($orderid);
		$data['billinfo']	   = $this->order_model->billinfo($orderid);
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderlist";
		$this->load->view('ordermanage/updateorderlist', $data);
	}
	public function posaddtocart()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$catid = $this->input->post('catid');
		$pid = $this->input->post('pid');
		$sizeid = $this->input->post('sizeid');
		$isgroup = $this->input->post('isgroup', true);
		$myid = $catid . $pid . $sizeid;
		$itemname = $this->input->post('itemname', true);
		$size = $this->input->post('varientname', true);
		$qty = $this->input->post('qty', true);
		$price = $this->input->post('price', true);
		$addonsid = $this->input->post('addonsid', true);
		$allprice = $this->input->post('allprice', true);
		$adonsunitprice = $this->input->post('adonsunitprice', true);
		$adonsqty = $this->input->post('adonsqty', true);
		$adonsname = $this->input->post('adonsname', true);
		$cart = $this->cart->contents();
		$n = 0;
		if (empty($isgroup)) {
			$isgroup1 = 0;
		} else {
			$isgroup1 = $this->input->post('isgroup', true);
		}
		$new_str = str_replace(',', '0', $addonsid);
		$new_str2 = str_replace(',', '0', $adonsqty);
		$uaid = $pid . $new_str . $sizeid;
		if (!empty($addonsid)) {
			$joinid = trim($addonsid, ',');
			//$uaid=(int)$joinid.mt_rand(1, time());
			$cartexist = $this->cart->contents();
			if (!empty($cartexist)) {
				$adonsarray = explode(',', $addonsid);
				$adonsqtyarray = explode(',', $adonsqty);
				$adonspricearray = explode(',', $adonsunitprice);

				$adqty = array();
				$adprice = array();
				foreach ($cartexist as $cartinfo) {
					if ($cartinfo['id'] == $myid . $uaid) {
						$adqty = explode(',', $cartinfo['addonsqty']);
						$adprice = explode(',', $cartinfo['addonupr']);
					}
				}
				$x = 0;
				$finaladdonsqty = '';
				$finaladdonspr = 0;
				foreach ($adonsarray as $singleaddons) {
					$singleaddons;
					$totalaqty = $adonsqtyarray[$x] + $adqty[$x];
					$finaladdonsqty .= $totalaqty . ',';
					$totalaprice = $totalaqty * $adonspricearray[$x];
					$finaladdonspr = $totalaprice + $finaladdonspr;
					$x++;
				}

				if (!empty($adonsarray)) {
					$aids = $addonsid;
					$aqty = trim($finaladdonsqty, ',');;
					$aname = $adonsname;
					$aprice = $adonsunitprice;
					$atprice = $finaladdonspr;
					$grandtotal = $price;
				} else {
					$aids = $addonsid;
					$aqty = $adonsqty;
					$aname = $adonsname;
					$aprice = $adonsunitprice;
					$atprice = $allprice;
					$grandtotal = $price;
				}
			} else {
				$aids = $addonsid;
				$aqty = $adonsqty;
				$aname = $adonsname;
				$aprice = $adonsunitprice;
				$atprice = $allprice;
				$grandtotal = $price;
			}
		} else {
			$grandtotal = $price;
			$aids = '';
			$aqty = '';
			$aname = '';
			$aprice = '';
			$atprice = '0';
		}
		$myid = $catid . $pid . $sizeid . $uaid;
		$data_items = array(
			'id'      	=> $myid,
			'pid'     	=> $pid,
			'name'    	=> $itemname,
			'sizeid'    	=> $sizeid,
			'isgroup'    => $isgroup1,
			'size'    	=> $size,
			'qty'     	=> $qty,
			'price'   	=> $grandtotal,
			'addonsuid'  => $uaid,
			'addonsid'   => $aids,
			'addonname'  => $aname,
			'addonupr'   => $aprice,
			'addontpr'   => $atprice,
			'addonsqty'  => $aqty,
			'itemnote'	=> ""
		);


		$this->cart->insert($data_items);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "poscartlist";
		$this->load->view('ordermanage/poscartlist', $data);
	}
	public function cartupdate()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$cartID = $this->input->post('CartID');
		$productqty = $this->input->post('qty', true);
		$Udstatus = $this->input->post('Udstatus', true);
		if (($Udstatus == "del") && ($productqty > 0)) {
			$data = array(
				'rowid' => $cartID,
				'qty' => $productqty - 1
			);
			$this->cart->update($data);
		}
		if ($Udstatus == "add") {
			$data = array(
				'rowid' => $cartID,
				'qty' => $productqty + 1
			);
			$this->cart->update($data);
		}
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "cartlist";
		$this->load->view('ordermanage/cartlist', $data);
	}
	public function poscartupdate()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$cartID = $this->input->post('CartID');
		$productqty = $this->input->post('qty', true);
		$Udstatus = $this->input->post('Udstatus', true);
		if (($Udstatus == "del") && ($productqty > 0)) {
			$data = array(
				'rowid' => $cartID,
				'qty' => $productqty - 1
			);
			$this->cart->update($data);
		}
		if ($Udstatus == "add") {
			$data = array(
				'rowid' => $cartID,
				'qty' => $productqty + 1
			);
			$this->cart->update($data);
		}
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "poscartlist";
		$this->load->view('ordermanage/poscartlist', $data);
	}
	public function addonsmenu()
	{
		$id = $this->input->post('pid');
		$sid = $this->input->post('sid');
		$data['item']   	  = $this->order_model->findid($id, $sid);
		$data['addonslist']   = $this->order_model->findaddons($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "addonsfood";
		$this->load->view('ordermanage/addonsfood', $data);
	}
	public function posaddonsmenu()
	{
		$id = $this->input->post('pid');
		$sid = $this->input->post('sid');
		$data['totalvarient'] = $this->input->post('totalvarient', true);
		$data['customqty'] = $this->input->post('customqty', true);
		$data['item']   	  = $this->order_model->findid($id, $sid);
		$data['addonslist']   = $this->order_model->findaddons($id);
		$data['varientlist']   = $this->order_model->findByvmenuId($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "posaddonsfood";
		$this->load->view('ordermanage/posaddonsfood', $data);
	}

	public function cartclear()
	{
		$this->cart->destroy();
		redirect('ordermanage/order/neworder');
	}
	public function posclear()
	{
		$this->cart->destroy();
		redirect('ordermanage/order/pos_invoice');
	}

	public function removetocart()
	{
		$rowid = $this->input->post('rowid');
		$data = array(
			'rowid'   => $rowid,
			'qty'     => 0
		);
		$this->cart->update($data);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "poscartlist";
		$this->load->view('ordermanage/poscartlist', $data);
	}
	public function placeoreder()
	{
		$this->form_validation->set_rules('ctypeid', 'Customer Type', 'required');
		$this->form_validation->set_rules('waiter', 'Select Waiter', 'required');
		$this->form_validation->set_rules('tableid', 'Select Table', 'required');
		$this->form_validation->set_rules('customer_name', 'Customer Name', 'required');
		$this->form_validation->set_rules('order_date', 'Order Date', 'required');
		$saveid = $this->session->userdata('id');
		$customerid = $this->input->post('customer_name', true);
		$paymentsatus = $this->input->post('card_type', true);
		if ($this->form_validation->run()) {
			if ($cart = $this->cart->contents()) {
				$this->permission->method('ordermanage', 'create')->redirect();
				$logData = array(
					'action_page'         => "Add New Order",
					'action_done'     	 => "Insert Data",
					'remarks'             => "Item New Order Created",
					'user_name'           => $this->session->userdata('fullname'),
					'entry_date'          => date('Y-m-d H:i:s'),
				);
				/* add New Order*/
				$purchase_date = str_replace('/', '-', $this->input->post('order_date'));
				$newdate = date('Y-m-d', strtotime($purchase_date));
				$lastid = $this->db->select("*")->from('customer_order')
					->order_by('order_id', 'desc')
					->get()
					->row();
				$sl = $lastid->order_id;
				if (empty($sl)) {
					$sl = 1;
				} else {
					$sl = $sl + 1;
				}

				$si_length = strlen((int)$sl);

				$str = '0000';
				$str2 = '0000';
				$cutstr = substr($str, $si_length);
				$sino = $cutstr . $sl;
				$data2 = array(
					'customer_id'			=>	$this->input->post('customer_name', true),
					'saleinvoice'			=>	$sino,
					'cutomertype'		    =>	$this->input->post('ctypeid', true),
					'waiter_id'	        	=>	$this->input->post('waiter', true),
					'order_date'	        =>	$newdate,
					'order_time'	        =>	date('H:i:s'),
					'totalamount'		 	=>  $this->input->post('grandtotal', true),
					'table_no'		    	=>	$this->input->post('tableid', true),
					'customer_note'		    =>	$this->input->post('customernote', true),
					'order_status'		    =>	1
				);
				$this->db->insert('customer_order', $data2);
				$orderid = $this->db->insert_id();

				if ($this->order_model->orderitem($orderid)) {
					$this->logs_model->log_recorded($logData);
					$this->session->set_flashdata('message', display('save_successfully'));
					$customer = $this->order_model->customerinfo($customerid);

					$this->cart->destroy();

					if ($paymentsatus == 5) {
						redirect('ordermanage/order/paymentgateway/' . $orderid . '/' . $paymentsatus);
					} else if ($paymentsatus == 3) {
						redirect('ordermanage/order/paymentgateway/' . $orderid . '/' . $paymentsatus);
					} else if ($paymentsatus == 2) {
						redirect('ordermanage/order/paymentgateway/' . $orderid . '/' . $paymentsatus);
					} else {
						redirect('ordermanage/order/neworder');
					}
				} else {
					$this->session->set_flashdata('exception',  display('please_try_again'));
				}
				redirect("ordermanage/order/neworder");
			} else {
				$this->session->set_flashdata('exception',  'Please add Some food!!');
				redirect("ordermanage/order/neworder");
			}
		} else {
			$this->permission->method('ordermanage', 'read')->redirect();
			$data['categorylist']   = $this->order_model->category_dropdown();
			$data['curtomertype']   = $this->order_model->ctype_dropdown();
			$data['waiterlist']     = $this->order_model->waiter_dropdown();
			$data['tablelist']     = $this->order_model->table_dropdown();
			$data['customerlist']   = $this->order_model->customer_dropdown();
			$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
			$data['module'] = "ordermanage";
			$data['page']   = "addorder";
			echo Modules::run('template/layout', $data);
		}
	}
	public function pos_order($value = null)
	{
		$this->form_validation->set_rules('ctypeid', 'Customer Type', 'required');

		$this->form_validation->set_rules('customer_name', 'Customer Name', 'required');
		$saveid = $this->session->userdata('id');
		$paymentsatus = $this->input->post('card_type', true);
		$isonline = $this->input->post('isonline', true);
		if ($this->form_validation->run()) {
			if ($cart = $this->cart->contents()) {
				$this->permission->method('ordermanage', 'create')->redirect();
				$logData = array(
					'action_page'         => "Add New Order",
					'action_done'     	 => "Insert Data",
					'remarks'             => "Item New Order Created",
					'user_name'           => $this->session->userdata('fullname'),
					'entry_date'          => date('Y-m-d H:i:s'),
				);
				/* add New Order*/
				$purchase_date = str_replace('/', '-', $this->input->post('order_date'));
				$newdate = date('Y-m-d', strtotime($purchase_date));
				$lastid = $this->db->select("*")->from('customer_order')->order_by('order_id', 'desc')->get()->row();
				$sl = ($lastid && $lastid->order_id) ? $lastid->order_id : 0;
				if (empty($sl)) {
					$sl = 1;
				} else {
					$sl = $sl + 1;
				}

				$si_length = strlen((int)$sl);

				$str = '0000';
				$str2 = '0000';
				$cutstr = substr($str, $si_length);
				$sino = $cutstr . $sl;
				$todaydate = date('Y-m-d');
				$todaystoken = $this->db->select("*")->from('customer_order')->where('order_date', $todaydate)->order_by('order_id', 'desc')->get()->row();
				if (empty($todaystoken)) {
					$mytoken = 1;
				} else {
					$mytoken = $todaystoken->tokenno + 1;
				}
				$token_length = strlen((int)$mytoken);
				$tokenstr = '00';
				$newtoken = substr($tokenstr, $token_length);
				$tokenno = $newtoken . $mytoken;
				$cookedtime = $this->input->post('cookedtime');
				$customerid2 = $this->input->post('customer_name', true);
				if (empty($cookedtime)) {
					$cookedtime = "00:15:00";
				}
				$customerinfo = $this->order_model->read('*', 'customer_info', array('customer_id' => $this->input->post('customer_name', true)));
				$mtype = $this->order_model->read('*', 'membership', array('id' => $customerinfo->membership_type));
				$ordergrandt = $this->input->post('grandtotal', true);
				$scan = scandir('application/modules/');
				$getdiscount = 0;
				foreach ($scan as $file) {
					if ($file == "loyalty") {
						if (file_exists(APPPATH . 'modules/' . $file . '/assets/data/env')) {
							$getdiscount = $mtype->discount * $this->input->post('subtotal') / 100;
						}
					}
				}
				$data2 = array(
					'customer_id'			=>	$this->input->post('customer_name', true),
					'saleinvoice'			=>	$sino,
					'cutomertype'		    =>	$this->input->post('ctypeid'),
					'waiter_id'	        	=>	$this->input->post('waiter', true),
					'isthirdparty'	        =>	$this->input->post('delivercom', true),
					'thirdpartyinvoiceid'	=>	$this->input->post('thirdpartyinvoiceid'),
					'order_date'	        =>	$newdate,
					'order_time'	        =>	date('H:i:s'),
					'totalamount'		 	=>  $ordergrandt - $getdiscount,
					'table_no'		    	=>	$this->input->post('tableid', true),
					'customer_note'		    =>	$this->input->post('customernote', true),
					'tokenno'		        =>	$tokenno,
					'cookedtime'		    =>	$cookedtime,
					'order_status'		    =>	1
				);

				$this->db->insert('customer_order', $data2);
				$orderid = $this->db->insert_id();
				$taxinfos = $this->taxchecking();
				if (!empty($taxinfos)) {
					$multitaxvalue = $this->input->post('multiplletaxvalue', true);
					$multitaxvaluedata = unserialize($multitaxvalue);
					$inserttaxarray = array(
						'customer_id' => $this->input->post('customer_name', true),
						'relation_id' => $orderid,
						'date' => $newdate
					);
					$inserttaxdata = array_merge($inserttaxarray, $multitaxvaluedata);
					$this->db->insert('tax_collection', $inserttaxdata);
				}
				/*for 02/11*/
				if ($this->input->post('ctypeid') == 1 || $this->input->post('ctypeid') == 5 || $this->input->post('ctypeid') == 6) {
					if ($this->input->post('table_member_multi') == 0) {
						$addtable_member = array(
							'table_id' 		=> $this->input->post('tableid'),
							'customer_id'	=> $this->input->post('customer_name', true),
							'order_id' 		=> $orderid,
							'time_enter' 	=> date('H:i:s'),
							'created_at'	=> $newdate,
							'total_people' 	=> $this->input->post('tablemember', true),
						);
						$this->db->insert('table_details', $addtable_member);
					} else {
						$multipay_inserts = explode(',', $this->input->post('table_member_multi'));
						$table_member_multi_person = explode(',', $this->input->post('table_member_multi_person', true));
						$z = 0;
						foreach ($multipay_inserts as $multipay_insert) {
							$addtable_member = array(
								'table_id' 		=> $multipay_insert,
								'customer_id'	=> $this->input->post('customer_name', true),
								'order_id' 		=> $orderid,
								'time_enter' 	=> date('H:i:s'),
								'created_at'	=> $newdate,
								'total_people' 	=> $table_member_multi_person[$z],
							);
							$this->db->insert('table_details', $addtable_member);
							$z++;
						}
					}
				}
				/*enc 02/11*/
				if ($this->input->post('delivercom', true) > 0) {
					/*Push Notification*/
					$this->db->select('*');
					$this->db->from('user');
					$this->db->where('id', $this->input->post('waiter', true));
					$query = $this->db->get();
					$allemployee = $query->row();
					$senderid = array();
					$senderid[] = $allemployee->waiter_kitchenToken;
					define('API_ACCESS_KEY', 'AAAAqG0NVRM:APA91bExey2V18zIHoQmCkMX08SN-McqUvI4c3CG3AnvkRHQp8S9wKn-K4Vb9G79Rfca8bQJY9pn-tTcWiXYJiqe2s63K6QHRFqIx4Oaj9MoB1uVqB7U_gNT9fiqckeWge8eVB9P5-rX');
					$registrationIds = $senderid;
					$msg = array(
						'message' 					=> "Orderid:" . $orderid . ", Amount:" . $this->input->post('grandtotal', true),
						'title'						=> "New Order Placed",
						'subtitle'					=> "admin",
						'tickerText'				=> "10",
						'vibrate'					=> 1,
						'sound'						=> 1,
						'largeIcon'					=> "TSET",
						'smallIcon'					=> "TSET"
					);
					$fields2 = array(
						'registration_ids' 	=> $registrationIds,
						'data'			=> $msg
					);

					$headers2 = array(
						'Authorization: key=' . API_ACCESS_KEY,
						'Content-Type: application/json'
					);

					$ch2 = curl_init();
					curl_setopt($ch2, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
					curl_setopt($ch2, CURLOPT_POST, true);
					curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
					curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($fields2));
					$result2 = curl_exec($ch2);
					curl_close($ch2);
					/*End Notification*/
					/*Push Notification*/
					$condition = "user.waiter_kitchenToken!='' AND employee_history.pos_id=1";
					$this->db->select('user.*,employee_history.emp_his_id,employee_history.employee_id,employee_history.pos_id ');
					$this->db->from('user');
					$this->db->join('employee_history', 'employee_history.emp_his_id = user.id', 'left');
					$this->db->where($condition);
					$query = $this->db->get();
					$allkitchen = $query->result();
					$senderid5 = array();
					foreach ($allkitchen as $mytoken) {
						$senderid5[] = $mytoken->waiter_kitchenToken;
					}

					define('API_ACCESS_KEY', 'AAAAqG0NVRM:APA91bExey2V18zIHoQmCkMX08SN-McqUvI4c3CG3AnvkRHQp8S9wKn-K4Vb9G79Rfca8bQJY9pn-tTcWiXYJiqe2s63K6QHRFqIx4Oaj9MoB1uVqB7U_gNT9fiqckeWge8eVB9P5-rX');
					$registrationIds5 = $senderid5;
					$msg5 = array(
						'message' 					=> "Orderid:" . $orderid . ", Amount:" . $this->input->post('grandtotal', true),
						'title'						=> "New Order Placed",
						'subtitle'					=> "TSET",
						'tickerText'				=> "onno",
						'vibrate'					=> 1,
						'sound'						=> 1,
						'largeIcon'					=> "TSET",
						'smallIcon'					=> "TSET"
					);
					$fields5 = array(
						'registration_ids' 	=> $registrationIds5,
						'data'			=> $msg5
					);

					$headers5 = array(
						'Authorization: key=' . API_ACCESS_KEY,
						'Content-Type: application/json'
					);

					$ch5 = curl_init();
					curl_setopt($ch5, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
					curl_setopt($ch5, CURLOPT_POST, true);
					curl_setopt($ch5, CURLOPT_HTTPHEADER, $headers5);
					curl_setopt($ch5, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch5, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch5, CURLOPT_POSTFIELDS, json_encode($fields5));
					$result5 = curl_exec($ch5);
					curl_close($ch5);
				} else {
					/*Push Notification*/
					$this->db->select('*');
					$this->db->from('user');
					$this->db->where('id', $this->input->post('waiter', true));
					$query = $this->db->get();
					$allemployee = $query->row();
					$senderid = array();
					$senderid[] = $allemployee->waiter_kitchenToken;
					define('API_ACCESS_KEY', 'AAAAqG0NVRM:APA91bExey2V18zIHoQmCkMX08SN-McqUvI4c3CG3AnvkRHQp8S9wKn-K4Vb9G79Rfca8bQJY9pn-tTcWiXYJiqe2s63K6QHRFqIx4Oaj9MoB1uVqB7U_gNT9fiqckeWge8eVB9P5-rX');
					$registrationIds = $senderid;
					$msg = array(
						'message' 					=> "Orderid:" . $orderid . ", Amount:" . ($ordergrandt - $getdiscount),
						'title'						=> "New Order Placed",
						'subtitle'					=> "admin",
						'tickerText'				=> "10",
						'vibrate'					=> 1,
						'sound'						=> 1,
						'largeIcon'					=> "TSET",
						'smallIcon'					=> "TSET"
					);
					$fields2 = array(
						'registration_ids' 	=> $registrationIds,
						'data'			=> $msg
					);

					$headers2 = array(
						'Authorization: key=' . API_ACCESS_KEY,
						'Content-Type: application/json'
					);

					$ch2 = curl_init();
					curl_setopt($ch2, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
					curl_setopt($ch2, CURLOPT_POST, true);
					curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
					curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($fields2));
					$result2 = curl_exec($ch2);
					curl_close($ch2);
					/*End Notification*/
					/*Push Notification*/
					$condition = "user.waiter_kitchenToken!='' AND employee_history.pos_id=1";
					$this->db->select('user.*,employee_history.emp_his_id,employee_history.employee_id,employee_history.pos_id ');
					$this->db->from('user');
					$this->db->join('employee_history', 'employee_history.emp_his_id = user.id', 'left');
					$this->db->where($condition);
					$query = $this->db->get();
					$allkitchen = $query->result();
					$senderid5 = array();
					foreach ($allkitchen as $mytoken) {
						$senderid5[] = $mytoken->waiter_kitchenToken;
					}
					define('API_ACCESS_KEY2', 'AAAAqG0NVRM:APA91bExey2V18zIHoQmCkMX08SN-McqUvI4c3CG3AnvkRHQp8S9wKn-K4Vb9G79Rfca8bQJY9pn-tTcWiXYJiqe2s63K6QHRFqIx4Oaj9MoB1uVqB7U_gNT9fiqckeWge8eVB9P5-rX');
					$registrationIds5 = $senderid5;
					$msg5 = array(
						'message' 					=> "Orderid:" . $orderid . ", Amount:" . ($ordergrandt - $getdiscount),
						'title'						=> "New Order Placed",
						'subtitle'					=> "TSET",
						'tickerText'				=> "onno",
						'vibrate'					=> 1,
						'sound'						=> 1,
						'largeIcon'					=> "TSET",
						'smallIcon'					=> "TSET"
					);
					$fields5 = array(
						'registration_ids' 	=> $registrationIds5,
						'data'			=> $msg5
					);

					$headers5 = array(
						'Authorization: key=' . API_ACCESS_KEY2,
						'Content-Type: application/json'
					);

					$ch5 = curl_init();
					curl_setopt($ch5, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
					curl_setopt($ch5, CURLOPT_POST, true);
					curl_setopt($ch5, CURLOPT_HTTPHEADER, $headers5);
					curl_setopt($ch5, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch5, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch5, CURLOPT_POSTFIELDS, json_encode($fields5));
					$result5 = curl_exec($ch5);
					curl_close($ch5);
				}
				if ($this->order_model->orderitem($orderid)) {
					$this->logs_model->log_recorded($logData);

					// Ensure customerid is properly defined
					if (!isset($customerid)) {
						$customerid = $this->input->post('customer_name', true);
					}
					$customer = $this->order_model->customerinfo($customerid);
					$scan = scandir('application/modules/');
					$getcus = "";
					foreach ($scan as $file) {
						if ($file == "loyalty") {
							if (file_exists(APPPATH . 'modules/' . $file . '/assets/data/env')) {
								$getcus = $customerid2;
							}
						}
					}
					if (!empty($getcus)) {
						$isexitscusp = $this->db->select("*")->from('tbl_customerpoint')->where('customerid', $customerid2)->get()->row();
						if (empty($isexitscusp)) {
							$pointstable2 = array(
								'customerid'   => $customerid2,
								'amount'       => "",
								'points'       => 10
							);
							$this->order_model->insert_data('tbl_customerpoint', $pointstable2);
						}
					}

					$this->cart->destroy();
					if ($paymentsatus == 5) {
						redirect('ordermanage/order/paymentgateway/' . $orderid . '/' . $paymentsatus);
					} else if ($paymentsatus == 3) {
						redirect('ordermanage/order/paymentgateway/' . $orderid . '/' . $paymentsatus);
					} else if ($paymentsatus == 2) {
						redirect('ordermanage/order/paymentgateway/' . $orderid . '/' . $paymentsatus);
					} else {
						if ($isonline == 1) {
							$this->session->set_flashdata('message', display('order_successfully'));
							redirect('ordermanage/order/pos_invoice');
						} else {
							if ($value == 1) {
								echo $orderid;
								exit;
							} else {
								$view = $this->postokengenerate($orderid, 0);
								// echo $view; //work
								exit;
							}
						}
					}
				} else {
					if ($isonline == 1) {
						$this->session->set_flashdata('exception',  display('please_try_again'));
						redirect("ordermanage/order/pos_invoice");
					} else {
						echo "error 3";
					}
				}
			} else {
				if ($isonline == 1) {
					$this->session->set_flashdata('exception',  'Please add Some food!!');
					redirect("ordermanage/order/pos_invoice");
				} else {
					echo "error 2";
				}
			}
		} else {
			$this->permission->method('ordermanage', 'read')->redirect();
			if ($isonline == 1) {
				$data['categorylist']   = $this->order_model->category_dropdown();
				$data['curtomertype']   = $this->order_model->ctype_dropdown();
				$data['waiterlist']     = $this->order_model->waiter_dropdown();
				$data['tablelist']     = $this->order_model->table_dropdown();
				$data['customerlist']   = $this->order_model->customer_dropdown();
				$settinginfo = $this->order_model->settinginfo();
				$data['settinginfo'] = $settinginfo;
				$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);

				$data['module'] = "ordermanage";
				$data['page']   = "posorder";
				echo Modules::run('template/layout', $data);
			} else {
				echo "error 1";
			}
		}
	}
	public function orderlist()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('order_list');
		$saveid = $this->session->userdata('id');
		#-------------------------------#       
		#
		#pagination starts
		#
		$config["base_url"] = base_url('ordermanage/order/orderlist');
		$config["total_rows"]  = $this->order_model->count_order();
		$config["per_page"]    = 25;
		$config["uri_segment"] = 4;
		$config["last_link"] = display('sLast');
		$config["first_link"] = display('sFirst');
		$config['next_link'] = display('sNext');
		$config['prev_link'] = display('sPrevious');
		$config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
		$config['full_tag_close'] = "</ul>";
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		$config['next_tag_open'] = "<li>";
		$config['next_tag_close'] = "</li>";
		$config['prev_tag_open'] = "<li>";
		$config['prev_tagl_close'] = "</li>";
		$config['first_tag_open'] = "<li>";
		$config['first_tagl_close'] = "</li>";
		$config['last_tag_open'] = "<li>";
		$config['last_tagl_close'] = "</li>";
		/* ends of bootstrap */
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data["iteminfo"] = $this->order_model->orderlist($config["per_page"], $page);
		$data["links"] = $this->pagination->create_links();
		$data['pagenum'] = $page;
		#
		#pagination ends
		# 
		$settinginfo = $this->order_model->settinginfo();
		$data['possetting'] = $this->order_model->read('*', 'tbl_posetting', array('possettingid' => 1));
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "orderlist";
		echo Modules::run('template/layout', $data);
	}
	public function allorderlist()
	{

		$list = $this->order_model->get_allorder();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $rowdata) {
			$no++;
			$row = array();
			if ($rowdata->order_status == 1) {
				$status = "Pending";
			}
			if ($rowdata->order_status == 2) {
				$status = "Processing";
			}
			if ($rowdata->order_status == 3) {
				$status = "Ready";
			}
			if ($rowdata->order_status == 4) {
				$status = "Served";
			}
			if ($rowdata->order_status == 5) {
				$status = "Cancel";
			}
			$newDate = date("d-M-Y", strtotime($rowdata->order_date));
			$update = '';
			$posprint = '';
			$details = '';
			$paymentbtn = '';
			$cancelbtn = '';
			$acptreject = '';
			$margeord = '';
			$printmarge = '';
			$split = '';
			$ptype = $this->db->select("bill_status")->from('bill')->where('order_id', $rowdata->order_id)->get()->row();


			if ($this->permission->method('ordermanage', 'read')->access()) :
				$details = '<a href="' . base_url() . 'ordermanage/order/orderdetails/' . $rowdata->order_id . '" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="Details"><i class="fa fa-eye"></i></a>&nbsp;';
			endif;
			if ($rowdata->splitpay_status == 1) :
				$split = '<a href="javascript:;" onclick="showsplit(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="Update" id="table-split-' . $rowdata->order_id . '">' . display('split') . '</a>&nbsp;&nbsp;';
			endif;
			if ($this->permission->method('ordermanage', 'read')->access()) :
				if (($rowdata->order_status != 5 && $rowdata->orderacceptreject != 1) && ($rowdata->cutomertype == 2 || $rowdata->cutomertype == 99)) {
					$acptreject = '&nbsp;<a href="javascript:;" id="accepticon_' . $rowdata->order_id . '" data-id="' . $rowdata->order_id . '" data-type="' . $ptype->bill_status . '" class="btn btn-xs btn-danger btn-sm mr-1 aceptorcancel" data-toggle="tooltip" data-placement="left" title="" data-original-title="Accept or Cancel"><i class="fa fa-info-circle"></i></a>&nbsp;';
				}
				if ($rowdata->order_status == 1 || $rowdata->order_status == 2 || $rowdata->order_status == 3) {
					$cancelbtn = '&nbsp;<a href="javascript:;" id="cancelicon_' . $rowdata->order_id . '" data-id="' . $rowdata->order_id . '" data-type="' . $ptype->bill_status . '" class="btn btn-xs btn-danger btn-sm mr-1 aceptorcancel" data-toggle="tooltip" data-placement="left" title="" data-original-title="Accept or Cancel"><i class="fa fa-trash-o"></i></a>&nbsp;';
					$update = '<a href="' . base_url() . 'ordermanage/order/otherupdateorder/' . $rowdata->order_id . '" class="btn btn-xs btn-info btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;';
				}
				$posprint = '<a href="javascript:;" onclick="printPosinvoice(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="Pos Invoice"><i class="fa fa-window-maximize" aria-hidden="true"></i></a>&nbsp;';
				if (!empty($rowdata->marge_order_id)) {
					$printmarge = '<a href="javascript:;" onclick="printmergeinvoice(\'' . base64_encode($rowdata->marge_order_id) . '\')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="Merge Invoice"><i class="fa fa-meetup" aria-hidden="true"></i></a>';
				}
			endif;
			if ($this->permission->method('ordermanage', 'read')->access()) {
				if ($ptype->bill_status == 0  && $rowdata->orderacceptreject != 0) {
					$margeord = '<a href="javascript:;" onclick="createMargeorder(' . $rowdata->order_id . ',1)" id="hidecombtn_' . $rowdata->order_id . '" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="Make Payment"><i class="fa fa-window-restore" aria-hidden="true"></i></a>&nbsp;';
				}
			}

			$row[] = $no;
			$row[] = $rowdata->order_id;
			$row[] = $rowdata->customer_name;
			$row[] = $rowdata->fullname;
			$row[] = $rowdata->tablename;
			$row[] = $status;
			$row[] = $rowdata->order_date;
			$row[] = $rowdata->totalamount;
			$row[] = $acptreject . $cancelbtn . $update . $details . $margeord . $posprint . $printmarge . $split;
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->order_model->count_allorder(),
			"recordsFiltered" => $this->order_model->count_filterallorder(),
			"data" => $data,
		);
		echo json_encode($output);
	}
	// public function todayallorder()
	// {

	// 	$list = $this->order_model->get_completeorder();
	// 	$data = array();
	// 	$no = $_POST['start'];
	// 	foreach ($list as $rowdata) {
	// 		$no++;
	// 		$row = array();
	// 		$update = '';
	// 		$details = '';
	// 		$print = '';
	// 		$posprint = '';
	// 		$split = '';
	// 		$kot = '';
	// 		if ($this->permission->method('ordermanage', 'update')->access()) :
	// 			$update = '<a href="javascript:;" onclick="editposorder(' . $rowdata->order_id . ',2)" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="Update" id="table-today-' . $rowdata->order_id . '"><i class="ti-pencil"></i></a>&nbsp;&nbsp;';
	// 		endif;
	// 		if ($rowdata->splitpay_status == 1) :
	// 			$split = '<a href="javascript:;" onclick="showsplit(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="Update" id="table-split-' . $rowdata->order_id . '">' . display('split') . '</a>&nbsp;&nbsp;';
	// 		endif;
	// 		if ($this->permission->method('ordermanage', 'read')->access()) :
	// 			$details = '&nbsp;<a href="javascript:;" onclick="detailspop(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="Details"><i class="fa fa-eye"></i></a>&nbsp;';
	// 			$print = '<a href="javascript:;" onclick="pos_order_invoice(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="Invoice"><i class="fa fa-window-restore"></i></a>&nbsp;';
	// 			$posprint = '<a href="javascript:;" onclick="pospageprint(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="Pos Invoice"><i class="fa fa-window-maximize"></i></a>';
	// 			$kot = '<a href="javascript:;" onclick="postokenprint(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="KOT"><i class="fa fa-print"></i></a>';
	// 		endif;

	// 		$row[] = $no;
	// 		$row[] = $rowdata->saleinvoice;
	// 		$row[] = $rowdata->customer_name;
	// 		$row[] = $rowdata->customer_type;
	// 		$row[] = $rowdata->first_name . $rowdata->last_name;
	// 		$row[] = $rowdata->tablename;
	// 		$row[] = $rowdata->order_date;
	// 		$row[] = $rowdata->totalamount;
	// 		$row[] = $update . $print . $posprint . $details . $split . $kot;
	// 		$data[] = $row;
	// 	}
	// 	$output = array(
	// 		"draw" => $_POST['draw'],
	// 		"recordsTotal" => $this->order_model->count_alltodayorder(),
	// 		"recordsFiltered" => $this->order_model->count_filtertorder(),
	// 		"data" => $data,
	// 	);
	// 	echo json_encode($output);
	// }


public function todayallorder()
{
    $list = $this->order_model->get_completeorder(null);
    $this->generate_order_data($list);
}

public function todayallguestorder()
{
    $list = $this->order_model->get_completeorder(6);
    $this->generate_order_data($list,6);
}

public function todayallemployeeorder()
{
    $list = $this->order_model->get_completeorder(5);
    $this->generate_order_data($list,5);
}


public function todayallemployeeorder2()
{
    $list = $this->order_model->get_completeorder(6);
    $this->generate_order_data($list,6);
}

public function todayallcharityorder()
{
    $list = $this->order_model->get_completeorder(7);
    $this->generate_order_data($list,7);
}

private function generate_order_data($list,$type = null)
{
    $data = array();
    $no = $_POST['start'];

	if($type == null){
		$vid = 2;
	}
	else{
		$vid = $type;
	}

    foreach ($list as $rowdata) {
        $no++;
        $row = array();
        $update = '';
        $details = '';
        $print = '';
        $posprint = '';
        $split = '';
        $kot = '';
        if ($this->permission->method('ordermanage', 'update')->access()) :
            $update = '<a href="javascript:;" onclick="editposorder(' . $rowdata->order_id . ','.$vid.')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="Update" id="table-today-' . $rowdata->order_id . '"><i class="ti-pencil"></i></a>&nbsp;&nbsp;';
        endif;
        if ($rowdata->splitpay_status == 1) :
            $split = '<a href="javascript:;" onclick="showsplit(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="Update" id="table-split-' . $rowdata->order_id . '">' . display('split') . '</a>&nbsp;&nbsp;';
        endif;
        if ($this->permission->method('ordermanage', 'read')->access()) :
            $details = '&nbsp;<a href="javascript:;" onclick="detailspop(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="Details"><i class="fa fa-eye"></i></a>&nbsp;';
            $print = '<a href="javascript:;" onclick="pos_order_invoice(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="Invoice"><i class="fa fa-window-restore"></i></a>&nbsp;';
            $posprint = '<a href="javascript:;" onclick="pospageprint(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="Pos Invoice"><i class="fa fa-window-maximize"></i></a>';
            $kot = '<a href="javascript:;" onclick="postokenprint(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left" title="" data-original-title="KOT"><i class="fa fa-print"></i></a>';
        endif;

        $row[] = $no;
        $row[] = $rowdata->saleinvoice;
        $row[] = $rowdata->customer_name;
        $row[] = $rowdata->customer_type;
        $row[] = $rowdata->first_name . $rowdata->last_name;
        $row[] = $rowdata->tablename;
        $row[] = $rowdata->order_date;
        $row[] = $rowdata->totalamount;
        $row[] = $update . $print . $posprint . $details . $split . $kot;
        $data[] = $row;
    }
    $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->order_model->count_alltodayorder($type),
        "recordsFiltered" => $this->order_model->count_filtertorder($type),
        "data" => $data,
    );
    echo json_encode($output);
}


	public function notification()
	{
		$tdata = date('Y-m-d');
		$notify = $this->db->select("*")->from('customer_order')->where('cutomertype', 2)->where('order_date', $tdata)->where('nofification', 0)->get()->num_rows();

		$data = array(
			'unseen_notification'  => $notify
		);
		echo json_encode($data);
	}
	public function notificationqr()
	{
		$tdata = date('Y-m-d');
		$notify = $this->db->select("*")->from('customer_order')->where('cutomertype', 99)->where('order_date', $tdata)->where('nofification', 0)->get()->num_rows();

		$data = array(
			'unseen_notificationqr'  => $notify
		);
		echo json_encode($data);
	}
	public function acceptnotify()
	{
		$status = $this->input->post('status');
		$orderid = $this->input->post('orderid');
		$acceptreject = $this->input->post('acceptreject', true);
		$reason = $this->input->post('reason', true);
		$onprocesstab = $this->input->post('onprocesstab', true);
		$orderinfo = $this->db->select("*")->from('customer_order')->where('order_id', $orderid)->get()->row();
		$customerinfo = $this->db->select("*")->from('customer_info')->where('customer_id', $orderinfo->customer_id)->get()->row();
		if ($acceptreject == 1) {
			$mymsg = "You Order is Accepted";
			$bodymsg = "Order ID:" . $orderid . " Order amount:" . $orderinfo->totalamount;
			$orderstatus = $this->db->select('order_status,cutomertype,saleinvoice,order_date,customer_id')->from('customer_order')->where('order_id', $orderid)->get()->row();
			if ($orderstatus->order_status == 4) {
				$this->removeformstock($orderid);
				if ($orderstatus->cutomertype == 2) {
					$cusinfo = $this->db->select('*')->from('customer_info')->where('customer_id', $orderinfo->customer_id)->get()->row();
					$finalill = $this->db->select('*')->from('bill')->where('order_id', $orderid)->get()->row();
					$headn = $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name;
					$coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
					$customer_headcode = $coainfo->HeadCode;
					if ($finalill->payment_method_id == 4) {
						$headcode = 1020101;
					} else {
						$paytype = $this->db->select('payment_method')->from('payment_method')->where('payment_method_id', $finalill->payment_method_id)->get()->row();
						$coacode = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $paytype->payment_method)->get()->row();
						$headcode = $coacode->HeadCode;
					}

					$invoice_no = $orderinfo->saleinvoice;
					$saveid = $this->session->userdata('id');
					//Customer debit for Product Value
					$cosdr = array(
						'VNo'            =>  $invoice_no,
						'Vtype'          =>  'CIV',
						'VDate'          =>  $orderinfo->order_date,
						'COAID'          =>  $customer_headcode,
						'Narration'      =>  'Customer debit for Product Invoice#' . $invoice_no,
						'Debit'          =>  $finalill->bill_amount,
						'Credit'         =>  0,
						'StoreID'        =>  0,
						'IsPosted'       => 1,
						'CreateBy'       => $saveid,
						'CreateDate'     => $orderinfo->order_date,
						'IsAppove'       => 1
					);
					$this->db->insert('acc_transaction', $cosdr);
					//Store credit for Product Value
					$sc = array(
						'VNo'            =>  $invoice_no,
						'Vtype'          =>  'CIV',
						'VDate'          =>  $orderinfo->order_date,
						'COAID'          =>  10107,
						'Narration'      =>  'Inventory Credit for Product Invoice#' . $invoice_no,
						'Debit'          =>  0,
						'Credit'         =>  $finalill->bill_amount,
						'StoreID'        =>  0,
						'IsPosted'       => 1,
						'CreateBy'       => $saveid,
						'CreateDate'     => $orderinfo->order_date,
						'IsAppove'       => 1
					);
					$this->db->insert('acc_transaction', $sc);

					// Customer Credit for paid amount.
					$cc = array(
						'VNo'            =>  $invoice_no,
						'Vtype'          =>  'CIV',
						'VDate'          =>  $orderinfo->order_date,
						'COAID'          =>  $customer_headcode,
						'Narration'      =>  'Customer Credit for Product Invoice#' . $invoice_no,
						'Debit'          =>  0,
						'Credit'         =>  $finalill->bill_amount,
						'StoreID'        =>  0,
						'IsPosted'       => 1,
						'CreateBy'       => $saveid,
						'CreateDate'     => $orderinfo->order_date,
						'IsAppove'       => 1
					);
					$this->db->insert('acc_transaction', $cc);

					//Cash In hand Debit for paid value
					$cdv = array(
						'VNo'            =>  $invoice_no,
						'Vtype'          =>  'CIV',
						'VDate'          =>  $orderinfo->order_date,
						'COAID'          =>  $headcode,
						'Narration'      =>  'Cash in hand Debit For Invoice#' . $invoice_no,
						'Debit'          =>  $finalill->bill_amount,
						'Credit'         =>  0,
						'StoreID'        =>  0,
						'IsPosted'       =>  1,
						'CreateBy'       => $saveid,
						'CreateDate'     => $orderinfo->order_date,
						'IsAppove'       => 1
					);
					$this->db->insert('acc_transaction', $cdv);
					// Income for company							 
					$income = array(
						'VNo'            => "Sale" . $orderinfo->saleinvoice,
						'Vtype'          => 'Sales Products',
						'VDate'          =>  $orderinfo->order_date,
						'COAID'          => 303,
						'Narration'      => 'Sale Income For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
						'Debit'          => 0,
						'Credit'         => $finalill->bill_amount - $finalill->VAT, //purchase price asbe
						'IsPosted'       => 1,
						'CreateBy'       => $saveid,
						'CreateDate'     => $orderinfo->order_date,
						'IsAppove'       => 1
					);
					$this->db->insert('acc_transaction', $income);

					// Tax Pay for company							 
					$income = array(
						'VNo'            => "Sale" . $orderinfo->saleinvoice,
						'Vtype'          => 'Sales Products Vat',
						'VDate'          =>  $orderinfo->order_date,
						'COAID'          => 502030101,
						'Narration'      => 'Sale TAX For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
						'Debit'          => $finalill->VAT,
						'Credit'         => 0,
						'IsPosted'       => 1,
						'CreateBy'       => $saveid,
						'CreateDate'     => $orderinfo->order_date,
						'IsAppove'       => 1
					);
					$this->db->insert('acc_transaction', $income);
					$updatetDatakitchen = array('order_status' => 1);
					$this->db->where('order_id', $orderid);
					$this->db->update('customer_order', $updatetDatakitchen);
				}
			}
		} else {
			$mymsg = "You Order is Rejected";
			$bodymsg = "Order ID:" . $orderid . " Rejeceted with due Reason:" . $orderinfo->anyreason;
			if (!empty($orderinfo->marge_order_id)) {
				$margecancel = array('marge_order_id' => NULL);
				$this->db->where('order_id', $orderid);
				$this->db->update('customer_order', $margecancel);
			}
		}
		if ($acceptreject == 1) {
			$onlinebill = $this->db->select('*')->from('bill')->where('order_id', $orderid)->get()->row();
			if ($onlinebill->payment_method_id == 1 && $onlinebill->payment_method_id == 4) {
				$updatetData = array('anyreason' => $reason, 'nofification' => $status, 'orderacceptreject' => $acceptreject, 'order_status' => 2);
			} else {
				$updatetData = array('anyreason' => $reason, 'nofification' => $status, 'orderacceptreject' => $acceptreject);
			}
		} else {
			$updatetData = array('anyreason' => $reason, 'order_status' => 5, 'nofification' => $status, 'orderacceptreject' => 0);
			$taxinfos = $this->taxchecking();
			if (!empty($taxinfos)) {
				$this->db->where('relation_id', $orderid);
				$this->db->delete('tax_collection');
			}
		}
		$this->db->where('order_id', $orderid);
		$this->db->update('customer_order', $updatetData);
		/*PUSH Notification For Customer*/
		$icon = base_url('assets/img/applogo.png');
		$content = array(
			"en" => $bodymsg,
		);
		$title = array(
			"en" => $mymsg,
		);
		$fields = array(
			'app_id' => "208455d9-baca-4ed2-b6be-12b466a2efbd",
			'include_player_ids' => array($customerinfo->customer_token),
			'data' => array(
				'type' => "order place",
				'logo' => $icon
			),
			'contents' => $content,
			'headings' => $title,
		);

		$fields = json_encode($fields);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);
		if ($onprocesstab == 1) {
			$data['ongoingorder']  = $this->order_model->get_ongoingorder();
			$data['module'] = "ordermanage";
			$data['page']   = "updateorderlist";
			$this->load->view('ordermanage/ongoingorder', $data);
		}
	}
	public function cancelitem()
	{
		$taxinfos = $this->taxchecking();
		$orderid = $this->input->post('orderid');
		$itemid = $this->input->post('item', true);
		$varient = $this->input->post('varient', true);
		$kid = $this->input->post('kid', true);
		$reason = $this->input->post('reason', true);
		$orderinfo = $this->db->select("*")->from('customer_order')->where('order_id', $orderid)->get()->row();
		$setting = $this->db->select("*")->from('setting')->where('id', 2)->get()->row();
		if (!empty($taxinfos)) {
			$taxcolec = $this->db->select("*")->from('tax_collection')->where('relation_id', $orderid)->get()->row();
		}

		$itemids = explode(',', $itemid);
		$varientids = explode(',', $varient);
		$allfoods = "";
		$i = 0;
		foreach ($itemids as $sitem) {
			$vaids = $varientids[$i];
			$olditm = $this->db->select("*")->from('order_menu')->where('order_id', $orderid)->where('menu_id', $sitem)->where('varientid', $vaids)->get()->row();
			$foodname = $this->db->select("item_foods.*,variant.variantName,variant.price")->from('variant')->join('item_foods', 'item_foods.ProductsID=variant.menuid', 'left')->where('variant.variantid', $vaids)->get()->row();
			$iteminfo = $this->order_model->getiteminfo($sitem);

			if ($olditm->price > 0) {
				$foodprice = $olditm->price;
			} else {
				$foodprice = $foodname->price;
			}
			if ($foodname->OffersRate > 0) {
				$discount = $foodprice * $foodname->OffersRate / 100;
				$fprice = $foodprice - $discount;
			} else {
				$discount = 0;
				$fprice = $foodprice;
			}
			$pvat = 0;
			if (!empty($taxinfos)) {
				$tx = 0;
				$multiplletax = array();
				foreach ($taxinfos as $taxinfo) {
					$fildname = 'tax' . $tx;
					if (!empty($iteminfo->$fildname)) {
						$vatcalc = $fprice * $iteminfo->$fildname / 100;
					} else {
						$vatcalc = $fprice * $taxinfo['default_value'] / 100;
					}
					$updatetax = array($fildname => $taxcolec->$fildname - $vatcalc);
					$this->db->where('relation_id', $orderid);
					$this->db->update('tax_collection', $updatetax);
					$pvat = $pvat + $vatcalc;
					$vatcalc = 0;
					$tx++;
				}
			} else {
				$vatcalc = $fprice * $iteminfo->productvat / 100;
				$pvat = $pvat + $vatcalc;
			}
			$anonsfprm = 0;
			$adtvat = 0;
			if (!empty($olditm->add_on_id)) {
				if (!empty($taxinfos)) {
					$addonsarray = explode(',', $olditm->add_on_id);
					$addonsqtyarray = explode(',', $olditm->addonsqty);
					$getaddonsdatas = $this->db->select('*')->from('add_ons')->where_in('add_on_id', $addonsarray)->get()->result_array();
					$addn = 0;
					foreach ($getaddonsdatas as $getaddonsdata) {
						$tax1 = 0;
						foreach ($taxinfos as $taxainfo) {
							$fildaname = 'tax' . $tax1;
							if (!empty($getaddonsdata[$fildaname])) {
								$avatcalc = ($getaddonsdata['price'] * $addonsqtyarray[$addn]) * $getaddonsdata[$fildaname] / 100;
								$avtax = $taxcolec->$fildname - $avatcalc;
								$addonsupdatetax = array($fildname => $avtax);
								$this->db->where('relation_id', $orderid);
								$this->db->update('tax_collection', $addonsupdatetax);
							} else {
								$avatcalc = ($getaddonsdata['price'] * $addonsqtyarray[$addn]) * $taxainfo['default_value'] / 100;
								$avtax = $taxcolec->$fildname - $avatcalc;
								$addonsupdatetax = array($fildname => $avtax);
								$this->db->where('relation_id', $orderid);
								$this->db->update('tax_collection', $addonsupdatetax);
							}


							$adtvat =  $adtvat + $avatcalc;
							$tax1++;
						}
						$addonsprm = $getaddonsdata['price'] * $addonsqtyarray[$addn];
						$anonsfprm = $addonsprm + $anonsfprm;
						$addn++;
					}
				}
			}

			$allfoods .= $foodname->ProductName . ' Varient: ' . $foodname->variantName . ",";
			$this->db->where('order_id', $orderid)->where('menu_id', $sitem)->where('varientid', $vaids)->delete('order_menu');

			$finalbillinfo = $this->db->select("*")->from('bill')->where('order_id', $orderid)->get()->row();


			if ($setting->service_chargeType == 1) {
				$subtotal = $finalbillinfo->total_amount - ($fprice + $anonsfprm);
				$fsd = $subtotal * $setting->servicecharge / 100;
			} else {
				$subtotal = $finalbillinfo->total_amount - ($fprice + $anonsfprm);
				$fsd = $setting->servicecharge;
			}

			if (empty($taxinfos)) {
				if ($settinginfo->vat > 0) {
					$calvat = $itemtotal * $settinginfo->vat / 100;
				} else {
					$calvat = $pvat;
				}
			} else {
				$calvat = $pvat;
			}
			$fvat = $finalbillinfo->VAT - ($calvat + $adtvat);
			$grdiscount = $finalbillinfo->discount - $discount;
			$fbillamount = $subtotal + $fvat + $fsd - $grdiscount;
			$updatebill = array('total_amount' => $subtotal, 'discount' => $grdiscount, 'service_charge' => $fsd, 'VAT' => $fvat, 'bill_amount' => $fbillamount);


			$this->db->where('order_id', $orderid);
			$this->db->update('bill', $updatebill);

			$updateorderinfo = array('totalamount' => $fbillamount);
			$this->db->where('order_id', $orderid);
			$this->db->update('customer_order', $updateorderinfo);

			$i++;
		}
		$allfoods = trim($allfoods, ',');
		$customerinfo = $this->db->select("*")->from('customer_info')->where('customer_id', $orderinfo->customer_id)->get()->row();
		$mymsg = "You Item is Rejected";
		$bodymsg = "Order ID: " . $orderid . " Item Name: " . $allfoods . " Rejeceted with due Reason:" . $reason;
		/*PUSH Notification For Customer*/
		$icon = base_url('assets/img/applogo.png');
		$content = array(
			"en" => $bodymsg,
		);
		$title = array(
			"en" => $mymsg,
		);
		$fields = array(
			'app_id' => "208455d9-baca-4ed2-b6be-12b466a2efbd",
			'include_player_ids' => array($customerinfo->customer_token),
			'data' => array(
				'type' => "order place",
				'logo' => $icon
			),
			'contents' => $content,
			'headings' => $title,
		);

		$fields = json_encode($fields);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);

		$afterorderinfo = $this->db->select("*")->from('order_menu')->where('order_id', $orderid)->get()->row();
		if (empty($afterorderinfo)) {
			$updatetData = array('anyreason' => "All item no available", 'order_status' => 5, 'nofification' => 1, 'orderacceptreject' => 0);
			$this->db->where('order_id', $orderid);
			$this->db->update('customer_order', $updatetData);
		}
		$alliteminfo = $this->order_model->customerorderkitchen($orderid, $kid);
		$singleorderinfo = $this->order_model->kitchen_ajaxorderinfoall($orderid);

		$data['orderinfo'] = $singleorderinfo;
		$data['kitchenid'] = $kid;
		$data['iteminfo'] = $alliteminfo;
		$data['module'] = "ordermanage";
		$data['page']   = "kitchen_view";
		$this->load->view('kitchen_view', $data);
	}

	public function printtoken()
	{

		$orderid = $this->input->post('orderid');
		$kid = $this->input->post('kid', true);
		$itemid = $this->input->post('itemid', true);
		$varient = $this->input->post('varient', true);
		$itemids = explode(',', $itemid);
		$varientids = explode(',', $varient);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$alliteminfo = $this->order_model->customerorderkitchen($orderid, $kid);
		$singleorderinfo = $this->order_model->kitchen_ajaxorderinfoall($orderid);
		$slitem = array_filter($itemids);
		if (!empty($slitem)) {
			$data['printitem'] = $this->order_model->customerprintkitchen($orderid, $kid, $itemids, $varientids);
		} else {
			$data['printitem'] = '';
		}
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $singleorderinfo->customer_id));
		if (!empty($singleorderinfo->table_no)) {
			$data['tableinfo']      = $this->order_model->read('*', 'rest_table', array('tableid' => $singleorderinfo->table_no));
		} else {
			$data['tableinfo'] = '';
		}
		$data['orderinfo'] = $singleorderinfo;
		$data['kitchenid'] = $kid;
		$data['iteminfo'] = $alliteminfo;
		$data['allcancelitem'] = $this->order_model->customercancelkitchen($orderid, $kid);
		$data['module'] = "ordermanage";
		$data['page']   = "postoken3";
		$this->load->view('postoken3', $data);
	}

	public function onlinellorder()
	{
		$list = $this->order_model->get_completeonlineorder();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $rowdata) {
			if ($rowdata->bill_status == 1) {
				$paymentyst = "Paid";
			} else {
				$paymentyst = "Unpaid";
			}
			$no++;
			$row = array();
			$update = '';
			$print = '';
			$details = '';
			$paymentbtn = '';
			$cancelbtn = '';
			$rejectbtn = '';
			$posprint = '';
			$shipinfo == '';
			if (!empty($rowdata->shipping_type)) {
				$shipinfo = $this->order_model->read('*', 'shipping_method', array('ship_id' => $rowdata->shipping_type));
			}
			$shippingname = '';
			$shippingdate = '';
			if (!empty($shipinfo)) {
				$shippingname = $shipinfo->shipping_method;
				$shippingdate = $rowdata->shipping_date;
			}
			if ($this->permission->method('ordermanage', 'update')->access()) :
				if ($rowdata->order_status != 5) {
					$update = '<a href="javascript:;" onclick="editposorder(' . $rowdata->order_id . ',3)" class="btn btn-xs btn-success btn-sm mr-1" id="table-today-online-' . $rowdata->order_id . '" data-toggle="tooltip" data-placement="left" title="Update"><i class="ti-pencil"></i></a>&nbsp;&nbsp;';
				}
			endif;
			if ($this->permission->method('ordermanage', 'read')->access()) :
				$details = '&nbsp;<a href="javascript:;" onclick="detailspop(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" data-placement="left"  title="" data-original-title="Details"><i class="fa fa-eye"></i></a>&nbsp;';
				$posprint = '<a href="javascript:;" onclick="pospageprint(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip"  data-placement="left" title="" data-original-title="Pos Invoice"><i class="fa fa-window-maximize"></i></a>';
			endif;
			if ($this->permission->method('ordermanage', 'delete')->access()) :
				if ($rowdata->order_status != 5) {
					$rejectbtn = '<a href="javascript:;" id="cancelicon_' . $rowdata->order_id . '" data-id="' . $rowdata->order_id . '" data-type="' . $rowdata->bill_status . '"  class="btn btn-xs btn-danger btn-sm mr-1 cancelorder" data-toggle="tooltip" data-placement="left" title="" data-original-title="Cancel"><i class="fa fa-trash-o" aria-hidden="true"></i></a>&nbsp;';
				}
				if ($rowdata->orderacceptreject == '') {
					$cancelbtn = '<a href="javascript:;" id="accepticon_' . $rowdata->order_id . '" data-id="' . $rowdata->order_id . '" data-type="' . $rowdata->bill_status . '"  class="btn btn-xs btn-danger btn-sm mr-1 aceptorcancel" data-toggle="tooltip" data-placement="left" title="" data-original-title="Accept or Cancel"><i class="fa fa-info-circle" aria-hidden="true"></i></a>&nbsp;';
				}
				if ($rowdata->bill_status == 0 && $rowdata->orderacceptreject != 0) {
					$paymentbtn = '<a href="javascript:;" onclick="createMargeorder(' . $rowdata->order_id . ',1)" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" id="table-today-online-accept-' . $rowdata->order_id . '" data-placement="left" title="" data-original-title="Make Payments"><i class="fa fa-window-restore"></i></a>&nbsp;';
				}
			endif;


			$row[] = $no;
			$row[] = $rowdata->saleinvoice;
			$row[] = $rowdata->customer_name;
			$row[] = $shippingname;
			$row[] = $shippingdate;
			$row[] = $rowdata->first_name . $rowdata->last_name;
			$row[] = $rowdata->tablename;
			$row[] = $paymentyst;
			$row[] = $rowdata->order_date;
			$row[] = $rowdata->totalamount;
			$row[] = $cancelbtn . $rejectbtn . $paymentbtn . $update . $posprint . $details;
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->order_model->count_allonlineorder(),
			"recordsFiltered" => $this->order_model->count_filtertonlineorder(),
			"data" => $data,
		);
		echo json_encode($output);
	}
	public function allqrorder()
	{
		$list = $this->order_model->get_qronlineorder();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $rowdata) {
			if ($rowdata->bill_status == 1) {
				$paymentyst = "Paid";
			} else {
				$paymentyst = "Unpaid";
			}
			$no++;
			$row = array();
			$update = '';
			$print = '';
			$details = '';
			$paymentbtn = '';
			$cancelbtn = '';
			$rejectbtn = '';
			$posprint = '';
			if ($this->permission->method('ordermanage', 'update')->access()) :
				$update = '<a href="javascript:;" onclick="editposorder(' . $rowdata->order_id . ',4)" class="btn btn-xs btn-success btn-sm mr-1" id="table-today-online-' . $rowdata->order_id . '" data-toggle="tooltip" data-placement="left" title="Update"><i class="ti-pencil"></i></a>&nbsp;&nbsp;';
			endif;
			if ($this->permission->method('ordermanage', 'read')->access()) :
				$details = '&nbsp;<a onclick="detailspop(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-placement="left" title="" data-original-title="Details" data-toggle="modal" data-target="#orderdetailsp" data-dismiss="modal"><i class="fa fa-eye"></i></a>&nbsp;';
				$posprint = '<a href="javascript:;" onclick="pospageprint(' . $rowdata->order_id . ')" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip"  data-placement="left" title="" data-original-title="Pos Invoice"><i class="fa fa-window-maximize"></i></a>';
			endif;
			if ($this->permission->method('ordermanage', 'delete')->access()) :
				if ($rowdata->order_status != 5) {
					$rejectbtn = '<a href="javascript:;" id="cancelicon_' . $rowdata->order_id . '" data-id="' . $rowdata->order_id . '" class="btn btn-xs btn-danger btn-sm mr-1 cancelorder" data-toggle="tooltip" data-placement="left" title="" data-original-title="Cancel"><i class="fa fa-trash-o" aria-hidden="true"></i></a>&nbsp;';
				}
				if ($rowdata->orderacceptreject == '') {
					$cancelbtn = '<a href="javascript:;" id="accepticon_' . $rowdata->order_id . '" data-id="' . $rowdata->order_id . '" class="btn btn-xs btn-danger btn-sm mr-1 aceptorcancel" data-toggle="tooltip" data-placement="left" title="" data-original-title="Accept or Cancel"><i class="fa fa-info-circle" aria-hidden="true"></i></a>&nbsp;';
				}
				if ($rowdata->bill_status == 0 && $rowdata->orderacceptreject != 0) {
					$paymentbtn = '<a href="javascript:;" onclick="createMargeorder(' . $rowdata->order_id . ',1)" class="btn btn-xs btn-success btn-sm mr-1" data-toggle="tooltip" id="table-today-online-accept-' . $rowdata->order_id . '" data-placement="left" title="" data-original-title="Make Payments"><i class="fa fa-window-restore"></i></a>&nbsp;';
				}
			endif;


			$row[] = $no;
			$row[] = $rowdata->saleinvoice;
			$row[] = $rowdata->customer_name;
			$row[] = "QR Customer";
			$row[] = $rowdata->first_name . $rowdata->last_name;
			$row[] = $rowdata->tablename;
			$row[] = $paymentyst;
			$row[] = $rowdata->order_date;
			$row[] = $rowdata->totalamount;
			$row[] = $cancelbtn . $rejectbtn . $paymentbtn . $update . $posprint . $details;
			$row[] = $rowdata->isupdate;
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->order_model->count_allqrorder(),
			"recordsFiltered" => $this->order_model->count_filtertqrorder(),
			"data" => $data,
		);
		echo json_encode($output);
	}
	public function pendingorder()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('pending_order');
		$saveid = $this->session->userdata('id');

		$status = 1;
		#-------------------------------#       
		#
		#pagination starts
		#
		$config["base_url"] = base_url('ordermanage/order/orderlist');
		$config["total_rows"]  = $this->order_model->count_canorder($status);
		$config["per_page"]    = 25;
		$config["uri_segment"] = 4;
		$config["last_link"] = display('sLast');
		$config["first_link"] = display('sFirst');
		$config['next_link'] = display('sNext');
		$config['prev_link'] = display('sPrevious');
		$config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
		$config['full_tag_close'] = "</ul>";
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		$config['next_tag_open'] = "<li>";
		$config['next_tag_close'] = "</li>";
		$config['prev_tag_open'] = "<li>";
		$config['prev_tagl_close'] = "</li>";
		$config['first_tag_open'] = "<li>";
		$config['first_tagl_close'] = "</li>";
		$config['last_tag_open'] = "<li>";
		$config['last_tagl_close'] = "</li>";
		/* ends of bootstrap */
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data["iteminfo"] = $this->order_model->pendingorder($config["per_page"], $page, $status);
		$data["links"] = $this->pagination->create_links();
		$data['pagenum'] = $page;
		#
		#pagination ends
		# 
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data["links"] = '';
		$data['pagenum'] = 0;
		$data['module'] = "ordermanage";
		$data['page']   = "pendingorder";
		echo Modules::run('template/layout', $data);
	}
	public function processing()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('processing_order');
		$saveid = $this->session->userdata('id');
		$status = 2;
		$data['iteminfo']      = $this->order_model->pendingorder($status);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "processing";
		echo Modules::run('template/layout', $data);
	}
	public function completelist()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('complete_order');
		$saveid = $this->session->userdata('id');
		$status = 1;
		$config["base_url"] = base_url('ordermanage/order/completelist');
		$config["total_rows"]  = $this->order_model->count_comorder($status);
		$config["per_page"]    = 25;
		$config["uri_segment"] = 4;
		$config["last_link"] = display('sLast');
		$config["first_link"] = display('sFirst');
		$config['next_link'] = display('sNext');
		$config['prev_link'] = display('sPrevious');
		$config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
		$config['full_tag_close'] = "</ul>";
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		$config['next_tag_open'] = "<li>";
		$config['next_tag_close'] = "</li>";
		$config['prev_tag_open'] = "<li>";
		$config['prev_tagl_close'] = "</li>";
		$config['first_tag_open'] = "<li>";
		$config['first_tagl_close'] = "</li>";
		$config['last_tag_open'] = "<li>";
		$config['last_tagl_close'] = "</li>";
		/* ends of bootstrap */
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data["iteminfo"] = $this->order_model->completeorder($config["per_page"], $page, $status);
		$data["links"] = $this->pagination->create_links();
		$data['taxinfos'] = $this->taxchecking();
		$data['pagenum'] = $page;
		#
		#pagination ends
		# 
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['module'] = "ordermanage";
		$data['page']   = "pendingorder";
		echo Modules::run('template/layout', $data);
	}
	public function cancellist()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('cancel_order');
		$saveid = $this->session->userdata('id');

		$status = 5;
		#-------------------------------#       
		#
		#pagination starts
		#
		$config["base_url"] = base_url('ordermanage/order/orderlist');
		$config["total_rows"]  = $this->order_model->count_canorder($status);
		$config["per_page"]    = 25;
		$config["uri_segment"] = 4;
		$config["last_link"] = display('sLast');
		$config["first_link"] = display('sFirst');
		$config['next_link'] = display('sNext');
		$config['prev_link'] = display('sPrevious');
		$config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
		$config['full_tag_close'] = "</ul>";
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		$config['next_tag_open'] = "<li>";
		$config['next_tag_close'] = "</li>";
		$config['prev_tag_open'] = "<li>";
		$config['prev_tagl_close'] = "</li>";
		$config['first_tag_open'] = "<li>";
		$config['first_tagl_close'] = "</li>";
		$config['last_tag_open'] = "<li>";
		$config['last_tagl_close'] = "</li>";
		/* ends of bootstrap */
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data["iteminfo"] = $this->order_model->pendingorder($config["per_page"], $page, $status);
		$data["links"] = $this->pagination->create_links();
		$data['pagenum'] = $page;
		#
		#pagination ends
		# 
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "pendingorder";
		echo Modules::run('template/layout', $data);
	}
	public function updateorder($id)
	{
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');

		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);

		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$data['categorylist']   = $this->order_model->category_dropdown();
		$data['allcategorylist']  = $this->order_model->allcat_dropdown();
		$data['customerlist']  = $this->order_model->customer_dropdown();
		$data['curtomertype']   = $this->order_model->ctype_dropdown();
		$data['waiterlist']     = $this->order_model->waiter_dropdown();
		$data['tablelist']      = $this->order_model->table_dropdown();
		$data['thirdpartylist']  = $this->order_model->thirdparty_dropdown();
		$data['banklist']      = $this->order_model->bank_dropdown();
		$data['terminalist']   = $this->order_model->allterminal_dropdown();
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$data['itemlist']      =  $this->order_model->allfood2();
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['possetting'] = $this->order_model->read('*', 'tbl_posetting', array('possettingid' => 1));
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$this->load->view('updateorder', $data);
	}

	public function otherupdateorder($id)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');

		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);

		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$data['categorylist']   = $this->order_model->category_dropdown();
		$data['allcategorylist']  = $this->order_model->allcat_dropdown();
		$data['customerlist']  = $this->order_model->customer_dropdown();
		$data['curtomertype']   = $this->order_model->ctype_dropdown();
		$data['waiterlist']     = $this->order_model->waiter_dropdown();
		$data['tablelist']      = $this->order_model->table_dropdown();
		$data['thirdpartylist']  = $this->order_model->thirdparty_dropdown();
		$data['banklist']      = $this->order_model->bank_dropdown();
		$data['terminalist']   = $this->order_model->allterminal_dropdown();
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$data['itemlist']      =  $this->order_model->allfood2();
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['possetting'] = $this->order_model->read('*', 'tbl_posetting', array('possettingid' => 1));
		$data['taxinfos'] = $this->taxchecking();
		$mtype = $this->order_model->read('*', 'membership', array('id' =>  $data['customerinfo']->membership_type));
		$data['title'] = "posinvoiceloading2";
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderother";
		echo Modules::run('template/layout', $data);
	}
	public function modifyoreder()
	{
		$orderid                 = $this->input->post('updateid');
		$dataup['cutomertype']   = $this->input->post('ctypeid');
		$dataup['waiter_id']     = $this->input->post('waiter', true);
		$dataup['isthirdparty']  = $this->input->post('delivercom', true);
		$dataup['table_no']      = $this->input->post('tableid', true);
		$dataup['order_status']  = $this->input->post('orderstatus', true);
		$dataup['totalamount']   = $this->input->post('orginattotal', true);

		$updared = $this->order_model->update_info('customer_order', $dataup, 'order_id', $orderid);
		$taxinfos = $this->taxchecking();
		if (!empty($taxinfos)) {
			$multiplletaxvalue = $this->input->post('multiplletaxvalue', true);
			$multiplletaxdata = unserialize($multiplletaxvalue);

			$updared = $this->order_model->update_info('tax_collection', $multiplletaxdata, 'relation_id', $orderid);
		}
		$this->order_model->payment_info($orderid);

		$logData = array(
			'action_page'         => "Pending Order",
			'action_done'     	 => "Insert Data",
			'remarks'             => "Pending Order is Update",
			'user_name'           => $this->session->userdata('fullname'),
			'entry_date'          => date('Y-m-d H:i:s'),
		);
		$this->logs_model->log_recorded($logData);

		$this->session->set_flashdata('message', display('update_successfully'));

		$successfull =  array('success' => 'success', 'msg' => display('update_successfully'), 'orderid' => $orderid, 'tokenmsg' => display('do_print_token'));
		echo json_encode($successfull);
		exit;

		redirect("ordermanage/order/pos_invoice/" . $orderid);
	}
	public function ajaxupdateoreder()
	{
		$orderid                 = $this->input->post('orderid');
		$status                 = $this->input->post('status', true);

		$this->order_model->payment_info($orderid);

		$logData = array(
			'action_page'         => "Order List",
			'action_done'     	 => "Insert Data",
			'remarks'             => "Order is Update",
			'user_name'           => $this->session->userdata('fullname'),
			'entry_date'          => date('Y-m-d H:i:s'),
		);
		$this->logs_model->log_recorded($logData);
		$this->session->set_flashdata('message', display('update_successfully'));
		redirect("ordermanage/order/updateorder/" . $orderid);
	}


	public function changestatus()
	{
		$orderid                 = $this->input->post('orderid');
		$status                 = $this->input->post('status', true);
		$paytype                 = $this->input->post('paytype', true);
		$cterminal                 = $this->input->post('cterminal', true);
		$mybank                  = $this->input->post('mybank', true);
		$mydigit                 = $this->input->post('mydigit', true);
		$paidamount              = $this->input->post('paid', true);

		$orderinfo = $this->order_model->uniqe_order_id($orderid);

		$duevalue = (round($orderinfo->totalamount) - $orderinfo->customerpaid);
		if ($paidamount == $duevalue || $duevalue <  $paidamount) {
			$paidamount  = $paidamount + $orderinfo->customerpaid;
			$status = 4;
		} else {
			$paidamount  = $paidamount + $orderinfo->customerpaid;

			$status = 3;
		}

		$updatetData = array(
			'order_status'     => $status,
		);
		$this->db->where('order_id', $orderid);
		$this->db->update('customer_order', $updatetData);
		//Update Bill Table
		$updatetbill = array(
			'bill_status'           => 1,
			'payment_method_id'     => $paytype,
		);
		$this->db->where('order_id', $orderid);
		$this->db->update('bill', $updatetbill);
		$billinfo = $this->db->select('*')->from('bill')->where('order_id', $orderid)->get()->row();
		if (!empty($billinfo)) {
			$billid = $billinfo->bill_id;
			if ($paidamount >= 0) {
				$paidData = array(
					'customerpaid'     => $paidamount
				);
				$this->db->where('order_id', $orderid);
				$this->db->update('customer_order', $paidData);
			} else {
				$paidData = array(
					'customerpaid'     => $billinfo->bill_amount
				);
				$this->db->where('order_id', $orderid);
				$this->db->update('customer_order', $paidData);
			}
			if ($paytype == 1) {
				$billpayment = $this->db->select('*')->from('bill_card_payment')->where('bill_id', $billid)->get()->row();
				if (!empty($billpayment)) {
					$updatetcardinfo = array(
						'card_no'           => $mydigit,
						'terminal_name'     => $cterminal,
						'bank_name'         => $mybank
					);

					$this->db->where('bill_id', $billid);
					$this->db->update('bill_card_payment', $updatetcardinfo);
				} else {
					$cardinfo = array(
						'bill_id'			    =>	$billid,
						'card_no'		        =>	$mydigit,
						'terminal_name'		    =>	$cterminal,
						'bank_name'	            =>	$mybank,
					);

					$this->db->insert('bill_card_payment', $cardinfo);
				}
			}
		}
		if ($status == 4) {
			$customerinfo = $this->db->select('*')->from('customer_info')->where('customer_id', $billinfo->customer_id)->get()->row();
		}
		$orderinfo = $this->db->select('*')->from('customer_order')->where('order_id', $orderid)->get()->row();
		$cusinfo = $this->db->select('*')->from('customer_info')->where('customer_id', $orderinfo->customer_id)->get()->row();

		// Income for company
		$saveid = $this->session->userdata('id');
		$income = array(
			'VNo'            => $orderinfo->saleinvoice,
			'Vtype'          => 'Sales Products',
			'VDate'          =>  $orderinfo->order_date,
			'COAID'          => 303,
			'Narration'      => 'Sale Income For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
			'Debit'          => 0,
			'Credit'         => $orderinfo->totalamount, //purchase price asbe
			'IsPosted'       => 1,
			'CreateBy'       => $saveid,
			'CreateDate'     => $orderinfo->order_date,
			'IsAppove'       => 1
		);
		$this->db->insert('acc_transaction', $income);
		$logData = array(
			'action_page'         => "Order List",
			'action_done'     	 => "Insert Data",
			'remarks'             => "Order is Update",
			'user_name'           => $this->session->userdata('fullname'),
			'entry_date'          => date('Y-m-d H:i:s'),
		);
		$this->logs_model->log_recorded($logData);
		$data['ongoingorder']  = $this->order_model->get_ongoingorder();
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderlist";
		$view = $this->posprintdirect($orderid);

		echo $view;
		exit;
		$this->load->view('ordermanage/ongoingorder', $data); //work
	}
	public function posprintview($id)
	{
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$data['cashierinfo']   = $this->order_model->read('*', 'user', array('id' => $data['billinfo']->create_by));
		$settinginfo = $this->order_model->settinginfo();
		if ($settinginfo->printtype == 1 || $settinginfo->printtype == 3) {
			$updatetData = array('invoiceprint' => 2);
			$this->db->where('order_id', $id);
			$this->db->update('customer_order', $updatetData);
		}
		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['tableinfo'] = $this->order_model->read('*', 'rest_table', array('tableid' => $customerorder->table_no));
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "posinvoice";
		$this->load->view('posinvoiceview', $data);
	}
	public function onprocessajax()
	{
		$data['ongoingorder']  = $this->order_model->get_ongoingorder();
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderlist";
		$this->load->view('ordermanage/ongoingorder', $data);
	}

	public function deletetocart()
	{
		$rowid = $this->input->post('mid');
		$orderid = $this->input->post('orderid');
		$pid = $this->input->post('pid', true);
		$vid = $this->input->post('vid', true);
		$qty = $this->input->post('qty', true);
		$this->order_model->cartitem_delete($rowid, $orderid);
		$checkcancelitem = $this->order_model->check_cancelitem($orderid, $pid, $vid);
		if (empty($checkcancelitem)) {
			$datacancel = array(
				'orderid'			    =>	$orderid,
				'foodid'		        =>	$pid,
				'quantity'	        	=>	$qty,
				'varientid'		    	=>	$vid,
			);
			$this->db->insert('tbl_cancelitem', $datacancel);
		} else {
			$udatacancel = array(
				'quantity'       => $checkcancelitem->quantity + $qty,
			);
			$this->db->where('orderid', $orderid);
			$this->db->where('foodid', $pid);
			$this->db->where('varientid', $vid);
			$this->db->update('tbl_cancelitem', $udatacancel);
		}
		$iteminfo = $this->order_model->customerorder($orderid);
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$data['billinfo']	   = $this->order_model->billinfo($orderid);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$i = 0;
		$totalamount = 0;
		$subtotal = 0;
		foreach ($iteminfo as $item) {
			$adonsprice = 0;
			$discount = 0;
			$itemprice = $item->price * $item->menuqty;
			if (!empty($item->add_on_id)) {
				$addons = explode(",", $item->add_on_id);
				$addonsqty = explode(",", $item->addonsqty);
				$x = 0;
				foreach ($addons as $addonsid) {
					$adonsinfo = $this->order_model->read('*', 'add_ons', array('add_on_id' => $addonsid));
					$adonsprice = $adonsprice + $adonsinfo->price * $addonsqty[$x];
					$x++;
				}
				$nittotal = $adonsprice;
				$itemprice = $itemprice + $adonsprice;
			} else {
				$nittotal = 0;
			}
			$totalamount = $totalamount + $nittotal;
			$subtotal = $subtotal + $item->price * $item->menuqty;
		}
		$itemtotal = $totalamount + $subtotal;
		$calvat = $itemtotal * $settinginfo->vat / 100;
		$updatedprice = $calvat + $itemtotal - $discount;
		$postData = array(
			'order_id'        => $orderid,
			'totalamount'     => $updatedprice,
		);
		$this->order_model->update_order($postData);
		$data['orderinfo']  	   = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$data['iteminfo']       = $this->order_model->customerorder($orderid);
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderlist";
		$this->load->view('ordermanage/updateorderlist', $data);
	}
	public function addtocartupdate()
	{
		$catid = $this->input->post('catid');
		$pid = $this->input->post('pid');
		$sizeid = $this->input->post('sizeid');
		$totalvarient = $this->input->post('totalvarient', true);
		$customqty = $this->input->post('customqty', true);
		$isgroup = $this->input->post('isgroup', true);
		$itemname = $this->input->post('itemname', true);
		$size = $this->input->post('varientname', true);
		$qty = $this->input->post('qty', true);
		// For cart updates by clicking items, ensure we only add 1
		// Force quantity to 1 for cart update clicks to prevent multiplication
		$qty = 1;
		$price = $this->input->post('price', true);
		$addonsid = $this->input->post('addonsid');
		$allprice = $this->input->post('allprice', true);
		$adonsunitprice = $this->input->post('adonsunitprice', true);
		$adonsqty = $this->input->post('adonsqty', true);
		$adonsname = $this->input->post('adonsname', true);
		$orderid = $this->input->post('orderid');
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;


		$new_str = str_replace(',', '0', $addonsid);
		$new_str2 = str_replace(',', '0', $adonsqty);
		$uaid = $pid . $new_str . $sizeid;
		if (!empty($addonsid)) {
			$joinid = trim($addonsid, ',');

			$aids = $addonsid;
			$aqty = $adonsqty;
			$aname = $adonsname;
			$aprice = $adonsunitprice;
			$atprice = $allprice;
			$grandtotal = $price;
		} else {
			$grandtotal = $price;
			$aids = '';
			$aqty = '';
			$aname = '';
			$aprice = '';
		}
		if ($isgroup == 1) {
			$orderchecked = $this->order_model->check_ordergroup($orderid, $pid, $sizeid, $uaid);
			if (empty($orderchecked)) {
				$groupinfo = $this->db->select('*')->from('tbl_groupitems')->where('gitemid', $pid)->get()->result();
				foreach ($groupinfo as $grouprow) {
					$data3 = array(
						'order_id'				=>	$orderid,
						'menu_id'		        =>	$grouprow->items,
						'groupmid'		        =>	$pid,
						'menuqty'	        	=>	$grouprow->item_qty * $qty,
						'price'	       			=>  $price,
						'addonsuid'	        	=>	$uaid,
						'add_on_id'	        	=>	$aids,
						'addonsqty'	        	=>	$aqty,
						'varientid'		    	=>	$grouprow->varientid,
						'groupvarient'		    =>	$sizeid,
						'qroupqty'		    	=>	$qty,
						'isgroup'		    	=>	1,
						'isupdate'     			=> 1,
					);
					$this->order_model->new_entry($data3);
				}
			} else {
				$groupinfo = $this->db->select('*')->from('tbl_groupitems')->where('gitemid', $pid)->get()->result();
				foreach ($groupinfo as $grouprow) {
					$udata2 = array(
						'qroupqty'      => $qty,
						'add_on_id'     => $aids,
						'addonsqty'     => $aqty,
						'menuqty'       => $grouprow->item_qty * $qty,
					);
					$this->db->where('order_id', $orderid);
					$this->db->where('menu_id', $grouprow->items);
					$this->db->where('groupmid', $pid);
					$this->db->where('groupvarient', $sizeid);
					$this->db->where('varientid', $grouprow->varientid);
					$this->db->where('addonsuid', $uaid);
					$this->db->update('order_menu', $udata2);
				}
				$checkcancelitem = $this->order_model->check_cancelitem($orderid, $pid, $sizeid);

				$reqqty = $qty - $orderchecked->qroupqty;
				if ($reqqty > 0) {
					$data4 = array(
						'ordid'				  	=>	$orderid,
						'menuid'		        =>	$pid,
						'qty'	        	    =>	$qty - $orderchecked->qroupqty,
						'addonsid'	        	=>	$aids,
						'addonsuid'     		=>  $uaid,
						'adonsqty'	        	=>	$aqty,
						'varientid'		    	=>	$sizeid,
						'insertdate'		    =>	date('Y-m-d'),
					);
					$this->db->insert('tbl_updateitems', $data4);
					if (empty($checkcancelitem)) {
						$datacancel = array(
							'orderid'			    =>	$orderid,
							'foodid'		        =>	$pid,
							'quantity'	        	=>	$qty - $orderchecked->qroupqty,
							'varientid'		    	=>	$sizeid,
						);
						$this->db->insert('tbl_cancelitem', $datacancel);
					} else {
						$nqty = $qty - $orderchecked->qroupqty;
						$udatacancel = array(
							'quantity'       => $checkcancelitem->quantity + $nqty,
						);
						$this->db->where('orderid', $orderid);
						$this->db->where('foodid', $pid);
						$this->db->where('varientid', $sizeid);
						$this->db->update('tbl_cancelitem', $udatacancel);
					}
				}
			}
		} else {
			$orderchecked = $this->order_model->check_order($orderid, $pid, $sizeid, $uaid);
			if (empty($orderchecked)) {
				$postInfo = array(
					'order_id'      => $orderid,
					'menu_id'       => $pid,
					'menuqty'       => $qty,
					'price'	       => $price,
					'addonsuid'     => $uaid,
					'add_on_id'     => $aids,
					'addonsqty'     => $aqty,
					'varientid'     => $sizeid,
					'isupdate'     => 1,
				);
				$this->order_model->new_entry($postInfo);
			} else {
				$checkcancelitem = $this->order_model->check_cancelitem($orderid, $pid, $sizeid);
				$adonsarray = explode(',', $addonsid);
				$adonsqtyarray = explode(',', $adonsqty);
				$adqty = explode(',', $orderchecked->addonsqty);
				$x = 0;
				$finaladdonsqty = '';
				foreach ($adonsarray as $singleaddons) {
					$totalaqty = (float)$adonsqtyarray[$x] + (float)$adqty[$x];
					$finaladdonsqty .= $totalaqty . ',';
					$x++;
				}
				if (!empty($adonsarray)) {
					$aqty = trim($finaladdonsqty, ',');
				}

				$adqty = array();
				$adprice = array();
				if ((empty($addonsid)) && ($customqty == 0) && ($totalvarient == 1)) {
					$udata = array(
						'menuqty'       => $orderchecked->menuqty + $qty,
						'add_on_id'     => $aids,
						'addonsqty'     => $aqty,
					);
				} else {
					$udata = array(
						'menuqty'       => $orderchecked->menuqty + $qty,
						'add_on_id'     => $aids,
						'addonsqty'     => $aqty,
					);
				}

				$this->db->where('order_id', $orderid);
				$this->db->where('menu_id', $pid);
				$this->db->where('varientid', $sizeid);
				$this->db->where('addonsuid', $uaid);
				$this->db->update('order_menu', $udata);

				if ((empty($addonsid)) && ($customqty == 0) && ($totalvarient == 1)) {
					$reqqty = $qty - $orderchecked->menuqty;
				} else {
					$reqqty = $qty;
				}
				if ($reqqty > 0) {
					if ((empty($addonsid)) && ($customqty == 0) && ($totalvarient == 1)) {
						$data4 = array(
							'ordid'				  	=>	$orderid,
							'menuid'		        =>	$pid,
							'qty'	        	    =>	$qty - $orderchecked->menuqty,
							'addonsid'	        	=>	$aids,
							'addonsuid'     		=>  $uaid,
							'adonsqty'	        	=>	$aqty,
							'varientid'		    	=>	$sizeid,
							'insertdate'		    =>	date('Y-m-d'),
						);
						if (empty($checkcancelitem)) {
							$datacancel = array(
								'orderid'			    =>	$orderid,
								'foodid'		        =>	$pid,
								'quantity'	        	=>	$qty - $orderchecked->menuqty,
								'varientid'		    	=>	$sizeid,
							);
							$this->db->insert('tbl_cancelitem', $datacancel);
						} else {
							$nqty = $qty - $orderchecked->menuqty;
							$udatacancel = array(
								'quantity'       => $checkcancelitem->quantity + $nqty,
							);
							$this->db->where('orderid', $orderid);
							$this->db->where('foodid', $pid);
							$this->db->where('varientid', $sizeid);
							$this->db->update('tbl_cancelitem', $udatacancel);
						}
					} else {
						$data4 = array(
							'ordid'				  	=>	$orderid,
							'menuid'		        =>	$pid,
							'qty'	        	    =>	$qty,
							'addonsid'	        	=>	$aids,
							'addonsuid'     		=>  $uaid,
							'adonsqty'	        	=>	$aqty,
							'varientid'		    	=>	$sizeid,
							'insertdate'		    =>	date('Y-m-d'),
						);
						if (empty($checkcancelitem)) {
							$datacancel = array(
								'orderid'			    =>	$orderid,
								'foodid'		        =>	$pid,
								'quantity'	        	=>	$qty,
								'varientid'		    	=>	$sizeid,
							);
							$this->db->insert('tbl_cancelitem', $datacancel);
						} else {
							$udatacancel = array(
								'quantity'       => $checkcancelitem->quantity + $qty,
							);
							$this->db->where('orderid', $orderid);
							$this->db->where('foodid', $pid);
							$this->db->where('varientid', $sizeid);
							$this->db->update('tbl_cancelitem', $udatacancel);
						}
					}
					$this->db->insert('tbl_updateitems', $data4);
				}
			}
		}

		$existingitem = $this->order_model->customerorder($orderid);

		$i = 0;
		$totalamount = 0;
		$subtotal = 0;
		foreach ($existingitem as $item) {
			$adonsprice = 0;
			$discount = 0;
			$itemprice = $item->price * $item->menuqty;
			if (!empty($item->add_on_id)) {
				$addons = explode(",", $item->add_on_id);
				$addonsqty = explode(",", $item->addonsqty);
				$x = 0;
				foreach ($addons as $addonsid) {
					$adonsinfo = $this->order_model->read('*', 'add_ons', array('add_on_id' => $addonsid));
					$adonsprice = $adonsprice + $adonsinfo->price * $addonsqty[$x];
					$x++;
				}
				$nittotal = $adonsprice;
				$itemprice = $itemprice + $adonsprice;
			} else {
				$nittotal = 0;
			}
			$totalamount = $totalamount + $nittotal;
			$subtotal = $subtotal + $item->price * $item->menuqty;
		}


		$itemtotal = $totalamount + $subtotal;
		$calvat = $itemtotal * $settinginfo->vat / 100;
		$updatedprice = $calvat + $itemtotal - $discount;
		$postData = array(
			'order_id'        => $orderid,
			'totalamount'     => $updatedprice,
		);
		$this->order_model->update_order($postData);


		$data['orderinfo']  	   = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$data['iteminfo']       = $this->order_model->customerorder($orderid);
		$data['billinfo']	   = $this->order_model->billinfo($orderid);
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderlist";

		$this->load->view('ordermanage/updateorderlist', $data);
	}
	public function itemqtyupdate()
	{
		$pid = $this->input->post('itemid');
		$sizeid = $this->input->post('varientid');
		$qty = $this->input->post('existqty', true);
		$status = $this->input->post('status', true);
		$uaid = $this->input->post('auid', true);
		$isgroup = $this->input->post('isgroup', true);
		$status = preg_replace('/\s+/', '', $status);
		$orderid = $this->input->post('orderid');
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		if ($status == "add") {
			$acqty =	$qty + 1;
		}
		if ($status == "del") {
			$acqty =	$qty - 1;
		}
		if ($isgroup == 1) {
			$orderchecked = $this->order_model->check_ordergroup($orderid, $pid, $sizeid, $uaid);
			$checkcancelitem = $this->order_model->check_cancelitem($orderid, $pid, $sizeid);
			$groupinfo = $this->db->select('*')->from('tbl_groupitems')->where('gitemid', $pid)->get()->result();
			foreach ($groupinfo as $grouprow) {
				$udata2 = array(
					'qroupqty'      => $acqty,
					'menuqty'       => $grouprow->item_qty * $acqty,
				);
				$this->db->where('order_id', $orderid);
				$this->db->where('menu_id', $grouprow->items);
				$this->db->where('groupmid', $pid);
				$this->db->where('groupvarient', $sizeid);
				$this->db->where('varientid', $grouprow->varientid);
				$this->db->where('addonsuid', $uaid);
				$this->db->update('order_menu', $udata2);
			}
			if ($status == "del" && $acqty == 0) {
				$this->db->where('order_id', $orderid)->where('groupmid', $pid)->where('groupvarient', $sizeid)->where('addonsuid', $uaid)->delete('order_menu');
				if (empty($checkcancelitem)) {
					$datacancel = array(
						'orderid'			    =>	$orderid,
						'foodid'		        =>	$pid,
						'quantity'	        	=>	1,
						'varientid'		    	=>	$sizeid,
					);
					$this->db->insert('tbl_cancelitem', $datacancel);
				} else {
					$udatacancel = array(
						'quantity'       => $checkcancelitem->quantity + 1,
					);
					$this->db->where('orderid', $orderid);
					$this->db->where('foodid', $pid);
					$this->db->where('varientid', $sizeid);
					$this->db->update('tbl_cancelitem', $udatacancel);
				}
			} else {
				if ($acqty > $orderchecked->qroupqty) {
					$reqqty = $acqty - $orderchecked->qroupqty;
				} else {
					$reqqty = $orderchecked->qroupqty - $acqty;
				}

				if ($reqqty > 0) {
					if ($status == "del") {
						$data4 = array(
							'ordid'				  =>	$orderid,
							'menuid'		        =>	$pid,
							'qty'	        	    =>	1,
							'addonsid'	        	=>	$orderchecked->add_on_id,
							'addonsuid'     		=>  $uaid,
							'adonsqty'	        	=>	$orderchecked->addonsqty,
							'varientid'		    	=>	$sizeid,
							'isupdate'				=>  "-",
							'insertdate'		    =>	date('Y-m-d'),
						);
						if (empty($checkcancelitem)) {
							$datacancel = array(
								'orderid'			    =>	$orderid,
								'foodid'		        =>	$pid,
								'quantity'	        	=>	1,
								'varientid'		    	=>	$sizeid,
							);
							$this->db->insert('tbl_cancelitem', $datacancel);
						} else {
							$udatacancel = array(
								'quantity'       => $checkcancelitem->quantity + 1,
							);
							$this->db->where('orderid', $orderid);
							$this->db->where('foodid', $pid);
							$this->db->where('varientid', $sizeid);
							$this->db->update('tbl_cancelitem', $udatacancel);
						}
					} else {
						$data4 = array(
							'ordid'				  =>	$orderid,
							'menuid'		        =>	$pid,
							'qty'	        	    =>	$acqty - $orderchecked->menuqty,
							'addonsid'	        	=>	$orderchecked->add_on_id,
							'addonsuid'     		=>  $uaid,
							'adonsqty'	        	=>	$orderchecked->addonsqty,
							'varientid'		    	=>	$sizeid,
							'insertdate'		    =>	date('Y-m-d'),
						);
					}

					$this->db->insert('tbl_updateitems', $data4);
				}
				$existingitem = $this->order_model->customerorder($orderid);

				$i = 0;
				$totalamount = 0;
				$subtotal = 0;
				foreach ($existingitem as $item) {
					$adonsprice = 0;
					$discount = 0;
					$itemprice = $item->price * $item->menuqty;
					if (!empty($item->add_on_id)) {
						$addons = explode(",", $item->add_on_id);
						$addonsqty = explode(",", $item->addonsqty);
						$x = 0;
						foreach ($addons as $addonsid) {
							$adonsinfo = $this->order_model->read('*', 'add_ons', array('add_on_id' => $addonsid));
							$adonsprice = $adonsprice + $adonsinfo->price * $addonsqty[$x];
							$x++;
						}
						$nittotal = $adonsprice;
						$itemprice = $itemprice + $adonsprice;
					} else {
						$nittotal = 0;
					}
					$totalamount = $totalamount + $nittotal;
					$subtotal = $subtotal + $item->price * $item->menuqty;
				}


				$itemtotal = $totalamount + $subtotal;
				$calvat = $itemtotal * $settinginfo->vat / 100;
				$updatedprice = $calvat + $itemtotal - $discount;
				$postData = array(
					'order_id'        => $orderid,
					'totalamount'     => $updatedprice,
				);
				$this->order_model->update_order($postData);
			}
		} else {
			$orderchecked = $this->order_model->check_order($orderid, $pid, $sizeid, $uaid);
			$checkcancelitem = $this->order_model->check_cancelitem($orderid, $pid, $sizeid);
			$udata = array(
				'menuqty'       => $acqty
			);

			$this->db->where('order_id', $orderid);
			$this->db->where('menu_id', $pid);
			$this->db->where('varientid', $sizeid);
			$this->db->where('addonsuid', $uaid);
			$this->db->update('order_menu', $udata);

			if ($status == "del" && $acqty == 0) {
				$this->db->where('order_id', $orderid)->where('menu_id', $pid)->where('varientid', $sizeid)->where('addonsuid', $uaid)->delete('order_menu');
				if (empty($checkcancelitem)) {
					$datacancel = array(
						'orderid'			    =>	$orderid,
						'foodid'		        =>	$pid,
						'quantity'	        	=>	1,
						'varientid'		    	=>	$sizeid,
					);
					$this->db->insert('tbl_cancelitem', $datacancel);
				} else {
					$udatacancel = array(
						'quantity'       => $checkcancelitem->quantity + 1,
					);
					$this->db->where('orderid', $orderid);
					$this->db->where('foodid', $pid);
					$this->db->where('varientid', $sizeid);
					$this->db->update('tbl_cancelitem', $udatacancel);
				}
			} else {
				if ($acqty > $orderchecked->menuqty) {
					$reqqty = $acqty - $orderchecked->menuqty;
				} else {
					$reqqty = $orderchecked->menuqty - $acqty;
				}

				if ($reqqty > 0) {
					if ($status == "del") {
						$data4 = array(
							'ordid'				  =>	$orderid,
							'menuid'		        =>	$pid,
							'qty'	        	    =>	1,
							'addonsid'	        	=>	$orderchecked->add_on_id,
							'addonsuid'     		=>  $uaid,
							'adonsqty'	        	=>	$orderchecked->addonsqty,
							'varientid'		    	=>	$sizeid,
							'isupdate'				=>  "-",
							'insertdate'		    =>	date('Y-m-d'),
						);
						if (empty($checkcancelitem)) {
							$datacancel = array(
								'orderid'			    =>	$orderid,
								'foodid'		        =>	$pid,
								'quantity'	        	=>	1,
								'varientid'		    	=>	$sizeid,
							);
							$this->db->insert('tbl_cancelitem', $datacancel);
						} else {
							$udatacancel = array(
								'quantity'       => $checkcancelitem->quantity + 1,
							);
							$this->db->where('orderid', $orderid);
							$this->db->where('foodid', $pid);
							$this->db->where('varientid', $sizeid);
							$this->db->update('tbl_cancelitem', $udatacancel);
						}
					} else {
						$data4 = array(
							'ordid'				  =>	$orderid,
							'menuid'		        =>	$pid,
							'qty'	        	    =>	$acqty - $orderchecked->menuqty,
							'addonsid'	        	=>	$orderchecked->add_on_id,
							'addonsuid'     		=>  $uaid,
							'adonsqty'	        	=>	$orderchecked->addonsqty,
							'varientid'		    	=>	$sizeid,
							'insertdate'		    =>	date('Y-m-d'),
						);
					}

					$this->db->insert('tbl_updateitems', $data4);
				}
				$existingitem = $this->order_model->customerorder($orderid);

				$i = 0;
				$totalamount = 0;
				$subtotal = 0;
				foreach ($existingitem as $item) {
					$adonsprice = 0;
					$discount = 0;
					$itemprice = $item->price * $item->menuqty;
					if (!empty($item->add_on_id)) {
						$addons = explode(",", $item->add_on_id);
						$addonsqty = explode(",", $item->addonsqty);
						$x = 0;
						foreach ($addons as $addonsid) {
							$adonsinfo = $this->order_model->read('*', 'add_ons', array('add_on_id' => $addonsid));
							$adonsprice = $adonsprice + $adonsinfo->price * $addonsqty[$x];
							$x++;
						}
						$nittotal = $adonsprice;
						$itemprice = $itemprice + $adonsprice;
					} else {
						$nittotal = 0;
					}
					$totalamount = $totalamount + $nittotal;
					$subtotal = $subtotal + $item->price * $item->menuqty;
				}


				$itemtotal = $totalamount + $subtotal;
				$calvat = $itemtotal * $settinginfo->vat / 100;
				$updatedprice = $calvat + $itemtotal - $discount;
				$postData = array(
					'order_id'        => $orderid,
					'totalamount'     => $updatedprice,
				);
				$this->order_model->update_order($postData);
			}
		}

		$data['orderinfo']  	   = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$data['iteminfo']       = $this->order_model->customerorder($orderid);
		$data['billinfo']	   = $this->order_model->billinfo($orderid);
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderlist";

		$this->load->view('ordermanage/updateorderlist', $data);
	}
	/*update uniqe*/
	public function addtocartupdate_uniqe($pid, $oid)
	{
		$getproduct = $this->order_model->getuniqeproduct($pid);
		$this->db->select('*');
		$this->db->from('menu_add_on');
		$this->db->where('menu_id', $pid);
		$query = $this->db->get();

		$getadons = "";
		if ($query->num_rows() > 0 || $getproduct->is_customqty == 1) {
			$getadons = 1;
		} else {
			$getadons =  0;
		}

		$catid = $getproduct->CategoryID;
		$sizeid = $getproduct->variantid;
		$itemname = $getproduct->ProductName . '-' . $getproduct->itemnotes;
		$size = $getproduct->variantName;
		$qty = 1;
		$price = isset($getproduct->price) ? $getproduct->price : 0;
		$orderid = $oid;
		if ($price == 0) {
			$sizeid = 0;
		}
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;

		if ($getadons == 1) {
			echo 'adons';
			exit;
		} else {
			$grandtotal = $price;
			$aids = '';
			$aqty = '';
			$aname = '';
			$aprice = '';
			$atprice = '0';
			echo 'adons';
			exit;
		}
		$uaid =	$pid . $sizeid;
		$orderchecked = $this->order_model->check_order($orderid, $pid, $sizeid, $uaid);

		if (empty($orderchecked)) {
			$postInfo = array(
				'order_id'      => $orderid,
				'menu_id'       => $pid,
				'menuqty'       => $qty,
				'add_on_id'     => $aids,
				'addonsuid'	   => $uaid,
				'addonsqty'     => $aqty,
				'varientid'     => $sizeid,
				'isupdate'      => 1,
			);
			$this->order_model->new_entry($postInfo);
		} else {
			$qty = $orderchecked->menuqty + 1;

			$udata = array(
				'menuqty'       => $qty,
				'add_on_id'     => $aids,
				'addonsqty'     => $aqty,
			);

			$this->db->where('order_id', $orderid);
			$this->db->where('menu_id', $pid);
			$this->db->where('varientid', $sizeid);
			$this->db->update('order_menu', $udata);
			$reqqty = $qty - $orderchecked->menuqty;
			if ($reqqty > 0) {
				$data4 = array(
					'ordid'				  =>	$orderid,
					'menuid'		        =>	$pid,
					'qty'	        	    =>	$qty - $orderchecked->menuqty,
					'addonsid'	        	=>	$aids,
					'adonsqty'	        	=>	$aqty,
					'varientid'		    	=>	$sizeid,
					'insertdate'		    =>	date('Y-m-d'),
				);
				$this->db->insert('tbl_updateitems', $data4);
			}
		}
		$existingitem = $this->order_model->customerorder($orderid);

		$i = 0;
		$totalamount = 0;
		$subtotal = 0;
		foreach ($existingitem as $item) {
			$adonsprice = 0;
			$discount = 0;
			$itemprice = $item->price * $item->menuqty;
			if (!empty($item->add_on_id)) {
				$addons = explode(",", $item->add_on_id);
				$addonsqty = explode(",", $item->addonsqty);
				$x = 0;
				foreach ($addons as $addonsid) {
					$adonsinfo = $this->order_model->read('*', 'add_ons', array('add_on_id' => $addonsid));
					$adonsprice = $adonsprice + $adonsinfo->price * $addonsqty[$x];
					$x++;
				}
				$nittotal = $adonsprice;
				$itemprice = $itemprice + $adonsprice;
			} else {
				$nittotal = 0;
			}
			$totalamount = $totalamount + $nittotal;
			$subtotal = $subtotal + $item->price * $item->menuqty;
		}


		$itemtotal = $totalamount + $subtotal;
		$calvat = $itemtotal * $settinginfo->vat / 100;
		$updatedprice = $calvat + $itemtotal - $discount;
		$postData = array(
			'order_id'        => $orderid,
			'totalamount'     => $updatedprice,
		);
		$this->order_model->update_order($postData);


		$data['orderinfo']  	   = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$data['iteminfo']       = $this->order_model->customerorder($orderid);
		$data['billinfo']	   = $this->order_model->billinfo($orderid);
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderlist";

		$this->load->view('ordermanage/updateorderlist', $data);
	}

	public function orderinvoice($id)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "invoice";

		echo Modules::run('template/layout', $data);
	}
	/*order invoice for post*/
	public function pos_order_invoice($id)
	{
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$this->load->view('ordermanage/invoice_pos', $data);
	}

	public function orderdetails($id)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);

		$data['module'] = "ordermanage";
		$data['page']   = "details";
		echo Modules::run('template/layout', $data);
	}
	public function orderdetailspop($id)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);
		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$data['shipinfo']	   = $this->order_model->shipinfo($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "details";
		$this->load->view('ordermanage/details', $data);
	}
	/*details page for pos*/
	public function orderdetails_post($id)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$this->load->view('ordermanage/details', $data);
	}
	public function posorderinvoice($id)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));

		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$data['cashierinfo']   = $this->order_model->read('*', 'user', array('id' => $data['billinfo']->create_by));
		$data['tableinfo'] = $this->order_model->read('*', 'rest_table', array('tableid' => $customerorder->table_no));
		$settinginfo = $this->order_model->settinginfo();
		if ($settinginfo->printtype == 1 || $settinginfo->printtype == 3) {
			$updatetData = array('invoiceprint' => 2);
			$this->db->where('order_id', $id);
			$this->db->update('customer_order', $updatetData);
		}
		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "posinvoice";
		$view = $this->load->view('posinvoice', $data, true);
		echo $view;
		exit;
	}
	public function posprintdirect($id)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$updatetData = array('nofification' => 1);
		$this->db->where('order_id', $id);
		$this->db->update('customer_order', $updatetData);

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$data['cashierinfo']   = $this->order_model->read('*', 'user', array('id' => $data['billinfo']->create_by));
		$settinginfo = $this->order_model->settinginfo();
		$data['tableinfo'] = $this->order_model->read('*', 'rest_table', array('tableid' => $customerorder->table_no));
		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "posinvoice";

		$view = $this->load->view('posinvoicedirectprint', $data, true);
		echo $view;
		exit;
	}
	public function dueinvoice($id)
	{

		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		$data['iteminfo']       = $this->order_model->customerorder($id);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$data['cashierinfo']   = $this->order_model->read('*', 'user', array('id' => $data['billinfo']->create_by));
		$data['tableinfo'] = $this->order_model->read('*', 'rest_table', array('tableid' => $customerorder->table_no));
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "posinvoice";
		$view = $this->load->view('dueinvoicedirectprint', $data, true);
		echo $view;
		exit;
	}
	public function fwrite_stream($fp, $string)
	{
		for ($written = 0; $written < strlen($string); $written += $fwrite) {
			$fwrite = fwrite($fp, substr($string, $written));
			if ($fwrite === false) {
				return $written;
			}
		}
		return $written;
	}
	public function postokengenerate($id, $ordstatus)
	{
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));

		if (!empty($customerorder->waiter_id)) {
			$data['waiterinfo']      = $this->order_model->read('first_name,last_name', 'employee_history', array('emp_his_id' => $customerorder->waiter_id));
		} else {
			$data['waiterinfo'] = '';
		}
		if (!empty($customerorder->table_no)) {
			$data['tableinfo']      = $this->order_model->read('*', 'rest_table', array('tableid' => $customerorder->table_no));
		} else {
			$data['tableinfo'] = '';
		}
		$data['iteminfo']       = $this->order_model->customerorder($id, $ordstatus);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);

		$data['module'] = "ordermanage";
		$data['page']   = "posinvoice";


		echo $view = $this->load->view('postoken', $data, true);
		//return $view;


	}
	public function paidtoken($id)
	{
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		if (!empty($customerorder->table_no)) {
			$data['tableinfo']      = $this->order_model->read('*', 'rest_table', array('tableid' => $customerorder->table_no));
		} else {
			$data['tableinfo'] = '';
		}
		if (!empty($customerorder->waiter_id)) {
			$data['waiterinfo']      = $this->order_model->read('first_name,last_name', 'employee_history', array('emp_his_id' => $customerorder->waiter_id));
		} else {
			$data['waiterinfo'] = '';
		}
		$data['iteminfo']       = $this->order_model->customerorder($id, $ordstatus = null);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);

		$data['module'] = "ordermanage";
		$data['page']   = "posinvoice";


		echo $view = $this->load->view('postoken', $data, true);
		//return $view;


	}
	public function postokengenerateupdate($id, $ordstatus)
	{
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));

		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		if (!empty($customerorder->table_no)) {
			$data['tableinfo']      = $this->order_model->read('*', 'rest_table', array('tableid' => $customerorder->table_no));
		} else {
			$data['tableinfo'] = '';
		}
		if (!empty($customerorder->waiter_id)) {
			$data['waiterinfo']      = $this->order_model->read('first_name,last_name', 'employee_history', array('emp_his_id' => $customerorder->waiter_id));
		} else {
			$data['waiterinfo'] = '';
		}
		$data['exitsitem']      = $this->order_model->customerorder($id);
		$data['iteminfo']       = $this->order_model->customerorder($id, $ordstatus);

		$data['billinfo']	   = $this->order_model->billinfo($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);

		$data['module'] = "ordermanage";
		$data['page']   = "posinvoice";

		$view = $this->load->view('postokenupdate', $data);
		echo $view;
		$this->db->where('ordid', $id)->delete('tbl_updateitems');
		$updatetData = array(
			'isupdate' => NULL,
		);
		$this->db->where('order_id', $id);
		$this->db->update('order_menu', $updatetData);
	}
	public function tokenupdate($id)
	{
		$this->db->where('ordid', $id)->delete('tbl_updateitems');
		$updatetData = array(
			'isupdate' => NULL,
		);
		$this->db->where('order_id', $id);
		$this->db->update('order_menu', $updatetData);
	}
	public function postokengeneratesame($id, $ordstatus)
	{
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');
		$customerorder = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		$data['orderinfo']  	   = $customerorder;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $customerorder->customer_id));
		if (!empty($customerorder->table_no)) {
			$data['tableinfo']      = $this->order_model->read('*', 'rest_table', array('tableid' => $customerorder->table_no));
		} else {
			$data['tableinfo'] = '';
		}
		if (!empty($customerorder->waiter_id)) {
			$data['waiterinfo']      = $this->order_model->read('first_name,last_name', 'employee_history', array('emp_his_id' => $customerorder->waiter_id));
		} else {
			$data['waiterinfo'] = '';
		}
		$data['iteminfo']       = $this->order_model->customerorder($id, $ordstatus);
		$data['billinfo']	   = $this->order_model->billinfo($id);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);

		$data['module'] = "ordermanage";
		$data['page']   = "posinvoice";
		$this->load->view('postoken2', $data);
	}
	public function paymentgateway($orderid, $paymentid)
	{
		$data['orderinfo']  	       = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$data['paymentinfo']  	   = $this->order_model->read('*', 'paymentsetup', array('paymentid' => $paymentid));
		$paymentinfo = $this->order_model->read('*', 'paymentsetup', array('paymentid' => $paymentid));
		$data['customerinfo']  	   = $this->order_model->read('*', 'customer_info', array('customer_id' => $data['orderinfo']->customer_id));
		$customer = $this->order_model->read('*', 'customer_info', array('customer_id' => $data['orderinfo']->customer_id));
		$bill  	   = $this->order_model->read('*', 'bill', array('order_id' => $orderid));
		$data['billinfo']  	   = $this->order_model->read('*', 'bill_card_payment', array('bill_id' => $bill->bill_id));

		$data['iteminfo']       = $this->order_model->customerorder($orderid);
		$data['mybill']	   = $this->order_model->billinfo($orderid);
		$settinginfo = $this->order_model->settinginfo();
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);


		$data['module'] = "ordermanage";

		if ($paymentid == 5) {


			$full_name = $customer->customer_name;
			$email = $customer->customer_email;
			$phone = $customer->customer_phone;
			$amount =  $bill->bill_amount;
			$transactionid = $orderid;
			$address = $customer->customer_address;

			$post_data = array();
			$post_data['store_id'] = SSLCZ_STORE_ID;
			$post_data['store_passwd'] = SSLCZ_STORE_PASSWD;
			$post_data['total_amount'] =  $bill->bill_amount;
			$post_data['currency'] = $paymentinfo->currency;
			$post_data['tran_id'] = $orderid;
			$post_data['success_url'] =  base_url() . "ordermanage/order/successful/" . $orderid;
			$post_data['fail_url'] = base_url() . "ordermanage/order/fail/" . $orderid;
			$post_data['cancel_url'] = base_url() . "ordermanage/order/cancilorder/" . $orderid;


			# CUSTOMER INFORMATION
			$post_data['cus_name'] = $customer->customer_name;
			$post_data['cus_email'] = $customer->customer_email;
			$post_data['cus_add1'] = $customer->customer_address;
			$post_data['cus_add2'] = "";
			$post_data['cus_city'] = "";
			$post_data['cus_state'] = "";
			$post_data['cus_postcode'] = "";
			$post_data['cus_country'] = "";
			$post_data['cus_phone'] = $customer->customer_phone;
			$post_data['cus_fax'] = "";

			# SHIPMENT INFORMATION
			$post_data['ship_name'] = "";
			$post_data['ship_add1 '] = "";
			$post_data['ship_add2'] = "";
			$post_data['ship_city'] = "";
			$post_data['ship_state'] = "";
			$post_data['ship_postcode'] = "";
			$post_data['ship_country'] = "";

			# OPTIONAL PARAMETERS
			$post_data['value_a'] = "";
			$post_data['value_b '] = "";
			$post_data['value_c'] = "";
			$post_data['value_d'] = "";

			$this->load->library('session');
			$session = array(
				'tran_id' => $post_data['tran_id'],
				'amount' => $post_data['total_amount'],
				'currency' => $post_data['currency']
			);
			$this->session->set_userdata('tarndata', $session);
			$this->load->library('sslcommerz');
			echo "<h3>Wait...SSLCOMMERZ Payment Processing....</h3>";

			if ($this->sslcommerz->RequestToSSLC($post_data, false)) {

				redirect('ordermanage/order/fail/' . $orderid);
			}
			$data['page']   = "checkout";
		} else if ($paymentid == 3) {
			$data['page']   = "paypal";
			$this->load->view('ordermanage/paypal', $data);
		} else if ($paymentid == 2) {
			$data['page']   = "2checkout";
			echo Modules::run('template/layout', $data);
		}
	}
	public function successful($orderid)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$billinfo = $this->order_model->read('*', 'bill', array('order_id' => $orderid));
		$orderinfo  	       = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$customerid 	   = $orderinfo->customer_id;
		$updatetData = array('bill_status'     => 1);
		$this->db->where('order_id', $orderid);
		$this->db->update('bill', $updatetData);

		$updatetDataord = array('order_status' => 4);
		$this->db->where('order_id', $orderid);
		$this->db->update('customer_order', $updatetDataord);
		$this->session->set_flashdata('message', display('order_successfully'));

		redirect('ordermanage/order/pos_invoice/' . $orderid);
	}
	public function successful2()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$orderid = $this->input->post('li_0_name', true);

		$billinfo = $this->order_model->read('*', 'bill', array('order_id' => $orderid));
		$orderinfo  	       = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$customerid 	   = $orderinfo->customer_id;
		$updatetData = array('bill_status'     => 1);
		$this->db->where('order_id', $orderid);
		$this->db->update('bill', $updatetData);

		$updatetDataord = array('order_status'     => 4);
		$this->db->where('order_id', $orderid);
		$this->db->update('customer_order', $updatetDataord);
		$this->session->set_flashdata('message', display('order_successfully'));

		if (empty($this->session->userdata('id'))) {
			redirect('hungry/orderdelevered/001');
		} else {
			redirect('ordermanage/order/pos_invoice/' . $orderid);
		}
	}
	public function fail($orderid)
	{
		$this->session->set_flashdata('message', display('order_fail'));
		redirect('ordermanage/order/pos_invoice');
	}
	public function cancilorder($orderid)
	{
		$this->session->set_flashdata('message', display('order_fail'));
		redirect('ordermanage/order/pos_invoice');
	}
	public function allkitchen()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		if ($this->permission->method('ordermanage', 'read')->access() == FALSE) {
			redirect('dashboard/auth/logout');
		}
		$uid = $this->session->userdata('id');
		$assignketchen = $this->db->select('user.id,tbl_assign_kitchen.kitchen_id,tbl_assign_kitchen.userid,tbl_kitchen.kitchen_name')->from('tbl_assign_kitchen')->join('user', 'user.id=tbl_assign_kitchen.userid', 'left')->join('tbl_kitchen', 'tbl_kitchen.kitchenid=tbl_assign_kitchen.kitchen_id')->where('tbl_assign_kitchen.userid', $uid)->get()->result();
		if (!empty($assignketchen)) {
			$data['kitchenlist'] = $assignketchen;
			foreach ($assignketchen as $kitchen) {
				$data['kitcheninfo'][$i]['kitchenid'] = $kitchen->kitchen_id;
				$orderinfo = $this->order_model->kitchen_ongoingorder($kitchen->kitchen_id);

				if (!empty($orderinfo)) {
					$m = 0;
					foreach ($orderinfo as $orderlist) {
						$billtotal = round($orderlist->totalamount);
						if (($onprocess->orderacceptreject == 0 || empty($orderlist->orderacceptreject)) && ($orderlist->cutomertype == 2)) {
						} else {
							$data['kitcheninfo'][$i]['orderlist'][$m] = $orderlist;
							$data['kitcheninfo'][$i]['iteminfo'][$m] = $this->order_model->customerorderkitchen($orderlist->order_id, $kitchen->kitchen_id);
							$m++;
						}
					}
				}
				$i++;
			}
		} else {
			$kitchenlist = $this->db->select('kitchenid as kitchen_id,kitchen_name')->from('tbl_kitchen')->order_by('kitchen_name', 'Asc')->get()->result();
			$output = array();
			$i = 0;
			foreach ($kitchenlist as $kitchen) {
				$data['kitcheninfo'][$i]['kitchenid'] = $kitchen->kitchen_id;
				$orderinfo = $this->order_model->kitchen_ongoingorder($kitchen->kitchen_id);

				if (!empty($orderinfo)) {
					$m = 0;
					foreach ($orderinfo as $orderlist) {
						$billtotal = round($orderlist->totalamount);
						if (($orderlist->orderacceptreject == 0 || empty($orderlist->orderacceptreject)) && ($orderlist->cutomertype == 2)) {
						} else {
							$data['kitcheninfo'][$i]['orderlist'][$m] = $orderlist;
							$data['kitcheninfo'][$i]['iteminfo'][$m] = $this->order_model->customerorderkitchen($orderlist->order_id, $kitchen->kitchen_id);
							$m++;
						}
					}
				}
				$i++;
			}
			$data['kitchenlist'] = $kitchenlist;
		}
		$data['title'] = "Counter Dashboard";
		$data['module'] = "ordermanage";
		$data['page']   = "allkitchen";
		echo Modules::run('template/layout', $data);
	}
	public function kitchen($kitchenid = null)
	{
		if ($this->permission->method('ordermanage', 'read')->access() == FALSE) {
			redirect('dashboard/auth/logout');
		}

		$data['title'] = "Kitchen Dashboard";
		$data['ongoingorder']  = $this->order_model->kitchen_ongoingorder($kitchenid);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['kitchenid'] = $kitchenid;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);

		$data['module'] = "ordermanage";
		$data['page']   = "kitchen";
		echo Modules::run('template/layout', $data);
	}
	public function checkorder()
	{
		if ($this->permission->method('ordermanage', 'read')->access() == FALSE) {
			redirect('dashboard/auth/logout');
		}
		$orderid = $this->input->post('orderid');
		$kid = $this->input->post('kid');
		$data['title'] = "Kitchen Dashboard";
		$data['kitchenid'] = $kid;
		$data['orderinfo'] = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$data['itemlist'] = $this->order_model->customerorderkitchen($orderid, $kid);
		$data['module'] = "ordermanage";
		$data['page']   = "kitchen_view";
		$this->load->view('ordermanage/kitchen_view', $data);
	}
	public function itemacepted()
	{
		if ($this->permission->method('ordermanage', 'read')->access() == FALSE) {
			redirect('dashboard/auth/logout');
		}
		$orderid = $this->input->post('orderid');
		$kitid = $this->input->post('kitid');
		$itemid = $this->input->post('itemid');
		$varient = $this->input->post('varient', true);

		$itemids = explode(',', $itemid);
		$varientids = explode(',', $varient);
		$itemidsv = array_values(trim($itemids, ','));
		$varientidsv = array_values(trim($varientids, ','));
		$i = 0;
		foreach ($itemids as $sitem) {
			$vaids = $varientids[$i];
			$isexit = $this->db->select('tbl_kitchen_order.*')->from('tbl_kitchen_order')->where('orderid', $orderid)->where('kitchenid', $kitid)->where('itemid', $sitem)->where('varient', $vaids)->get()->num_rows();
			if ($isexit > 0) {
			} else {
				$kitchenorder = array(
					'kitchenid' => $kitid,
					'orderid'     => $orderid,
					'itemid'     => $sitem,
					'varient'     => $vaids
				);
				$this->db->insert('tbl_kitchen_order', $kitchenorder);
				$itemaccepted = array(
					'accepttime' => date('Y-m-d H:i:s'),
					'orderid'     => $orderid,
					'menuid'     => $sitem,
					'varient'     => $vaids
				);
				$this->db->insert('tbl_itemaccepted', $itemaccepted);
			}
			$i++;
		}
		$alliteminfo = $this->order_model->customerorderkitchen($orderid, $kitid);
		$allchecked = "";
		foreach ($alliteminfo as $single) {
			$allisexit = $this->db->select('tbl_kitchen_order.*')->from('tbl_kitchen_order')->where('orderid', $orderid)->where('kitchenid', $kitid)->where('itemid', $single->menu_id)->where('varient', $single->variantid)->get()->num_rows();

			if ($allisexit > 0) {
				$allchecked .= "1,";
			} else {
				$allchecked .= "0,";
			}
		}
		if (strpos($allchecked, '0') !== false) {
			echo 0;
		} else {
			echo 1;
		}
		$totalnumkitord = $this->db->select('tbl_kitchen_order.*')->from('tbl_kitchen_order')->where('orderid', $orderid)->where('itemid>0')->get()->num_rows();
		$totalmenuord = $this->db->select('order_menu.*')->from('order_menu')->where('order_id', $orderid)->get()->num_rows();
		if ($totalmenuord == $totalnumkitord) {
			$updatetData2 = array('order_status'  => 2);
			$this->db->where('order_id', $orderid);
			$this->db->update('customer_order', $updatetData2);
		}
	}
	public function itemisready()
	{
		if ($this->permission->method('ordermanage', 'read')->access() == FALSE) {
			redirect('dashboard/auth/logout');
		}
		$orderid = $this->input->post('orderid');
		$menuid = $this->input->post('menuid');
		$varient = $this->input->post('varient', true);
		$status = $this->input->post('status', true);
		$updatetData = array('food_status'     => $status);
		$this->db->where('order_id', $orderid);
		$this->db->where('menu_id', $menuid);
		$this->db->where('varientid', $varient);
		$this->db->update('order_menu', $updatetData);

		$updatetData2 = array('order_status'  => 2);
		$this->db->where('order_id', $orderid);
		$this->db->update('customer_order', $updatetData2);
		$orderinformation = $this->order_model->read('*', 'customer_order', array('order_id' => $orderid));
		$allemployee = $this->db->select('*')->from('user')->where('id', $orderinformation->waiter_id)->get()->row();
		$item = $this->order_model->read('*', 'item_foods', array('ProductsID' => $menuid));
		$isexit = $this->db->select('*')->from('tbl_orderprepare')->where('orderid', $orderid)->where('menuid', $menuid)->where('varient', $varient)->get()->row();
		if ($status == 1) {
			$ready = "Food Is Ready";
			if (empty($isexit)) {
				$ready = array(
					'preparetime' => date('Y-m-d H:i:s'),
					'orderid'     => $orderid,
					'menuid'     => $menuid,
					'varient'     => $varient
				);
				$this->db->insert('tbl_orderprepare', $ready);
			}
			//push 
			$senderid[] = $allemployee->waiter_kitchenToken;
			define('API_ACCESS_KEY', 'AAAAvWuiU2I:APA91bGGr8XSrxX1A_XkpbFkKu8KjT-UU0wgCjar1mHKVkT575rgq5cVUcqj2-2p-eEzHV-GtEH04d75yAccgoyZ3DM5YZPfp6OxYSMs-c_9nTVQLNOMksM9rWRv5zmBUpDqnPgLFj-E');
			$registrationIds = $senderid;
			$msg = array(
				'message' 					=> "Orderid: " . $orderid . ", Item Name: " . $item->ProductName . " Amount:" . $orderinformation->totalamount,
				'title'						=> "Food Is Ready.",
				'subtitle'					=> $orderid,
				'tickerText'				=> "TSET",
				'vibrate'					=> 1,
				'sound'						=> 1,
				'largeIcon'					=> "TSET",
				'smallIcon'					=> "TSET"
			);
			$fields2 = array(
				'registration_ids' 	=> $registrationIds,
				'data'			=> $msg
			);

			$headers2 = array(
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
			);

			$ch2 = curl_init();
			curl_setopt($ch2, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($ch2, CURLOPT_POST, true);
			curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
			curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($fields2));
			$result2 = curl_exec($ch2);
			curl_close($ch2);
		} else {
			$ready = "Food Is Cooking";
			$this->db->where('orderid', $orderid)->where('menuid', $menuid)->where('varient', $varient)->delete('tbl_orderprepare');
			//push 
			$senderid[] = $allemployee->waiter_kitchenToken;
			define('API_ACCESS_KEY', 'AAAAvWuiU2I:APA91bGGr8XSrxX1A_XkpbFkKu8KjT-UU0wgCjar1mHKVkT575rgq5cVUcqj2-2p-eEzHV-GtEH04d75yAccgoyZ3DM5YZPfp6OxYSMs-c_9nTVQLNOMksM9rWRv5zmBUpDqnPgLFj-E');
			$registrationIds = $senderid;
			$msg = array(
				'message' 					=> "Orderid: " . $orderid . ", Item Name: " . $item->ProductName . " Amount:" . $orderinformation->totalamount,
				'title'						=> "Processing",
				'subtitle'					=> $orderid,
				'tickerText'				=> "TSET",
				'vibrate'					=> 1,
				'sound'						=> 1,
				'largeIcon'					=> "TSET",
				'smallIcon'					=> "TSET"
			);
			$fields2 = array(
				'registration_ids' 	=> $registrationIds,
				'data'			=> $msg
			);

			$headers2 = array(
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
			);

			$ch2 = curl_init();
			curl_setopt($ch2, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($ch2, CURLOPT_POST, true);
			curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
			curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($fields2));
			$result2 = curl_exec($ch2);
			curl_close($ch2);
			/*End Notification*/
		}
		echo $status;
	}
	public function orderisready()
	{
		if ($this->permission->method('ordermanage', 'read')->access() == FALSE) {
			redirect('dashboard/auth/logout');
		}
		$orderid = $this->input->post('orderid');
		$allfood = $this->input->post('itemid');
		$kid = $this->input->post('kid', true);
		$allfood_id = explode(",", $allfood);
		foreach ($allfood_id as $foodid) {
			$updatetready = array(
				'allfoodready'           => 1
			);
			$this->db->where('order_id', $orderid);
			$this->db->where('menu_id', $foodid);
			$this->db->update('order_menu', $updatetready);
		}
		$data['ongoingorder']  = $this->order_model->kitchen_ongoingorder($kid);
		$data['page']   = "kitchen_load";
		$this->load->view('ordermanage/kitchen_load', $data);
	}
	public function markasdone()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$orderid = $this->input->post('orderid');
		$itemid = $this->input->post('item', true);
		$varient = $this->input->post('varient', true);
		$kid = $this->input->post('kid', true);
		$itemids = explode(',', $itemid);
		$varientids = explode(',', $varient);
		$i = 0;
		foreach ($itemids as $sitem) {
			$vaids = $varientids[$i];
			$updatetready = array(
				'food_status'           => 1,
				'allfoodready'           => 1
			);
			$this->db->where('order_id', $orderid);
			$this->db->where('menu_id', $sitem);
			$this->db->where('varientid', $vaids);
			$this->db->update('order_menu', $updatetready);
			$isexit = $this->db->select('*')->from('tbl_orderprepare')->where('orderid', $orderid)->where('menuid', $sitem)->where('varient', $vaids)->get()->row();
			if (empty($isexit)) {
				$ready = array(
					'preparetime' => date('Y-m-d H:i:s'),
					'orderid'     => $orderid,
					'menuid'     => $menuid,
					'varient'     => $varient
				);
				$this->db->insert('tbl_orderprepare', $ready);
			}
			$i++;
		}
		$updatetData = array('order_status'     => 3);
		$this->db->where('order_id', $orderid);
		$this->db->update('customer_order', $updatetData);
		$alliteminfo = $this->order_model->customerorderkitchen($orderid, $kid);
		$singleorderinfo = $this->order_model->kitchen_ajaxorderinfoall($orderid);

		$data['orderinfo'] = $singleorderinfo;
		$data['kitchenid'] = $kid;
		$data['iteminfo'] = $alliteminfo;
		$data['module'] = "ordermanage";
		$data['page']   = "kitchen_view";
		$this->load->view('kitchen_view', $data);
	}
	public function counterboard()
	{
		if ($this->permission->method('ordermanage', 'read')->access() == FALSE) {
			redirect('dashboard/auth/logout');
		}
		$data['title'] = "Counter Dashboard";
		$data['counterorder']  = $this->order_model->counter_ongoingorder();
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['module'] = "ordermanage";
		$data['page']   = "counter";
		echo Modules::run('template/layout', $data);
	}

	/*22-09*/
	public function showpaymentmodal($id, $type = null)
	{

		$array_id  = array('order_id' => $id);
		$order_info = $this->order_model->read('*', 'customer_order', $array_id);
		$customer_info = $this->order_model->read('*', 'customer_info', array('customer_id' => $order_info->customer_id));
		$data['membership'] = $customer_info->membership_type;
		$data['customerid'] = $customer_info->customer_id;
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['order_info'] = $order_info;
		$data['ismerge'] = 0;
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$data['banklist']      = $this->order_model->bank_dropdown();
		$data['terminalist']   = $this->order_model->allterminal_dropdown();
		if ($type == null) {
			$this->load->view('ordermanage/paymodal', $data);
		} else {
			$this->load->view('ordermanage/newpaymentveiw', $data);
		}
	}

	public function mergemodal()
	{
		$orderids = $this->input->post('orderid');
		$allorder = trim($orderids, ',');
		$data['order_info'] = $this->order_model->selectmerge($allorder);
		$customer_info = $this->order_model->read('*', 'customer_info', array('customer_id' => $data['order_info'][0]->customer_id));
		$data['membership'] = $customer_info->membership_type;
		$data['customerid'] = $customer_info->customer_id;
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		//print_r($data['order_info']);
		$data['ismerge'] = 1;
		$data['duemerge'] = 0;
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$data['banklist']      = $this->order_model->bank_dropdown();
		$data['terminalist']   = $this->order_model->allterminal_dropdown();
		$this->load->view('ordermanage/paymodal', $data);
	}
	public function duemergemodal()
	{
		$orderid = $this->input->post('orderid');
		$allorder = $this->input->post('allorderid');
		$mergeid = $this->input->post('mergeid');
		$data['order_info'] = $this->order_model->selectmerge($allorder);
		$customer_info = $this->order_model->read('*', 'customer_info', array('customer_id' => $orderid->customer_id));
		$data['membership'] = $customer_info->membership_type;
		$data['customerid'] = $customer_info->customer_id;
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);

		$data['ismerge'] = 1;
		$data['duemerge'] = 1;
		$data['paymentmethod'] = $this->order_model->pmethod_dropdown();
		$data['banklist']      = $this->order_model->bank_dropdown();
		$data['terminalist']   = $this->order_model->allterminal_dropdown();
		$this->load->view('ordermanage/paymodal', $data);
	}

	public function paymultiple()
	{
		$this->db->where('order_id', $this->input->post('orderid'))->delete('table_details');
		$postdata				 = $this->input->post();
		$discount                = $this->input->post('granddiscount', true);
		$grandtotal              = $this->input->post('grandtotal', true);
		$orderid                 = $this->input->post('orderid', true);
		$paytype                 = $this->input->post('paytype', true);
		$cterminal               = $this->input->post('card_terminal', true);
		$mybank                  = $this->input->post('bank', true);
		$mydigit                 = $this->input->post('last4digit', true);
		$payamonts               = $this->input->post('paidamount', true);
		$paidamount = 0;
		$updatetordfordiscount = array(
			'totalamount'           => $this->input->post('grandtotal', true),
			'customerpaid'           => $this->input->post('grandtotal', true)
		);

		$this->db->where('order_id', $orderid);
		$this->db->update('customer_order', $updatetordfordiscount);
		$settinginfo = $this->order_model->settinginfo();
		if ($settinginfo->printtype == 1 || $settinginfo->printtype == 3) {
			$updatetDatap = array('invoiceprint' => 2);
			$this->db->where('order_id', $orderid);
			$this->db->update('customer_order', $updatetDatap);
		}
		$prebillinfo = $this->db->select('*')->from('bill')->where('order_id', $orderid)->get()->row();
		$customerid = $prebillinfo->customer_id;
		$finalgrandtotal = $this->input->post('grandtotal', true);
		/***********Add pointing***********/
		$scan = scandir('application/modules/');
		$getcus = "";
		foreach ($scan as $file) {
			if ($file == "loyalty") {
				if (file_exists(APPPATH . 'modules/' . $file . '/assets/data/env')) {
					$getcus = $customerid;
				}
			}
		}

		if (!empty($getcus)) {
			$isexitscusp = $this->db->select("*")->from('tbl_customerpoint')->where('customerid', $customerid)->get()->row();
			$totalgrtotal = round($finalgrandtotal);
			$checkpointcondition = "$totalgrtotal BETWEEN amountrangestpoint AND amountrangeedpoint";
			$getpoint = $this->db->select("*")->from('tbl_pointsetting')->get()->row();
			$calcpoint = $getpoint->earnpoint / $getpoint->amountrangestpoint;
			$thisordpoint = $calcpoint * $totalgrtotal;
			if (empty($isexitscusp)) {
				$updateum = array('membership_type' => 1);
				$this->db->where('customer_id', $customerid);
				$this->db->update('customer_info', $updateum);
				$pointstable2 = array(
					'customerid'   => $customerid,
					'amount'       => $totalgrtotal,
					'points'       => $thisordpoint + 10
				);
				$this->order_model->insert_data('tbl_customerpoint', $pointstable2);
			} else {
				$pamnt = $isexitscusp->amount + $totalgrtotal;
				$tpoints = $isexitscusp->points + $thisordpoint;
				$updatecpoint = array('amount' => $pamnt, 'points' => $tpoints);
				$this->db->where('customerid', $customerid);
				$this->db->update('tbl_customerpoint', $updatecpoint);
			}
			$updatemember = $this->db->select("*")->from('tbl_customerpoint')->where('customerid', $customerid)->get()->row();
			$lastupoint = $updatemember->points;
			$updatecond = "'" . $lastupoint . "' BETWEEN startpoint AND endpoint";
			$checkmembership = $this->db->select("*")->from('membership')->where($updatecond)->get()->row();
			if (!empty($checkmembership)) {
				$updatememsp = array('membership_type' => $checkmembership->id);
				$this->db->where('customer_id', $customerid);
				$this->db->update('customer_info', $updatememsp);
			}
			$isredeem = $this->input->post('isredeempoint', true);
			if (!empty($isredeem)) {
				$updateredeem = array('amount' => 0, 'points' => 0);
				$this->db->where('customerid', $isredeem);
				$this->db->update('tbl_customerpoint', $updateredeem);
			}
		}

		/*******end Point**************/

		if ($discount > 0) {
			$finaldis = $discount + $prebillinfo->discount;
		} else {
			$finaldis = $prebillinfo->discount;
		}
		$updatetprebill = array(
			'discount'              => $finaldis,
			'bill_amount'           => $this->input->post('grandtotal', true)
		);

		$this->db->where('order_id', $orderid);
		$this->db->update('bill', $updatetprebill);
		$i = 0;
		$billinfo = $this->db->select('*')->from('bill')->where('order_id', $orderid)->get()->row();
		foreach ($payamonts  as $payamont) {
			$paidamount = $paidamount + $payamont;
			$data_pay = array(
				'paytype' => $paytype[$i], 'cterminal' => $cterminal[$i],
				'mybank' => $mybank[$i], 'mydigit' => $mydigit[$i], 'payamont' => $payamont
			);
			$this->add_multipay($orderid, $billinfo->bill_id, $data_pay);
			$i++;
		}
		$cpaidamount =	$paidamount;
		$orderinfo = $this->order_model->uniqe_order_id($orderid);
		$duevalue = ($orderinfo->totalamount - $orderinfo->customerpaid);
		if ($paidamount == $duevalue || $duevalue <  $paidamount) {
			$paidamount  = $paidamount + $orderinfo->customerpaid;
			$status = 4;
		} else {
			$paidamount  = $paidamount + $orderinfo->customerpaid;

			$status = 3;
		}

		$saveid = $this->session->userdata('id');
		$updatetData = array(
			'order_status'     => $status,
			'customerpaid'     => $cpaidamount,
		);
		$this->db->where('order_id', $orderid);
		$this->db->update('customer_order', $updatetData);
		//Update Bill Table
		if ($status == 4) {
			$updatetbill = array(
				'bill_status'           => 1,
				'payment_method_id'     => $paytype[0],
				'create_by'     		   => $saveid,
				'create_at'     		   => date('Y-m-d H:i:s')
			);
			$this->db->where('order_id', $orderid);
			$this->db->update('bill', $updatetbill);
		}
		if ($status == 4) {
			$this->removeformstock($orderid);
			$orderinfo = $this->db->select('*')->from('customer_order')->where('order_id', $orderid)->get()->row();
			$cusinfo = $this->db->select('*')->from('customer_info')->where('customer_id', $orderinfo->customer_id)->get()->row();
			$finalill = $this->db->select('*')->from('bill')->where('order_id', $orderid)->get()->row();
			$headn = $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name;
			$coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
			$customer_headcode = $coainfo->HeadCode;

			$invoice_no = $orderinfo->saleinvoice;
			$saveid = $this->session->userdata('id');
			//Customer debit for Product Value
			$cosdr = array(
				'VNo'            =>  $invoice_no,
				'Vtype'          =>  'CIV',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          =>  $customer_headcode,
				'Narration'      =>  'Customer debit for Product Invoice#' . $invoice_no,
				'Debit'          =>  $finalill->bill_amount,
				'Credit'         =>  0,
				'StoreID'        =>  0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $cosdr);
			//Store credit for Product Value
			$sc = array(
				'VNo'            =>  $invoice_no,
				'Vtype'          =>  'CIV',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          =>  10107,
				'Narration'      =>  'Inventory Credit for Product Invoice#' . $invoice_no,
				'Debit'          =>  0,
				'Credit'         =>  $finalill->bill_amount,
				'StoreID'        =>  0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $sc);

			// Customer Credit for paid amount.
			$cc = array(
				'VNo'            =>  $invoice_no,
				'Vtype'          =>  'CIV',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          =>  $customer_headcode,
				'Narration'      =>  'Customer Credit for Product Invoice#' . $invoice_no,
				'Debit'          =>  0,
				'Credit'         =>  $finalill->bill_amount,
				'StoreID'        =>  0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $cc);


			// Income for company							 
			$income = array(
				'VNo'            => "Sale" . $orderinfo->saleinvoice,
				'Vtype'          => 'Sales Products',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          => 303,
				'Narration'      => 'Sale Income For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
				'Debit'          => 0,
				'Credit'         => $finalill->bill_amount - $finalill->VAT, //purchase price asbe
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $income);

			// Tax Pay for company							 
			$income = array(
				'VNo'            => "Sale" . $orderinfo->saleinvoice,
				'Vtype'          => 'Sales Products Vat',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          => 502030101,
				'Narration'      => 'Sale TAX For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
				'Debit'          => $finalill->VAT,
				'Credit'         => 0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $income);
		}
		$logData = array(
			'action_page'         => "Order List",
			'action_done'     	 => "Insert Data",
			'remarks'             => "Order is Update",
			'user_name'           => $this->session->userdata('fullname'),
			'entry_date'          => date('Y-m-d H:i:s'),
		);
		$this->logs_model->log_recorded($logData);
		$this->savekitchenitem($orderid);
		$data['ongoingorder']  = $this->order_model->get_ongoingorder();
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderlist";
		$view = $this->posprintdirect($orderid);
		echo $view;
		exit;
	}
	public function savekitchenitem($orderid)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$this->db->select('order_menu.*,item_foods.kitchenid');
		$this->db->from('order_menu');
		$this->db->join('item_foods', 'order_menu.menu_id=item_foods.ProductsID', 'Left');
		$this->db->where('order_menu.order_id', $orderid);
		$query = $this->db->get();
		$result = $query->result();

		foreach ($result as $single) {
			$isexist = $this->db->select('*')->from('tbl_kitchen_order')->where('kitchenid', $single->kitchenid)->where('orderid', $single->order_id)->where('itemid', $single->menu_id)->where('varient', $single->varientid)->get()->row();
			if (empty($isexist)) {
				$inserekit = array(
					'kitchenid'			=>	$single->kitchenid,
					'orderid'			=>	$single->order_id,
					'itemid'		    =>	$single->menu_id,
					'varient'		    =>	$single->varientid,
				);
				$this->db->insert('tbl_kitchen_order', $inserekit);
			}
			$updatetmenu = array(
				'food_status'           => 1,
				'allfoodready'     	   => 1
			);
			$this->db->where('order_id', $orderid);
			$this->db->update('order_menu', $updatetmenu);
		}
	}
	public function add_multipay($orderid, $billid, $array_post)
	{
		$multipay = array(
			'order_id'			=>	$orderid,
			'payment_type_id'	=>	$array_post['paytype'],
			'amount'		    =>	$array_post['payamont'],
		);

		$this->db->insert('multipay_bill', $multipay);
		$multipay_id = $this->db->insert_id();
		$orderinfo = $this->db->select('*')->from('customer_order')->where('order_id', $orderid)->get()->row();
		$cusinfo = $this->db->select('*')->from('customer_info')->where('customer_id', $orderinfo->customer_id)->get()->row();
		if ($array_post['paytype'] != 1) {
			if ($array_post['paytype'] == 4) {
				$headcode = 1020101;
			} else {
				$paytype = $this->db->select('payment_method')->from('payment_method')->where('payment_method_id', $array_post['paytype'])->get()->row();
				$coainfo = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $paytype->payment_method)->get()->row();
				$headcode = $coainfo->HeadCode;
			}
			$saveid = $this->session->userdata('id');

			//Income for company
			$income3 = array(
				'VNo'            => "Sale" . $orderinfo->saleinvoice,
				'Vtype'          => 'Sales Products',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          => $headcode,
				'Narration'      => 'Sale Income For Online payment' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
				'Debit'          => $array_post['payamont'],
				'Credit'         => 0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $income3);
		}

		if ($array_post['paytype'] == 1) {
			$cardinfo = array(
				'bill_id'			    =>	$billid,
				'multipay_id'			=>	$multipay_id,
				'card_no'		        =>	$array_post['mydigit'],
				'terminal_name'		    =>	$array_post['cterminal'],
				'bank_name'	            =>	$array_post['mybank'],
			);

			$this->db->insert('bill_card_payment', $cardinfo);
			$bankinfo = $this->db->select('bank_name')->from('tbl_bank')->where('bankid', $array_post['mybank'])->get()->row();
			$coainfo = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $bankinfo->bank_name)->get()->row();

			$saveid = $this->session->userdata('id');
			$income2 = array(
				'VNo'            => "Sale" . $orderinfo->saleinvoice,
				'Vtype'          => 'Sales Products',
				'VDate'          => $orderinfo->order_date,
				'COAID'          => $coainfo->HeadCode,
				'Narration'      => 'Sale Income For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
				'Debit'          => $array_post['payamont'],
				'Credit'         => 0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $income2);
		}
	}

	public function changeMargeorder()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['orderamount'] = $this->input->post('paidamount', true);
		$discount                = $this->input->post('granddiscount', true);
		$singlediscount          = $this->input->post('discount', true);
		$grandtotal              = $this->input->post('grandtotal', true);
		$data['rendom_number'] = generateRandomStr();
		$data['multipay'] = $this->input->post('paytype', true);
		$data['allcard'] = $this->input->post('card_terminal', true);
		$data['allbank'] = $this->input->post('bank', true);
		$data['alldigity'] = $this->input->post('last4digit', true);
		$i = 0;
		$countord = count($this->input->post('order', true));
		foreach ($this->input->post('order', true) as $order_id) {
			$this->removeformstock($order_id);
			$this->db->where('order_id', $order_id)->delete('table_details');
			$paytype = $this->input->post('paytype', true);
			$myterminal = $this->input->post('card_terminal', true);
			$mibank = $this->input->post('bank', true);
			$midigit = $this->input->post('last4digit', true);
			$orderinfo = $this->order_model->uniqe_order_id($order_id);
			$prebill = $this->db->select('*')->from('bill')->where('order_id', $order_id)->get()->row();
			$disamount = $discount / $countord;
			$updatetord = array(
				'totalamount'            => $orderinfo->totalamount - $disamount,
				'customerpaid'           => $orderinfo->totalamount - $disamount
			);
			$this->db->where('order_id', $order_id);
			$this->db->update('customer_order', $updatetord);

			$settinginfo = $this->order_model->settinginfo();
			if ($settinginfo->printtype == 1 || $settinginfo->printtype == 3) {
				$updatetDatap = array('invoiceprint' => 2);
				$this->db->where('order_id', $orderid);
				$this->db->update('customer_order', $updatetDatap);
			}

			if ($disamount > 0) {
				$finaldis = $disamount + $prebill->discount;
			} else {
				$finaldis = $prebill->discount;
			}
			$updatetprebill = array(
				'discount'              => $finaldis,
				'bill_amount'           => $orderinfo->totalamount - $disamount
			);
			$this->db->where('order_id', $order_id);
			$this->db->update('bill', $updatetprebill);


			$data['orderid'] = $order_id;
			$data['status'] = 4;
			$data['paytype'] = $paytype[$i];
			$data['cterminal'] = $myterminal[$i];
			$data['mybank'] = $mibank[$i];
			$data['mydigit'] = $midigit[$i];
			$data['customer_id'] = $orderinfo->customer_id;
			$data['paid'] = $orderinfo->totalamount;
			$this->changestatusOrder($data);

			$i++;
		}
		$ordarray = $this->input->post('order', true);
		$checkismargeid = $this->db->select('*')->from('customer_order')->where('order_id', $ordarray[0])->get()->row();
		if (empty($checkismargeid)) {
			$marge_order_id = date('Y-m-d') . _ . $data['rendom_number'];
		} else {
			$marge_order_id = $checkismargeid->marge_order_id;
		}

		$mydata['margeid'] = $marge_order_id;
		$allorderinfo = $this->order_model->margeview($marge_order_id);
		$allorderid = '';
		$totalamount = 0;
		$m = 0;
		foreach ($allorderinfo as $ordersingle) {
			$mydata['billorder'][$m] = $ordersingle->order_id;
			$allorderid .= $ordersingle->order_id . ',';
			$totalamount = $totalamount + $ordersingle->totalamount;
			$m++;
		}
		$mydata['billinfo'] = $this->order_model->margebill($marge_order_id);
		$billinfo = $this->db->select('*')->from('bill')->where('order_id', $mydata['billinfo'][0]->order_id)->get()->row();
		$mydata['cashierinfo']   = $this->order_model->read('*', 'user', array('id' => $billinfo->create_by));
		$mydata['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $mydata['billinfo'][0]->customer_id));
		$mydata['billdate'] = $billinfo->bill_date;
		$mydata['tableinfo'] = $this->order_model->read('*', 'rest_table', array('tableid' => $mydata['billinfo'][0]->table_no));
		$mydata['iteminfo'] = $allorderinfo;
		$mydata['grandtotalamount'] = $totalamount;
		$settinginfo = $this->order_model->settinginfo();
		$mydata['settinginfo'] = $settinginfo;
		$mydata['taxinfos'] = $this->taxchecking();
		$mydata['storeinfo']      = $settinginfo;
		$mydata['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		echo $viewprint = $this->load->view('posmargeprint', $mydata, true);
	}
	public function changeMargedue()
	{
		$data['rendom_number'] = generateRandomStr();
		$i = 0;
		$countord = count($this->input->post('order', true));
		$marge_order_id = date('Y-m-d') . _ . $data['rendom_number'];
		foreach ($this->input->post('order', true) as $order_id) {
			$updatetprebill = array(
				'marge_order_id'              => $marge_order_id,
			);
			$this->db->where('order_id', $order_id);
			$this->db->update('customer_order', $updatetprebill);
		}
		$this->checkprintdue($marge_order_id);
	}
	public function checkprintdue($marge_order_id)
	{
		$mydata['margeid'] = $marge_order_id;
		$allorderinfo = $this->order_model->margeview($marge_order_id);
		$allorderid = '';
		$totalamount = 0;
		$m = 0;
		foreach ($allorderinfo as $ordersingle) {
			$mydata['billorder'][$m] = $ordersingle->order_id;
			$allorderid .= $ordersingle->order_id . ',';
			$totalamount = $totalamount + $ordersingle->totalamount;

			$m++;
		}
		$mydata['billinfo'] = $this->order_model->margebill($marge_order_id);
		$billinfo = $this->db->select('*')->from('bill')->where('order_id', $mydata['billinfo'][0]->order_id)->get()->row();
		$mydata['cashierinfo']   = $this->order_model->read('*', 'user', array('id' => $billinfo->create_by));

		$mydata['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $mydata['billinfo'][0]->customer_id));
		$mydata['billdate'] = $billinfo->bill_date;
		$mydata['tableinfo'] = $this->order_model->read('*', 'rest_table', array('tableid' => $mydata['billinfo'][0]->table_no));
		$mydata['iteminfo'] = $allorderinfo;
		$mydata['grandtotalamount'] = $totalamount;
		$settinginfo = $this->order_model->settinginfo();
		$mydata['settinginfo'] = $settinginfo;
		$mydata['taxinfos'] = $this->taxchecking();
		$mydata['storeinfo']      = $settinginfo;
		$mydata['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		echo $viewprint = $this->load->view('posmargeprintdue', $mydata, true);
	}
	public function checkprint($marge_order_id)
	{
		$mydata['margeid'] = $marge_order_id;
		$allorderinfo = $this->order_model->margeview($marge_order_id);
		$allorderid = '';
		$totalamount = 0;
		$m = 0;
		foreach ($allorderinfo as $ordersingle) {
			$mydata['billorder'][$m] = $ordersingle->order_id;
			$allorderid .= $ordersingle->order_id . ',';
			$totalamount = $totalamount + $ordersingle->totalamount;

			$m++;
		}
		$mydata['billinfo'] = $this->order_model->margebill($marge_order_id);
		$billinfo = $this->db->select('*')->from('bill')->where('order_id', $mydata['billinfo'][0]->order_id)->get()->row();
		$mydata['cashierinfo']   = $this->order_model->read('*', 'user', array('id' => $billinfo->create_by));

		$mydata['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $mydata['billinfo'][0]->customer_id));
		$mydata['billdate'] = $billinfo->bill_date;
		$mydata['tableinfo'] = $this->order_model->read('*', 'rest_table', array('tableid' => $mydata['billinfo'][0]->table_no));
		$mydata['iteminfo'] = $allorderinfo;
		$mydata['grandtotalamount'] = $totalamount;
		$settinginfo = $this->order_model->settinginfo();
		if ($settinginfo->printtype == 1 || $settinginfo->printtype == 3) {
			$updatetData = array('invoiceprint' => 2);
			$this->db->where('marge_order_id', $marge_order_id);
			$this->db->update('customer_order', $updatetData);
		}
		$mydata['settinginfo'] = $settinginfo;
		$mydata['taxinfos'] = $this->taxchecking();
		$mydata['storeinfo']      = $settinginfo;
		$mydata['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		echo $viewprint = $this->load->view('posmargeprint', $mydata, true);
	}
	public function changestatusOrder($value)
	{
		$saveid = $this->session->userdata('id');
		$orderid                 = $value['orderid'];
		$status                 = $value['status'];
		$paytype                 = $value['paytype'];
		$cterminal                 = $value['cterminal'];
		$mybank                  = $value['mybank'];
		$mydigit                 = $value['mydigit'];
		$paidamount              = $value['paid'];
		$multipayment               = $value['multipay'];
		$multipayid               = $value['rendom_number'];
		$orderamount			= $value['orderamount'];
		$allcard				= $value['allcard'];
		$allbank				= $value['allbank'];
		$alldigity			= $value['alldigity'];

		$orderinfo = $this->db->select('*')->from('customer_order')->where('order_id', $orderid)->get()->row();
		$cusinfo = $this->db->select('*')->from('customer_info')->where('customer_id', $orderinfo->customer_id)->get()->row();
		/***********Add pointing***********/
		$customerid = $orderinfo->customer_id;
		$scan = scandir('application/modules/');
		$getcus = "";
		foreach ($scan as $file) {
			if ($file == "loyalty") {
				if (file_exists(APPPATH . 'modules/' . $file . '/assets/data/env')) {
					$getcus = $customerid;
				}
			}
		}
		if (!empty($getcus)) {
			$isexitscusp = $this->db->select("*")->from('tbl_customerpoint')->where('customerid', $customerid)->get()->row();
			$totalgrtotal = round($finalgrandtotal);
			$checkpointcondition = "$totalgrtotal BETWEEN amountrangestpoint AND amountrangeedpoint";
			$getpoint = $this->db->select("*")->from('tbl_pointsetting')->get()->row();
			$calcpoint = $getpoint->earnpoint / $getpoint->amountrangestpoint;
			$thisordpoint = $calcpoint * $totalgrtotal;
			if (empty($isexitscusp)) {
				$updateum = array('membership_type' => 1);
				$this->db->where('customer_id', $customerid);
				$this->db->update('customer_info', $updateum);
				$pointstable2 = array(
					'customerid'   => $customerid,
					'amount'       => $totalgrtotal,
					'points'       => $thisordpoint + 10
				);
				$this->order_model->insert_data('tbl_customerpoint', $pointstable2);
			} else {
				$pamnt = $isexitscusp->amount + $totalgrtotal;
				$tpoints = $isexitscusp->points + $thisordpoint;
				$updatecpoint = array('amount' => $pamnt, 'points' => $tpoints);
				$this->db->where('customerid', $customerid);
				$this->db->update('tbl_customerpoint', $updatecpoint);
			}
			$updatemember = $this->db->select("*")->from('tbl_customerpoint')->where('customerid', $customerid)->get()->row();
			$lastupoint = $updatemember->points;
			$updatecond = "'" . $lastupoint . "' BETWEEN startpoint AND endpoint";
			$checkmembership = $this->db->select("*")->from('membership')->where($updatecond)->get()->row();
			if (!empty($checkmembership)) {
				$updatememsp = array('membership_type' => $checkmembership->id);
				$this->db->where('customer_id', $customerid);
				$this->db->update('customer_info', $updatememsp);
			}
			$isredeem = $this->input->post('isredeempoint', true);
			if (!empty($isredeem)) {
				$updateredeem = array('amount' => 0, 'points' => 0);
				$this->db->where('customerid', $isredeem);
				$this->db->update('tbl_customerpoint', $updateredeem);
			}
		}

		/*******end Point**************/
		$marge_order_id = date('Y-m-d') . _ . $value['rendom_number'];
		$updatetData = array(
			'marge_order_id' => $marge_order_id,
			'order_status'     => $status,
		);
		$this->db->where('order_id', $orderid);
		$this->db->update('customer_order', $updatetData);
		//Update Bill Table
		$updatetbill = array(
			'bill_status'           => 1,
			'payment_method_id'     => $paytype,
			'create_by'			   => $saveid,
			'create_at'     		   => date('Y-m-d H:i:s')
		);
		$this->db->where('order_id', $orderid);
		$this->db->update('bill', $updatetbill);
		$billinfo = $this->db->select('*')->from('bill')->where('order_id', $orderid)->get()->row();
		if (!empty($billinfo)) {
			$billid = $billinfo->bill_id;
			$checkmultipay = $this->db->select('*')->from('multipay_bill')->where('multipayid', $marge_order_id)->get()->row();
			$payid = '';
			if (empty($checkmultipay)) {
				$k = 0;
				foreach ($multipayment as $ptype) {
					$multipay = array(
						'order_id'			=>	$orderid,
						'payment_type_id'	=>	$ptype,
						'multipayid'		=>	$marge_order_id,
						'amount'		    =>	$orderamount[$k],
					);
					$this->db->insert('multipay_bill', $multipay);
					$multipay_id = $this->db->insert_id();

					if ($ptype != 1) {
						if ($ptype == 4) {
							$headcode = 1020101;
						} else {
							$paytype = $this->db->select('payment_method')->from('payment_method')->where('payment_method_id', $ptype)->get()->row();
							$coainfo = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $paytype->payment_method)->get()->row();
							$headcode = $coainfo->HeadCode;
						}
						// Income for company
						$income3 = array(
							'VNo'            => "Sale" . $orderinfo->saleinvoice,
							'Vtype'          => 'Sales Products',
							'VDate'          =>  $orderinfo->order_date,
							'COAID'          => $headcode,
							'Narration'      => 'Sale Income For Online payment' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
							'Debit'          => $orderamount[$k],
							'Credit'         => 0,
							'IsPosted'       => 1,
							'CreateBy'       => $saveid,
							'CreateDate'     => $orderinfo->order_date,
							'IsAppove'       => 1
						);
						$this->db->insert('acc_transaction', $income3);
					}

					if ($ptype == 1) {
						$cardinfo = array(
							'bill_id'			    =>	$billid,
							'card_no'		        =>	$alldigity[$k],
							'terminal_name'		    =>	$allcard[$k],
							'multipay_id'	   		=>	$multipay_id,
							'bank_name'	            =>	$allbank[$k],
						);
						$this->db->insert('bill_card_payment', $cardinfo);

						$bankinfo = $this->db->select('bank_name')->from('tbl_bank')->where('bankid', $allbank[$k])->get()->row();
						$coainfo = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $bankinfo->bank_name)->get()->row();

						$saveid = $this->session->userdata('id');
						$income2 = array(
							'VNo'            => "Sale" . $orderinfo->saleinvoice,
							'Vtype'          => 'Sales Products',
							'VDate'          =>  $orderinfo->order_date,
							'COAID'          => $coainfo->HeadCode,
							'Narration'      => 'Sale Income For Bank debit' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
							'Debit'          => $orderamount[$k],
							'Credit'         => 0,
							'IsPosted'       => 1,
							'CreateBy'       => $saveid,
							'CreateDate'     => $orderinfo->order_date,
							'IsAppove'       => 1
						);
						$this->db->insert('acc_transaction', $income2);
					}
					$k++;
				}
			}
		}
		if ($status == 4) {
			$customerinfo = $this->db->select('*')->from('customer_info')->where('customer_id', $billinfo->customer_id)->get()->row();
		}
		$orderinfo = $this->db->select('*')->from('customer_order')->where('order_id', $orderid)->get()->row();
		$cusinfo = $this->db->select('*')->from('customer_info')->where('customer_id', $orderinfo->customer_id)->get()->row();
		$this->savekitchenitem($orderid);
		$finalill = $this->db->select('*')->from('bill')->where('order_id', $orderid)->get()->row();
		$headn = $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name;
		$coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
		$customer_headcode = $coainfo->HeadCode;

		$invoice_no = $orderinfo->saleinvoice;
		$saveid = $this->session->userdata('id');
		//Customer debit for Product Value
		$cosdr = array(
			'VNo'            =>  $invoice_no,
			'Vtype'          =>  'CIV',
			'VDate'          =>  $orderinfo->order_date,
			'COAID'          =>  $customer_headcode,
			'Narration'      =>  'Customer debit for Product Invoice#' . $invoice_no,
			'Debit'          =>  $finalill->bill_amount,
			'Credit'         =>  0,
			'StoreID'        =>  0,
			'IsPosted'       => 1,
			'CreateBy'       => $saveid,
			'CreateDate'     => $orderinfo->order_date,
			'IsAppove'       => 1
		);
		$this->db->insert('acc_transaction', $cosdr);
		//Store credit for Product Value
		$sc = array(
			'VNo'            =>  $invoice_no,
			'Vtype'          =>  'CIV',
			'VDate'          =>  $orderinfo->order_date,
			'COAID'          =>  10107,
			'Narration'      =>  'Inventory Credit for Product Invoice#' . $invoice_no,
			'Debit'          =>  0,
			'Credit'         =>  $finalill->bill_amount,
			'StoreID'        =>  0,
			'IsPosted'       => 1,
			'CreateBy'       => $saveid,
			'CreateDate'     => $orderinfo->order_date,
			'IsAppove'       => 1
		);
		$this->db->insert('acc_transaction', $sc);

		// Customer Credit for paid amount.
		$cc = array(
			'VNo'            =>  $invoice_no,
			'Vtype'          =>  'CIV',
			'VDate'          =>  $orderinfo->order_date,
			'COAID'          =>  $customer_headcode,
			'Narration'      =>  'Customer Credit for Product Invoice#' . $invoice_no,
			'Debit'          =>  0,
			'Credit'         =>  $finalill->bill_amount,
			'StoreID'        =>  0,
			'IsPosted'       => 1,
			'CreateBy'       => $saveid,
			'CreateDate'     => $orderinfo->order_date,
			'IsAppove'       => 1
		);
		$this->db->insert('acc_transaction', $cc);
		// Income for company							 
		$income = array(
			'VNo'            => "Sale" . $orderinfo->saleinvoice,
			'Vtype'          => 'Sales Products',
			'VDate'          =>  $orderinfo->order_date,
			'COAID'          => 303,
			'Narration'      => 'Sale Income For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
			'Debit'          => 0,
			'Credit'         => $finalill->bill_amount - $finalill->VAT, //purchase price asbe
			'IsPosted'       => 1,
			'CreateBy'       => $saveid,
			'CreateDate'     => $orderinfo->order_date,
			'IsAppove'       => 1
		);
		$this->db->insert('acc_transaction', $income);

		// Tax Pay for company							 
		$income = array(
			'VNo'            => "Sale" . $orderinfo->saleinvoice,
			'Vtype'          => 'Sales Products Vat',
			'VDate'          =>  $orderinfo->order_date,
			'COAID'          => 502030101,
			'Narration'      => 'Sale TAX For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
			'Debit'          => $finalill->VAT,
			'Credit'         => 0,
			'IsPosted'       => 1,
			'CreateBy'       => $saveid,
			'CreateDate'     => $orderinfo->order_date,
			'IsAppove'       => 1
		);
		$this->db->insert('acc_transaction', $income);

		$logData = array(
			'action_page'         => "Order List",
			'action_done'     	 => "Insert Data",
			'remarks'             => "Order is Update",
			'user_name'           => $this->session->userdata('fullname'),
			'entry_date'          => date('Y-m-d H:i:s'),
		);
		$this->logs_model->log_recorded($logData);
	}

	public function showljslang()
	{
		$settinginfo = $this->order_model->settinginfo();
		$data['language'] = $this->order_model->settinginfolanguge($settinginfo->language);

		header('Content-Type: text/javascript');
		echo ('window.lang = ' . json_encode($data['language']) . ';');
		exit();
	}

	public function checktablecap($id)
	{
		$value = $this->order_model->read('person_capicity', 'rest_table', array('tableid' => $id));
		$total_sum = $this->order_model->get_table_total_customer($id);
		$present_free = $value->person_capicity - $total_sum->total;
		echo $present_free;
		exit;
	}

	public function showtablemodal()
	{
		$data['tablefloor'] = $this->order_model->tablefloor();
		$this->load->view('tablemodal', $data);
	}
	public function fllorwisetable()
	{
		$floorid = $this->input->post('floorid');
		$data['tableinfo'] = $this->order_model->get_table_total($floorid);
		$this->load->view('tableview', $data);
	}
	public function delete_table_details($id)
	{
		$this->db->where('id', $id)->delete('table_details');
		echo '1';
	}
	public function delete_table_details_all($id)
	{
		$this->db->where('table_id', $id)->delete('table_details');
		echo '1';
	}
	public function checkstock()
	{

		$orderid = $this->input->post('orderid');
		$iteminfos       = $this->order_model->customerorder($orderid);
		$available = 1;
		foreach ($iteminfos as $iteminfo) {
			$foodid = $iteminfo->menu_id;
			$qty = $iteminfo->menuqty;
			$vid = $iteminfo->varientid;
			$available = $this->order_model->checkingredientstock($foodid, $vid, $qty);
			if ($available != 1) {
				break;
			}
		}
		echo $available;
	}

	public function removeformstock($orderid)
	{
		$possetting = $this->db->select('*')->from('tbl_posetting')->where('possettingid', 1)->get()->row();
		if ($possetting->productionsetting == 1) {
			$items = $this->order_model->customerorder($orderid);
			foreach ($items as $item) {

				$this->order_model->insert_product($item->menu_id, $item->varientid, $item->menuqty);
			}
		}
		return $possetting->productionsetting;
	}

	/*start split order methods*/
	public function showsplitorder($orderid)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$array_id  = array('order_id' => $orderid);
		$order_info = $this->order_model->read('*', 'customer_order', $array_id);
		$settinginfo = $this->order_model->settinginfo();
		$data['settinginfo'] = $settinginfo;
		$data['taxinfos'] = $this->taxchecking();
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['order_info'] = $order_info;
		$data['iteminfo']       = $this->order_model->customerorder($orderid);
		$data['module'] = "ordermanage";
		$data['suborder_info'] = $this->order_model->read_all('*', 'sub_order', $array_id);
		$i = 0;
		if (!empty($data['suborder_info'])) {
			foreach ($data['suborder_info'] as $suborderitem) {
				if (!empty($suborderitem->order_menu_id)) {
					$presentsub = unserialize($suborderitem->order_menu_id);
					$menuarray = array_keys($presentsub);
					$data['suborder_info'][$i]->suborderitem = $this->order_model->updateSuborderDatalist($menuarray);
				} else {
					$data['suborder_info'][$i]->suborderitem = '';
				}
				$i++;
			}
		}
		$array_bill = array('order_id' => $orderid);
		$data['service'] = $this->order_model->read('service_charge', 'bill', $array_bill);
		$data['customerlist']   = $this->order_model->customer_dropdown();
		$this->load->view('ordermanage/splitorder', $data);
	}
	public function showsuborder($num)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$orderid = $this->input->post('orderid');
		$array_biil_id = array('order_id' => $orderid);
		$billinfo = $this->order_model->read('*', 'bill', $array_biil_id);
		$data['num'] = $num;
		$data['service_chrg_data'] = $billinfo->service_charge / $num;
		$data['orderid'] = $orderid;
		$data['customerlist']   = $this->order_model->customer_dropdown();
		$insertid = array();
		$this->db->where('order_id', $orderid)->delete('sub_order');
		for ($i = 0; $i < $num; $i++) {
			$sub_order = array(
				'order_id' => $orderid,

			);
			$this->db->insert('sub_order', $sub_order);
			$insertid[$i] = $this->db->insert_id();
		}
		$data['suborderid'] = $insertid;
		$this->load->view('ordermanage/showsuborder', $data);
	}



	public function showsuborderdetails()
	{
		$orderid = $this->input->post('orderid');
		$array_id  = array('order_id' => $orderid);
		$menuid = $this->input->post('menuid');
		$suborderid = $this->input->post('suborderid');
		$service_chrg_data = $this->input->post('service_chrg', true);
		$sdtotal = $this->order_model->read('service_charge', 'bill', $array_bill);
		$data['suborder_info'] = $this->order_model->read_all('*', 'sub_order', $array_id);
		//print_r($data['suborder_info']);
		$order_menu = $this->order_model->updateSuborderData($menuid);
		$presentsub = array();
		$array_id = array('sub_id' => $suborderid);
		$addonsidarray = '';
		$addonsqty = '';
		$order_sub = $this->order_model->read('*', 'sub_order', $array_id);
		$check_id = array('order_menuid' => $menuid);
		$check_info = $this->order_model->read('*', 'check_addones', $check_id);
		if (!empty($order_menu->add_on_id) && empty($check_info)) {

			$addonsidarray = $order_menu->add_on_id;
			$addonsqty = $order_menu->addonsqty;

			$is_addons = array(
				'order_menuid' => $menuid,
				'sub_order_id' => $suborderid,
				'status' => 1

			);
			$this->db->insert('check_addones', $is_addons);
		}
		if (!empty($order_sub->order_menu_id)) {
			$presentsub = unserialize($order_sub->order_menu_id);
			if (array_key_exists($menuid, $presentsub)) {
				$presentsub[$menuid] = $presentsub[$menuid] + 1;
			} else {
				$presentsub[$menuid] = 1;
			}
		} else {
			$presentsub = array($menuid => 1);
		}
		$order_menu_id = serialize($presentsub);

		if (empty($addonsidarray)) {
			$updatetready = array(
				'order_menu_id'           => $order_menu_id,

			);
		} else {
			$updatetready = array(
				'order_menu_id'           => $order_menu_id,
				'adons_id'				  => $addonsidarray,
				'adons_qty'				  => $addonsqty
			);
		}
		$this->db->where('sub_id', $suborderid);
		$this->db->update('sub_order', $updatetready);
		$menuarray = array_keys($presentsub);
		$data['iteminfo'] = $this->order_model->updateSuborderDatalist($menuarray);
		$data['taxinfos'] = $this->taxchecking();
		$data['presenttab'] = $presentsub;
		$data['settinginfo'] = $this->order_model->settinginfo();
		$data['suborderid'] = $suborderid;
		$data['service_chrg_data'] = $service_chrg_data;
		$data['SDtotal'] = $sdtotal;

		$this->load->view('ordermanage/showsuborderdetails', $data);
	}

	public function paysuborder()
	{
		$service = $this->input->post('service', true);
		$sub_id = $this->input->post('sub_id');
		$vat = $this->input->post('vat', true);
		$total = $this->input->post('total', true);
		$customerid = $this->input->post('customerid');
		$gtotal = $service + $vat + $total;
		$updatetordfordiscount = array(
			'vat'           => $vat,
			's_charge'      => $service,
			'total_price'   => $total,
			'customer_id'   => $customerid,

		);

		$this->db->where('sub_id', $sub_id);
		$this->db->update('sub_order', $updatetordfordiscount);
		$data['settinginfo'] = $this->order_model->settinginfo();

		$data['totaldue'] = $gtotal;
		$data['sub_id'] = $sub_id;
		$data['paymentmethod']   = $this->order_model->pmethod_dropdown();
		$data['banklist']      = $this->order_model->bank_dropdown();
		$data['terminalist']   = $this->order_model->allterminal_dropdown();

		$this->load->view('ordermanage/suborderpay', $data);
	}

	public function paymultiplsub()
	{

		$postdata				 = $this->input->post();
		$discount                = $this->input->post('granddiscount', true);
		$grandtotal              = $this->input->post('grandtotal', true);
		$orderid                 = $this->input->post('orderid', true);
		$paytype                 = $this->input->post('paytype', true);
		$cterminal               = $this->input->post('card_terminal', true);
		$mybank                  = $this->input->post('bank', true);
		$mydigit                 = $this->input->post('last4digit', true);
		$payamonts               = $this->input->post('paidamount', true);
		$paidamount = 0;
		$updatetordfordiscount = array(
			'status'           => 1,
			'discount'     		 => $discount

		);

		$this->db->where('sub_id', $orderid);
		$this->db->update('sub_order', $updatetordfordiscount);
		$settinginfo = $this->order_model->settinginfo();
		if ($settinginfo->printtype == 1 || $settinginfo->printtype == 3) {
			$updatetData = array('invoiceprint' => 2);
			$this->db->where('sub_id', $orderid);
			$this->db->update('sub_order', $updatetData);
		}
		$array_id = array('sub_id' => $orderid);
		$order_sub = $this->order_model->read('*', 'sub_order', $array_id);
		$order_id = $order_sub->order_id;
		$array_biil_id = array('order_id' => $order_id);
		$billinfo = $this->order_model->read('*', 'bill', $array_biil_id);
		$i = 0;

		foreach ($payamonts  as $payamont) {
			$paidamount = $paidamount + $payamont;
			$data_pay = array('paytype' => $paytype[$i], 'cterminal' => $cterminal[$i], 'mybank' => $mybank[$i], 'mydigit' => $mydigit[$i], 'payamont' => $payamont);
			$this->add_multipay($order_id, $billinfo->bill_id, $data_pay);
			$i++;
		}


		$logData = array(
			'action_page'         => "Order List",
			'action_done'     	 => "Insert Data",
			'remarks'             => "Order is Update",
			'user_name'           => $this->session->userdata('fullname'),
			'entry_date'          => date('Y-m-d H:i:s'),
		);

		$this->logs_model->log_recorded($logData);
		$where_array = array('status' => 0, 'order_id' => $order_id);
		$orderData = array(
			'splitpay_status'     => 1,
			'invoiceprint'     => 2,
		);
		$this->db->where('order_id', $order_id);

		$this->db->update('customer_order', $orderData);
		$totalorder = $this->db->select('*')->from('sub_order')->where('status', 0)->where('order_id', $order_id)->get()->num_rows();
		if ($totalorder == 0) {
			$totandiscount = $this->db->select('SUM(discount) as totaldiscount')->from('sub_order')->where('order_id', $order_id)->get()->row();
			$billinfo = $this->db->select('bill_amount')->from('bill')->where('order_id', $order_id)->get()->row();
			$saveid = $this->session->userdata('id');
			$this->savekitchenitem($order_id);
			$this->removeformstock($order_id);
			$orderData = array(
				'order_status'     => 4,
			);
			$this->db->where('order_id', $order_id);
			$this->db->update('customer_order', $orderData);

			$updatetbill = array(
				'bill_status'           => 1,
				'discount'			   => $totandiscount->totaldiscount,
				'bill_amount'		   => $billinfo->bill_amount - $totandiscount->totaldiscount,
				'payment_method_id'     => $paytype[0],
				'create_by'     		   => $saveid,
				'create_at'     		   => date('Y-m-d H:i:s')
			);
			$this->db->where('order_id', $order_id);
			$this->db->update('bill', $updatetbill);
			$this->savekitchenitem($orderid);
			$this->db->where('order_id', $order_id)->delete('table_details');

			$orderinfo = $this->db->select('*')->from('customer_order')->where('order_id', $order_id)->get()->row();
			$finalill = $this->db->select('*')->from('bill')->where('order_id', $order_id)->get()->row();
			$headn = $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name;
			$coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
			$customer_headcode = $coainfo->HeadCode;

			$invoice_no = $orderinfo->saleinvoice;
			$saveid = $this->session->userdata('id');
			//Customer debit for Product Value
			$cosdr = array(
				'VNo'            =>  $invoice_no,
				'Vtype'          =>  'CIV',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          =>  $customer_headcode,
				'Narration'      =>  'Customer debit for Product Invoice#' . $invoice_no,
				'Debit'          =>  $finalill->bill_amount,
				'Credit'         =>  0,
				'StoreID'        =>  0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $cosdr);
			//Store credit for Product Value
			$sc = array(
				'VNo'            =>  $invoice_no,
				'Vtype'          =>  'CIV',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          =>  10107,
				'Narration'      =>  'Inventory Credit for Product Invoice#' . $invoice_no,
				'Debit'          =>  0,
				'Credit'         =>  $finalill->bill_amount,
				'StoreID'        =>  0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $sc);

			// Customer Credit for paid amount.
			$cc = array(
				'VNo'            =>  $invoice_no,
				'Vtype'          =>  'CIV',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          =>  $customer_headcode,
				'Narration'      =>  'Customer Credit for Product Invoice#' . $invoice_no,
				'Debit'          =>  0,
				'Credit'         =>  $finalill->bill_amount,
				'StoreID'        =>  0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $cc);

			// Income for company							 
			$income = array(
				'VNo'            => "Sale" . $orderinfo->saleinvoice,
				'Vtype'          => 'Sales Products',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          => 303,
				'Narration'      => 'Sale Income For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
				'Debit'          => 0,
				'Credit'         => $finalill->bill_amount - $finalill->VAT, //purchase price asbe
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $income);

			// Tax Pay for company							 
			$income = array(
				'VNo'            => "Sale" . $orderinfo->saleinvoice,
				'Vtype'          => 'Sales Products Vat',
				'VDate'          =>  $orderinfo->order_date,
				'COAID'          => 502030101,
				'Narration'      => 'Sale TAX For ' . $cusinfo->cuntomer_no . '-' . $cusinfo->customer_name,
				'Debit'          => $finalill->VAT,
				'Credit'         => 0,
				'IsPosted'       => 1,
				'CreateBy'       => $saveid,
				'CreateDate'     => $orderinfo->order_date,
				'IsAppove'       => 1
			);
			$this->db->insert('acc_transaction', $income);
		}
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "updateorderlist";
		$view = $this->posprintdirectsub($orderid);

		echo $view;
		exit;
	}

	public function posprintdirectsub($id)
	{
		$array_id =  array('sub_id' => $id);
		$order_sub = $this->order_model->read('*', 'sub_order', $array_id);
		$presentsub = unserialize($order_sub->order_menu_id);
		$menuarray = array_keys($presentsub);
		$data['iteminfo'] = $this->order_model->updateSuborderDatalist($menuarray);
		$saveid = $this->session->userdata('id');
		$isadmin = $this->session->userdata('user_type');


		$data['orderinfo']  	   = $order_sub;
		$data['customerinfo']   = $this->order_model->read('*', 'customer_info', array('customer_id' => $order_sub->customer_id));

		$data['billinfo']	   = $this->order_model->billinfo($order_sub->order_id);
		$data['cashierinfo']   = $this->order_model->read('*', 'user', array('id' => $data['billinfo']->create_by));
		$data['mainorderinfo']  	   = $this->order_model->read('*', 'customer_order', array('order_id' => $order_sub->order_id));
		$data['tableinfo'] = $this->order_model->read('*', 'rest_table', array('tableid' => $data['mainorderinfo']->table_no));
		$settinginfo = $this->order_model->settinginfo();

		$data['settinginfo'] = $settinginfo;
		$data['storeinfo']      = $settinginfo;
		$data['currency'] = $this->order_model->currencysetting($settinginfo->currency);
		$data['taxinfos'] = $this->taxchecking();
		$data['module'] = "ordermanage";
		$data['page']   = "posinvoice";

		$view = $this->load->view('posprintsuborder', $data, true);
		echo $view;
		exit;
	}
	public function showsplitorderlist($order)
	{


		$data['suborder_info'] = $this->order_model->showsplitorderlist($order);

		$this->load->view('showsuborderlist', $data);
	}

	/*end split order methods*/
	/**Item information for kitchen*/
	public function counterlist()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('counter_list');
		$data['module'] 	= "ordermanage";
		$data['counterlist'] = $this->db->select('*')->from('tbl_cashcounter')->get()->result();
		$data['page']   = "cashcounter";
		echo Modules::run('template/layout', $data);
	}
	public function createcounter()
	{
		$data['title'] = display('counter_list');
		$this->form_validation->set_rules('counter', display('counter'), 'required');
		$postData = array(
			'ccid' 	        	=> $id,
			'counterno' 	        => $this->input->post('counter', true),
		);

		if ($this->form_validation->run() === true) {
			if ($this->order_model->createcounter($postData)) {
				#set success message
				$this->session->set_flashdata('message', display('save_successfully'));
			} else {
				#set exception message
				$this->session->set_flashdata('exception', display('please_try_again'));
			}

			redirect('ordermanage/order/counterlist');
		} else {
			$this->session->set_flashdata('exception', display('please_try_again'));
			redirect('ordermanage/order/counterlist');
		}
	}
	public function editcounter($id)
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('counter_list');
		$this->form_validation->set_rules('counter', display('counter'), 'required');
		$postData = array(
			'ccid' 	        		=> $id,
			'counterno' 	        => $this->input->post('counter', true),
		);
		if ($this->form_validation->run() === true) {
			if ($this->order_model->updatecounter($postData)) {
				#set success message
				$this->session->set_flashdata('message', display('update_successfully'));
			} else {
				#set exception message
				$this->session->set_flashdata('exception', display('please_try_again'));
			}

			redirect('ordermanage/order/counterlist');
		} else {
			$this->session->set_flashdata('exception', display('please_try_again'));
			redirect('ordermanage/order/counterlist');
		}
	}
	public function deletecounter($menuid = null)
	{
		$this->permission->method('ordermanage', 'delete')->redirect();
		if ($this->order_model->deletecounter($menuid)) {
			#set success message
			$this->session->set_flashdata('message', display('delete_successfully'));
		} else {
			#set exception message
			$this->session->set_flashdata('exception', display('please_try_again'));
		}
		redirect('ordermanage/order/counterlist');
	}
	public function cashregister()
	{
		$saveid = $this->session->userdata('id');
		$checkuser = $this->db->select('*')->from('tbl_cashregister')->where('userid', $saveid)->where('status', 0)->order_by('id', 'DESC')->get()->row();
		$openamount = $this->db->select('closing_balance')->from('tbl_cashregister')->where('userid', $saveid)->where('closing_balance>', '0.000')->order_by('id', 'DESC')->get()->row();

		$counterlist = $this->db->select('*')->from('tbl_cashcounter')->get()->result();
		$list[''] = 'Select Counter No';
		if (!empty($counterlist)) {
			foreach ($counterlist as $value)
				$list[$value->counterno] = $value->counterno;
		}
		$data['allcounter'] = $list;
		if (empty($checkuser)) {
			if ($openamount && $openamount->closing_balance > '0.000') {
				$data['openingbalance'] = $openamount->closing_balance;
			} else {
				$data['openingbalance'] = "0.000";
			}
			$this->load->view('cashregister', $data);
		} else {
			echo 1;
			exit;
		}
	}
	public function addcashregister()
	{
		$this->form_validation->set_rules('counter', display('counter'), 'required');
		$this->form_validation->set_rules('totalamount', display('amount'), 'required');
		$saveid = $this->session->userdata('id');
		$counter = $this->input->post('counter', true);
		$openingamount = $this->input->post('totalamount', true);
		$checkuser = $this->db->select('*')->from('tbl_cashregister')->where('counter_no', $counter)->where('status', 0)->order_by('id', 'DESC')->get()->row();
		if ($this->form_validation->run() === true) {
			$postData = array(
				'userid' 	        => $saveid,
				'counter_no' 	    => $this->input->post('counter', true),
				'opening_balance' 	=> $this->input->post('totalamount', true),
				'closing_balance' 	=> '0.000',
				'openclosedate' 	=> date('Y-m-d'),
				'opendate' 	        => date('Y-m-d H:i:s'),
				'closedate' 	    => "1970-01-01 00:00:00",
				'status' 	        => 0,
				'openingnote' 	    => $this->input->post('OpeningNote', true),
				'closing_note' 	    => "",
			);
			if (empty($checkuser)) {
				if ($this->order_model->addopeningcash($postData)) {
					echo 1;
				} else {
					echo 0;
				}
			} else {
				echo 0;
			}
		} else {
			echo 0;
		}
	}
	public function cashregisterclose()
	{
		$saveid = $this->session->userdata('id');
		$checkuser = $this->db->select('*')->from('tbl_cashregister')->where('userid', $saveid)->where('status', 0)->order_by('id', 'DESC')->get()->row();
		$data['userinfo'] = $this->db->select('*')->from('user')->where('id', $saveid)->get()->row();
		$data['registerinfo'] = $checkuser;
		$data['totalamount'] = $this->order_model->collectcash($saveid, $checkuser->opendate);
		$data['customertypewise'] = $this->order_model->customertypewise($saveid, $checkuser->opendate);
		$data['totalexpenses'] = $this->order_model->total_expenses($saveid, $checkuser->opendate);
		
		// Get expenses by category
		$expenses = $this->order_model->get_expenses($checkuser->opendate);
		$expensesByCategory = array();
		foreach ($expenses as $expense) {
			$categoryName = $expense->category_name ?: 'Uncategorized';
			if (!isset($expensesByCategory[$categoryName])) {
				$expensesByCategory[$categoryName] = 0;
			}
			$expensesByCategory[$categoryName] += $expense->total_amount;
		}
		$data['expensesByCategory'] = $expensesByCategory;
		
		$data['customertypewise'] = $data['customertypewise'][0];
		$findkitchen = $this->order_model->getKitchens(true);
		$kitchenItemsReport = array();
		foreach ($findkitchen as $kitchen) {
			$kitchenid = $kitchen->kitchenid;
			$kitchenname = $kitchen->kitchen_name;
			// print_r($kitchenid);
			$findkitchenitems = $this->order_model->itemsKiReport($kitchenid, $saveid, $checkuser->opendate);
			$kitchenItemsReport[] = array(
				'kitchenid' => $kitchenid,
				'kitchen_name' => $kitchenname,
				'items' => $findkitchenitems
			);
		}
		$data['kitchenItemsReport'] = $kitchenItemsReport;
		
		// Get detailed items sold information (similar to sellrptItems)
		$data['detailedItemsSold'] = $this->order_model->getDetailedItemsSoldByKitchen($saveid, $checkuser->opendate);
		if (!empty($checkuser)) {
			$this->load->view('cashregisterclose', $data);
		} else {
			echo 1;
			exit;
		}
	}

	public function dailycomprehensivereport()
	{
		$saveid = $this->session->userdata('id');
		
		// Get filter parameters
		$filterType = $this->input->get('filter_type') ?: 'current'; // current, date_range, cash_register, all_dates
		$startDate = $this->input->get('start_date');
		$endDate = $this->input->get('end_date');
		$cashRegisterId = $this->input->get('cash_register_id');
		
		// Check debug parameter
		$debugMode = $this->input->get('debug') == '1';
		$GLOBALS['debugMode'] = $debugMode;
		
		// Initialize query log for display on view (only if debug mode)
		$data['queryLog'] = array();
		if ($debugMode) {
			$data['queryLog'][] = "=== ALL SQL QUERIES FOR FILTER: $filterType ===";
			$data['queryLog'][] = "USER ID: $saveid | CASH REGISTER ID: $cashRegisterId";
		}
		
		// Get all cash registers for filter dropdown with counter names
		$data['cashRegisters'] = $this->db->select('cr.*, u.firstname, u.lastname, cc.counter_name')
			->from('tbl_cashregister cr')
			->join('user u', 'cr.userid = u.id', 'left')
			->join('tbl_cashcounter cc', 'cr.counter_no = cc.counterno', 'left')
			->where('cr.status', 1) // closed registers
			->order_by('cr.closedate', 'DESC')
			->get()->result();
		if ($debugMode) $data['queryLog'][] = "CASH REGISTERS QUERY: " . $this->db->last_query();
		
		// Get current open register
		$checkuser = $this->db->select('*')->from('tbl_cashregister')->where('userid', $saveid)->where('status', 0)->order_by('id', 'DESC')->get()->row();
		if ($debugMode) $data['queryLog'][] = "CURRENT REGISTER QUERY: " . $this->db->last_query();
		
		// Determine date range and register based on filter type
		$reportStartDate = null;
		$reportEndDate = null;
		$selectedRegister = null;
		
		switch ($filterType) {
			case 'current':
				if (empty($checkuser)) {
					$data['error'] = 'No open cash register found. Please open cash register first.';
					$data['reportDate'] = date('Y-m-d');
					$data['reportTime'] = date('H:i:s');
					$data['filterType'] = $filterType;
					$this->load->view('dailycomprehensivereport', $data);
					return;
				}
				$selectedRegister = $checkuser;
				$reportStartDate = $checkuser->opendate;
				$reportEndDate = date('Y-m-d H:i:s');
				break;
				
			case 'date_range':
				if (empty($startDate) || empty($endDate)) {
					$data['error'] = 'Please select both start and end dates.';
					$data['reportDate'] = date('Y-m-d');
					$data['reportTime'] = date('H:i:s');
					$data['filterType'] = $filterType;
					$this->load->view('dailycomprehensivereport', $data);
					return;
				}
				$reportStartDate = $startDate . ' 00:00:00';
				$reportEndDate = $endDate . ' 23:59:59';
				break;
				
			case 'cash_register':
				if (empty($cashRegisterId)) {
					$data['error'] = 'Please select a cash register.';
					$data['reportDate'] = date('Y-m-d');
					$data['reportTime'] = date('H:i:s');
					$data['filterType'] = $filterType;
					$this->load->view('dailycomprehensivereport', $data);
					return;
				}
				$selectedRegister = $this->db->select('*')->from('tbl_cashregister')->where('id', $cashRegisterId)->get()->row();
				if ($debugMode) $data['queryLog'][] = "SELECTED REGISTER QUERY: " . $this->db->last_query();
				if ($selectedRegister) {
					$reportStartDate = $selectedRegister->opendate;
					$reportEndDate = $selectedRegister->closedate ?: date('Y-m-d H:i:s');
					
					// Set up variables to use current register logic for this selected register
					$checkuser = $selectedRegister;
					$saveid = $selectedRegister->userid;
					log_message('debug', 'Cash register selected: ID=' . $cashRegisterId . ', User=' . $saveid . ', OpenDate=' . $reportStartDate);
					if ($debugMode) $data['queryLog'][] = "SELECTED REGISTER DETAILS: User=$saveid, OpenDate=$reportStartDate, CloseDate=$reportEndDate";
				} else {
					$data['error'] = 'Selected cash register not found.';
					$data['reportDate'] = date('Y-m-d');
					$data['reportTime'] = date('H:i:s');
					$data['filterType'] = $filterType;
					$this->load->view('dailycomprehensivereport', $data);
					return;
				}
				break;
				
			case 'all_dates':
				// Get earliest and latest dates from all registers
				$dateRange = $this->db->select('MIN(opendate) as min_date, MAX(CASE WHEN status = 1 THEN closedate ELSE NOW() END) as max_date')
					->from('tbl_cashregister')
					->get()->row();
				if ($dateRange) {
					$reportStartDate = $dateRange->min_date;
					$reportEndDate = $dateRange->max_date;
				}
				break;
		}
		
		// Set filter data for view
		$data['filterType'] = $filterType;
		$data['startDate'] = $startDate;
		$data['endDate'] = $endDate;
		$data['cashRegisterId'] = $cashRegisterId;
		$data['selectedRegister'] = $selectedRegister;

		// User and register info
		$data['userinfo'] = $this->db->select('*')->from('user')->where('id', $saveid)->get()->row();
		if ($debugMode) $data['queryLog'][] = "USER INFO QUERY: " . $this->db->last_query();
		$data['registerinfo'] = $selectedRegister ?: $checkuser;
		$data['reportStartDate'] = $reportStartDate;
		$data['reportEndDate'] = $reportEndDate;

		// === SALES DATA (with dynamic date filtering) ===
		if ($debugMode) $data['queryLog'][] = "========== SALES DATA QUERIES ==========";
		if (($filterType == 'current' || $filterType == 'cash_register') && $checkuser) {
			// Use the SAME working methods for both current and cash register
			// For closed registers, use both open and close dates
			$endDate = (isset($checkuser->closedate) && $checkuser->closedate != "1970-01-01 00:00:00") ? $checkuser->closedate : null;
			$endDateParam = $endDate ? ", '$endDate'" : '';
			
			if ($debugMode) $data['queryLog'][] = "CALLING collectcash($saveid, '{$checkuser->opendate}'$endDateParam)";
			$data['totalamount'] = $this->order_model->collectcash($saveid, $checkuser->opendate, $endDate);
			
			if ($debugMode) $data['queryLog'][] = "CALLING customertypewise($saveid, '{$checkuser->opendate}'$endDateParam)";
			$data['customertypewise'] = $this->order_model->customertypewise($saveid, $checkuser->opendate, $endDate);
			$data['customertypewise'] = !empty($data['customertypewise']) ? $data['customertypewise'][0] : null;
			
			if ($debugMode) $data['queryLog'][] = "CALLING total_expenses($saveid, '{$checkuser->opendate}'$endDateParam)";
			$data['totalexpenses'] = $this->order_model->total_expenses($saveid, $checkuser->opendate, $endDate);
			log_message('debug', 'Using current register sales methods for filter: ' . $filterType);
		} else {
			// Use filtered data collection for other filter types
			$data['totalamount'] = $this->_getFilteredSalesData($reportStartDate, $reportEndDate);
			
			// Get detailed customer type data for filtered results
			$detailedCustomerTypes = $this->_getFilteredDetailedCustomerTypeSales($reportStartDate, $reportEndDate);
			
			// Transform to expected format for view compatibility
			$employeeSales = 0;
			$guestSales = 0;
			$charitySales = 0;
			$regularSales = 0;
			
			foreach ($detailedCustomerTypes as $type) {
				$typeName = strtolower($type->customer_type ?: 'regular');
				if (strpos($typeName, 'employee') !== false) {
					$employeeSales += $type->total_amount;
				} elseif (strpos($typeName, 'guest') !== false) {
					$guestSales += $type->total_amount;
				} elseif (strpos($typeName, 'charity') !== false) {
					$charitySales += $type->total_amount;
				} else {
					$regularSales += $type->total_amount;
				}
			}
			
			// Create expected object structure
			$data['customertypewise'] = (object)array(
				'employee_sales' => $employeeSales,
				'guest_sales' => $guestSales,
				'charity_sales' => $charitySales,
				'regular_sales' => $regularSales,
				'total_sales' => $employeeSales + $guestSales + $charitySales + $regularSales
			);
			
			// Store detailed breakdown for use in calculations
			$data['detailedCustomerTypeSales'] = $detailedCustomerTypes;
			
			$data['totalexpenses'] = $this->_getFilteredExpenses($reportStartDate, $reportEndDate);
		}
		
		// Get expenses by category - use consistent logic with sales data
		if ($debugMode) $data['queryLog'][] = "========== EXPENSE DATA QUERIES ==========";
		if (($filterType == 'current' || $filterType == 'cash_register') && $checkuser) {
			// Use the SAME logic as sales data - register open/close times
			// For closed registers, use both open and close dates
			$endDate = (isset($checkuser->closedate) && $checkuser->closedate != "1970-01-01 00:00:00") ? $checkuser->closedate : null;
			$endDateParam = $endDate ? ", '$endDate'" : '';
			
			if ($endDate) {
				// Closed register - use _getFilteredExpensesByCategory with exact dates
				if ($debugMode) $data['queryLog'][] = "CALLING _getFilteredExpensesByCategory('{$checkuser->opendate}', '$endDate')";
				$expenses = $this->_getFilteredExpensesByCategory($checkuser->opendate, $endDate);
			} else {
				// Current register - use get_expenses from open date to now
				if ($debugMode) $data['queryLog'][] = "CALLING get_expenses('{$checkuser->opendate}')";
				$expenses = $this->order_model->get_expenses($checkuser->opendate);
			}
			$todayExpenses = array(); // Don't mix with other expense data
			log_message('debug', 'Using register-based expense methods for filter: ' . $filterType);
		} else {
			// For date range filtering, get expenses for the specified date range
			log_message('debug', 'Getting filtered expenses for date range: ' . $reportStartDate . ' to ' . $reportEndDate);
			if ($debugMode) $data['queryLog'][] = "CALLING _getFilteredExpensesByCategory('$reportStartDate', '$reportEndDate')";
			$expenses = $this->_getFilteredExpensesByCategory($reportStartDate, $reportEndDate);
			log_message('debug', 'Filtered expenses retrieved: ' . count($expenses) . ' records');
			$todayExpenses = array(); // Don't mix today's expenses with filtered data
		}
		
		// Merge both results to ensure completeness
		$allExpenses = array();
		if (!empty($expenses)) {
			$allExpenses = array_merge($allExpenses, $expenses);
		}
		if (!empty($todayExpenses)) {
			foreach ($todayExpenses as $todayExp) {
				// Check if this expense is already in the array to avoid duplicates
				$found = false;
				foreach ($allExpenses as $existing) {
					if (isset($existing->expense_id) && isset($todayExp->expense_id) && 
						$existing->expense_id == $todayExp->expense_id) {
						$found = true;
						break;
					}
				}
				if (!$found) {
					$allExpenses[] = $todayExp;
				}
			}
		}
		
		$expensesByCategory = array();
		$detailedExpenses = array();
		$groupedExpenses = array(); // New structure: Category -> Product/Entity -> Total Amount
		
		if (!empty($allExpenses)) {
			foreach ($allExpenses as $expense) {
				$categoryName = 'Uncategorized';
				
				// Try multiple fields for category name
				if (isset($expense->category_name) && !empty($expense->category_name)) {
					$categoryName = $expense->category_name;
				} elseif (isset($expense->category) && !empty($expense->category)) {
					$categoryName = $expense->category;
				} elseif (isset($expense->expense_category) && !empty($expense->expense_category)) {
					$categoryName = $expense->expense_category;
				}
				
				// Standardize category names
				if (stripos($categoryName, 'other') !== false) {
					$categoryName = 'Others';
				} elseif (stripos($categoryName, 'vegetable') !== false) {
					$categoryName = 'Vegetables';
				} elseif (stripos($categoryName, 'chicken') !== false) {
					$categoryName = 'Chicken';
				} elseif (stripos($categoryName, 'employee') !== false || 
						  stripos($categoryName, 'staff') !== false || 
						  stripos($categoryName, 'salary') !== false) {
					$categoryName = 'Employees';
				}
				
				// Get the display name based on available fields
				$displayName = 'Unknown Item';
				
				// Priority: product_name > entity_name > description > reason
				if (!empty($expense->product_name)) {
					$displayName = $expense->product_name;
				} elseif (!empty($expense->entity_name)) {
					$displayName = $expense->entity_name;
				} elseif (!empty($expense->description)) {
					$displayName = $expense->description;
				} elseif (!empty($expense->reason)) {
					$displayName = $expense->reason;
				} elseif (!empty($expense->expense_item)) {
					$displayName = $expense->expense_item;
				} elseif (!empty($expense->expense_name)) {
					$displayName = $expense->expense_name;
				}
				
				// Initialize category totals
				if (!isset($expensesByCategory[$categoryName])) {
					$expensesByCategory[$categoryName] = 0;
				}
				if (!isset($groupedExpenses[$categoryName])) {
					$groupedExpenses[$categoryName] = array();
				}
				if (!isset($groupedExpenses[$categoryName][$displayName])) {
					$groupedExpenses[$categoryName][$displayName] = array(
						'total_amount' => 0,
						'quantity' => 0,
						'records' => array()
					);
				}
				
				$amount = 0;
				$quantity = 0;
				if (isset($expense->total_amount) && !empty($expense->total_amount)) {
					$amount = floatval($expense->total_amount);
				} elseif (isset($expense->amount) && !empty($expense->amount)) {
					$amount = floatval($expense->amount);
				} elseif (isset($expense->expense_amount) && !empty($expense->expense_amount)) {
					$amount = floatval($expense->expense_amount);
				}
				
				if (isset($expense->quantity) && !empty($expense->quantity)) {
					$quantity = floatval($expense->quantity);
				} else {
					$quantity = 1; // Default quantity
				}
				
				// Add to totals
				$expensesByCategory[$categoryName] += $amount;
				$groupedExpenses[$categoryName][$displayName]['total_amount'] += $amount;
				$groupedExpenses[$categoryName][$displayName]['quantity'] += $quantity;
				$groupedExpenses[$categoryName][$displayName]['records'][] = $expense;
				
				// Keep the old detailed expenses structure for compatibility
				if (!isset($detailedExpenses[$categoryName])) {
					$detailedExpenses[$categoryName] = array();
				}
				$detailedExpenses[$categoryName][] = $expense;
			}
		}
		
		// Transform groupedExpenses to match view expectations
		$transformedGroupedExpenses = array();
		foreach ($groupedExpenses as $categoryName => $items) {
			$categoryTotal = 0;
			$transformedItems = array();
			
			foreach ($items as $itemName => $itemData) {
				// Get actual rate/price from database instead of calculating
				$actualRate = 0;
				$totalRate = 0;
				$rateCount = 0;
				
				// Find matching expenses and get their actual price
				if (!empty($expenses)) {
					foreach ($expenses as $expense) {
						$expenseItemName = '';
						
						// Determine item name based on category
						if (!empty($expense->product_name)) {
							$expenseItemName = $expense->product_name;
						} elseif (!empty($expense->entity_name)) {
							$expenseItemName = $expense->entity_name;
						} elseif (!empty($expense->description)) {
							$expenseItemName = $expense->description;
						}
						
						// Check if this expense matches current item
						if ($expenseItemName == $itemName && 
							($expense->category_name ?? 'Uncategorized') == $categoryName) {
							if (isset($expense->price) && $expense->price > 0) {
								$totalRate += $expense->price;
								$rateCount++;
							}
						}
					}
				}
				
				// Calculate average of actual rates, or fallback to calculated rate
				if ($rateCount > 0) {
					$actualRate = $totalRate / $rateCount;
				} else {
					// Fallback to calculated rate if no price found in database
					$actualRate = ($itemData['quantity'] > 0) ? ($itemData['total_amount'] / $itemData['quantity']) : 0;
				}
				
				$transformedItems[$itemName] = array(
					'quantity' => $itemData['quantity'],
					'total' => $itemData['total_amount'],
					'rate' => $actualRate
				);
				$categoryTotal += $itemData['total_amount'];
			}
			
			$transformedGroupedExpenses[$categoryName] = array(
				'items' => $transformedItems,
				'total' => $categoryTotal
			);
		}
		
		// Set expense data for view
		$data['expenses'] = $allExpenses;
		$data['expensesByCategory'] = $expensesByCategory;
		$data['detailedExpenses'] = $detailedExpenses;
		$data['groupedExpenses'] = $transformedGroupedExpenses; // New grouped structure
		
		// Debug information (only if debug mode)
		if ($debugMode) {
			$data['debugInfo'] = array(
				'filterType' => $filterType,
				'expenseCount' => count($allExpenses),
				'categoryCount' => count($expensesByCategory),
				'kitchenDataCount' => count($data['kitchenSalesData'] ?? []),
				'userId' => $saveid,
				'checkuserExists' => !empty($checkuser),
				'openDate' => $checkuser ? $checkuser->opendate : 'N/A',
				'reportStartDate' => $reportStartDate,
				'reportEndDate' => $reportEndDate
			);
		}

		// === ADDITIONAL FILTERED DATA (Payment Methods, etc.) ===
		if ($filterType != 'current' || !$checkuser) {
			// Get payment method breakdown for filtered data
			$data['paymentMethods'] = $this->_getFilteredPaymentMethods($reportStartDate, $reportEndDate);
		}
		
		// === CANCELLED ORDERS DATA (with dynamic date filtering) ===
		// Get cancelled orders based on selected filter
		$cancelledOrders = $this->_getCancelledOrdersBetweenDates($reportStartDate, $reportEndDate);
		$data['cancelledOrders'] = $cancelledOrders;
		$data['totalCancelledOrders'] = count($cancelledOrders);
		
		// Calculate cancelled orders value
		$totalCancelledValue = 0;
		$cancelledOrdersByType = array();
		$cancelledOrdersWithValues = array();
		
		if (!empty($cancelledOrders)) {
			foreach ($cancelledOrders as $cancelledOrder) {
				// Get order total value
				$orderValue = $this->_getCancelledOrderValue($cancelledOrder->order_id);
				$totalCancelledValue += $orderValue;
				
				// Add order value to the order object for display
				$cancelledOrder->order_value = $orderValue;
				$cancelledOrdersWithValues[] = $cancelledOrder;
				
				// Group by customer type
				$customerType = $cancelledOrder->customer_type ?? 'Regular';
				if (!isset($cancelledOrdersByType[$customerType])) {
					$cancelledOrdersByType[$customerType] = array(
						'count' => 0,
						'total_value' => 0
					);
				}
				$cancelledOrdersByType[$customerType]['count']++;
				$cancelledOrdersByType[$customerType]['total_value'] += $orderValue;
			}
		}
		
		// Update the data array with calculated values
		$data['cancelledOrders'] = $cancelledOrdersWithValues;
		$data['totalCancelledValue'] = $totalCancelledValue;
		$data['cancelledOrdersByType'] = $cancelledOrdersByType;
		
		// Set default values to prevent undefined variable errors
		if (!isset($data['totalCancelledOrders'])) {
			$data['totalCancelledOrders'] = count($cancelledOrdersWithValues);
		}

		// === DETAILED ORDERS BY CUSTOMER TYPE ===
		// Get detailed employee orders
		$employeeOrders = $this->_getOrdersByCustomerType('Employee', $reportStartDate, $reportEndDate);
		$data['employeeOrders'] = $employeeOrders;
		$data['totalEmployeeOrders'] = count($employeeOrders);
		
		// Calculate employee orders value
		$totalEmployeeOrdersValue = 0;
		foreach ($employeeOrders as $order) {
			$totalEmployeeOrdersValue += $order->totalamount ?? 0;
		}
		$data['totalEmployeeOrdersValue'] = $totalEmployeeOrdersValue;
		
		// Get detailed guest orders
		$guestOrders = $this->_getOrdersByCustomerType('Guest', $reportStartDate, $reportEndDate);
		$data['guestOrders'] = $guestOrders;
		$data['totalGuestOrders'] = count($guestOrders);
		
		// Calculate guest orders value
		$totalGuestOrdersValue = 0;
		foreach ($guestOrders as $order) {
			$totalGuestOrdersValue += $order->totalamount ?? 0;
		}
		$data['totalGuestOrdersValue'] = $totalGuestOrdersValue;
		
		// Get detailed charity orders
		$charityOrders = $this->_getOrdersByCustomerType('Charity', $reportStartDate, $reportEndDate);
		$data['charityOrders'] = $charityOrders;
		$data['totalCharityOrders'] = count($charityOrders);
		
		// Calculate charity orders value
		$totalCharityOrdersValue = 0;
		foreach ($charityOrders as $order) {
			$totalCharityOrdersValue += $order->totalamount ?? 0;
		}
		$data['totalCharityOrdersValue'] = $totalCharityOrdersValue;

		// Kitchen items section removed as per request

		// === SHOP SALES DATA (Dynamic from Kitchen ID 13) ===
		if ($filterType == 'current' && $checkuser) {
			// Use existing method for current register
			$shopEmployeeSales = 0;
			$shopGuestSales = 0;
			$shopCharitySales = 0;
			$shopRegularSales = 0;
			
			// Get kitchen items report for shop sales calculation
			$findkitchen = $this->order_model->getKitchens(true);
			foreach ($findkitchen as $kitchen) {
				if ($kitchen->kitchenid == 13) { // Kitchen ID 13 is Shop - Beverages
					$kitchenid = $kitchen->kitchenid;
					$findkitchenitems = $this->order_model->itemsKiReport($kitchenid, $saveid, $checkuser->opendate);
					
					if (!empty($findkitchenitems['by_type'])) {
						foreach ($findkitchenitems['by_type'] as $type) {
							$typeName = strtolower($type->type_name ?: 'regular');
							if (strpos($typeName, 'employee') !== false) {
								$shopEmployeeSales += $type->total_price;
							} elseif (strpos($typeName, 'guest') !== false) {
								$shopGuestSales += $type->total_price;
							} elseif (strpos($typeName, 'charity') !== false) {
								$shopCharitySales += $type->total_price;
							} else {
								$shopRegularSales += $type->total_price;
							}
						}
					}
					break;
				}
			}
			
			$shopSalesData = (object)array(
				'shop_regular_sales' => $shopRegularSales,
				'shop_employee_sales' => $shopEmployeeSales,
				'shop_guest_sales' => $shopGuestSales,
				'total_shop_sales' => $shopRegularSales + $shopEmployeeSales + $shopGuestSales
			);
		} else {
			// Use filtered method for other filter types
			$shopSalesData = $this->_getFilteredShopSales($reportStartDate, $reportEndDate);
		}
		
		$data['shopSalesData'] = $shopSalesData;
		
		// === OPENING BALANCE (for filtered data) ===
		if ($filterType != 'current' || !$checkuser) {
			$data['filteredOpeningBalance'] = $this->_getFilteredOpeningBalance($reportStartDate, $reportEndDate, $selectedRegister);
		}

		// === DETAILED ITEMS SOLD & KITCHEN ANALYSIS (with dynamic date filtering) ===
		if ($debugMode) $data['queryLog'][] = "========== KITCHEN DATA QUERIES ==========";
		if (($filterType == 'current' || $filterType == 'cash_register') && $checkuser) {
			// Use the SAME working methods for both current and cash register
			if ($debugMode) $data['queryLog'][] = "CALLING getDetailedItemsSoldByKitchen($saveid, '{$checkuser->opendate}')";
			$data['detailedItemsSold'] = $this->order_model->getDetailedItemsSoldByKitchen($saveid, $checkuser->opendate);
			if ($debugMode) $data['queryLog'][] = "CALLING getDetailedItemsForKitchenReport($saveid, '{$checkuser->opendate}')";
			$detailedItemsForReport = $this->order_model->getDetailedItemsForKitchenReport($saveid, $checkuser->opendate);
			
			// Try the original method first
			if ($debugMode) $data['queryLog'][] = "CALLING getKitchenAnalysis($saveid, '{$checkuser->opendate}')";
			$data['kitchenSalesData'] = $this->order_model->getKitchenAnalysis($saveid, $checkuser->opendate);
			log_message('debug', 'Using current register kitchen methods for filter: ' . $filterType);
			
			// If that fails, try our simplified version as backup
			if (empty($data['kitchenSalesData'])) {
				if ($debugMode) $data['queryLog'][] = "getKitchenAnalysis returned empty, trying _getFilteredKitchenSales";
				$data['kitchenSalesData'] = $this->_getFilteredKitchenSales($checkuser->opendate, date('Y-m-d H:i:s'));
			}
		} else {
			// For filtered data, get kitchen sales data
			log_message('debug', 'Getting filtered kitchen data for date range: ' . $reportStartDate . ' to ' . $reportEndDate);
			$data['kitchenSalesData'] = $this->_getFilteredKitchenSales($reportStartDate, $reportEndDate);
			log_message('debug', 'Kitchen data retrieved: ' . count($data['kitchenSalesData']) . ' records');
			$data['detailedItemsSold'] = array(); // Complex query - will implement if needed
			$detailedItemsForReport = array();
		}
		
		// Calculate total kitchen sales
		$totalKitchenSales = 0;
		foreach ($data['kitchenSalesData'] as $kitchen) {
			$totalKitchenSales += $kitchen->total_sales;
		}
		$data['totalKitchenSales'] = $totalKitchenSales;
		
		// === SHOP EXPENSES (Dynamic from expensesByCategory) ===
		$shopExpenses = 0;
		if (!empty($expensesByCategory)) {
			foreach ($expensesByCategory as $categoryName => $amount) {
				if (strtolower($categoryName) == 'shop' || strpos(strtolower($categoryName), 'shop') !== false) {
					$shopExpenses += $amount;
				}
			}
		}
		$data['shopExpenses'] = $shopExpenses;

		// === SUMMARY CALCULATIONS ===
		$totalSales = 0;
		if (isset($data['totalamount'][0]->totalamount)) {
			$totalSales = (float)$data['totalamount'][0]->totalamount;
		}
		
		$totalExpenses = $data['totalexpenses'] ?: 0;
		// Ensure totalExpenses is a number
		if (is_object($totalExpenses)) {
			$totalExpenses = isset($totalExpenses->total) ? (float)$totalExpenses->total : 0;
		} else {
			$totalExpenses = (float)$totalExpenses;
		}
		
		$data['netProfit'] = $totalSales - $totalExpenses;
		$data['reportDate'] = date('Y-m-d');
		$data['reportTime'] = date('H:i:s');

		// === ITEM SALES DATA ===
		if ($debugMode) $data['queryLog'][] = "========== ITEM SALES DATA QUERIES ==========";
		if (($filterType == 'current' || $filterType == 'cash_register') && $checkuser) {
			// Use the SAME logic as sales data - register open/close times
			$endDate = (isset($checkuser->closedate) && $checkuser->closedate != "1970-01-01 00:00:00") ? $checkuser->closedate : null;
			if ($debugMode) $data['queryLog'][] = "CALLING _getItemSalesData('{$checkuser->opendate}', " . ($endDate ?: 'NULL') . ")";
			$data['itemSalesData'] = $this->_getItemSalesData($checkuser->opendate, $endDate);
		} else {
			// Use filtered data collection for other filter types
			if ($debugMode) $data['queryLog'][] = "CALLING _getItemSalesData('$reportStartDate', '$reportEndDate')";
			$data['itemSalesData'] = $this->_getItemSalesData($reportStartDate, $reportEndDate);
		}

		// Add any additional queries that were logged globally (only in debug mode)
		if ($debugMode && isset($GLOBALS['queryLog']) && is_array($GLOBALS['queryLog'])) {
			$data['queryLog'] = array_merge($data['queryLog'], $GLOBALS['queryLog']);
		}
		
		// Pass debug flag to view
		$data['debugMode'] = $debugMode;

		$this->load->view('dailycomprehensivereport', $data);
	}

	/**
	 * Get filtered sales data for date range
	 */
	private function _getFilteredSalesData($startDate, $endDate)
	{
		try {
			$this->db->select('SUM(totalamount) as totalamount, COUNT(*) as total_orders');
			$this->db->from('customer_order');
			$this->db->where('order_status', 4); // Completed orders
			$this->db->where("CONCAT(order_date, ' ', IFNULL(order_time, '00:00:00')) BETWEEN '$startDate' AND '$endDate'");
			$query = $this->db->get();
			
			if ($query === false) {
				log_message('error', 'Failed to get filtered sales data: ' . $this->db->last_query());
				return array();
			}
			
			return $query->result();
		} catch (Exception $e) {
			log_message('error', 'Exception in _getFilteredSalesData: ' . $e->getMessage());
			return array();
		}
	}
	
	/**
	 * Get filtered customer type wise data
	 */
	private function _getFilteredCustomerTypeWise($startDate, $endDate)
	{
		try {
			$this->db->select('ct.customer_type, SUM(co.totalamount) as total_amount, COUNT(*) as total_orders');
			$this->db->from('customer_order co');
			$this->db->join('customer_type ct', 'co.cutomertype = ct.customer_type_id', 'left');
			$this->db->where('co.order_status', 4); // Completed orders
			$this->db->where("CONCAT(co.order_date, ' ', IFNULL(co.order_time, '00:00:00')) BETWEEN '$startDate' AND '$endDate'");
			$this->db->group_by('co.cutomertype');
			$query = $this->db->get();
			
			if ($query === false) {
				log_message('error', 'Failed to get filtered customer type wise data: ' . $this->db->last_query());
				return array();
			}
			
			return $query->result();
		} catch (Exception $e) {
			log_message('error', 'Exception in _getFilteredCustomerTypeWise: ' . $e->getMessage());
			return array();
		}
	}

	/**
	 * Get detailed orders by customer type
	 */
	private function _getOrdersByCustomerType($customerType, $startDate, $endDate)
	{
		try {
			$this->db->select('co.*, c.customer_name, c.customer_phone, c.customer_email, ct.customer_type');
			$this->db->from('customer_order co');
			$this->db->join('customer_info c', 'co.customer_id = c.customer_id', 'left');
			$this->db->join('customer_type ct', 'co.cutomertype = ct.customer_type_id', 'left');
			$this->db->where('co.order_status', 4); // Completed orders
			$this->db->where('ct.customer_type', $customerType);
			$this->db->where("CONCAT(co.order_date, ' ', IFNULL(co.order_time, '00:00:00')) BETWEEN '$startDate' AND '$endDate'");
			$this->db->order_by('co.order_time', 'DESC');
			$query = $this->db->get();
			
			if ($query === false) {
				log_message('error', 'Failed to get orders by customer type: ' . $this->db->last_query());
				return array();
			}
			
			$orders = $query->result();
			
			// Get order items for each order
			foreach ($orders as $order) {
				$order->order_items = $this->_getOrderItems($order->order_id);
			}
			
			return $orders;
		} catch (Exception $e) {
			log_message('error', 'Exception in _getOrdersByCustomerType: ' . $e->getMessage());
			return array();
		}
	}

	/**
	 * Get order items for a specific order
	 */
	private function _getOrderItems($orderId)
	{
		try {
			$this->db->select('oi.*, f.ProductName, f.productvat, v.variantName, v.price as variant_price');
			$this->db->from('order_menu oi');
			$this->db->join('item_foods f', 'oi.menu_id = f.ProductsID', 'left');
			$this->db->join('variant v', 'oi.varientid = v.variantid', 'left');
			$this->db->where('oi.order_id', $orderId);
			$this->db->order_by('oi.row_id', 'ASC');
			$query = $this->db->get();
			
			if ($query === false) {
				log_message('error', 'Failed to get order items: ' . $this->db->last_query());
				return array();
			}
			
			return $query->result();
		} catch (Exception $e) {
			log_message('error', 'Exception in _getOrderItems: ' . $e->getMessage());
			return array();
		}
	}
	
	/**
	 * Get filtered expenses data
	 */
	private function _getFilteredExpenses($startDate, $endDate)
	{
		try {
			$this->db->select('SUM(amount) as total_expenses');
			$this->db->from('expense');
			$this->db->where("date BETWEEN DATE('$startDate') AND DATE('$endDate')");
			$query = $this->db->get();
			
			if ($query === false) {
				log_message('error', 'Failed to get filtered expenses: ' . $this->db->last_query());
				return 0;
			}
			
			$result = $query->row();
			return $result ? $result->total_expenses : 0;
		} catch (Exception $e) {
			log_message('error', 'Exception in _getFilteredExpenses: ' . $e->getMessage());
			return 0;
		}
	}
	
	/**
	 * Get filtered expenses by category
	 */
	private function _getFilteredExpensesByCategory($startDate, $endDate)
	{
		try {
			log_message('debug', '=== EXPENSE QUERY DEBUG ===');
			log_message('debug', 'Start Date: ' . $startDate);
			log_message('debug', 'End Date: ' . $endDate);
			
			// Get expenses with proper date filtering including product names and entity names
			$this->db->select('expenses.*, categories.category_name, entities.entity_name, products.product_name, products.product_id, products.unit');
			$this->db->from('expenses');
			$this->db->join('categories', 'expenses.category_id = categories.category_id', 'left');
			$this->db->join('entities', 'expenses.entity_id = entities.entity_id', 'left');
			$this->db->join('products', 'expenses.product_id = products.product_id', 'left');
			$this->db->where('expenses.status', 1);
			
			$where = "expenses.created_at Between '$startDate' AND '$endDate'";
			$this->db->where($where);
			
			$this->db->order_by('expenses.expense_id', 'DESC');
			
			// Execute query
			$query = $this->db->get();
			
			// Add the actual SQL query to the log that will be displayed on view
			$sqlQuery = $this->db->last_query();
			log_message('debug', 'ACTUAL SQL QUERY: ' . $sqlQuery);
			// Store in a global variable so we can access it in the view
			if (!isset($GLOBALS['queryLog'])) $GLOBALS['queryLog'] = array();
			$GLOBALS['queryLog'][] = "EXPENSE SQL QUERY: " . $sqlQuery;
			
			if ($query === false) {
				log_message('error', 'Expense query failed');
				return array();
			}
			
			$expenses = $query->result();
			log_message('debug', 'Found ' . count($expenses) . ' expenses in date range');
			echo "<!-- FOUND " . count($expenses) . " EXPENSES IN RANGE -->\n";
			
			// Add display_name to each expense record based on category
			foreach ($expenses as $expense) {
				$categoryName = $expense->category_name ?? '';
				if ($categoryName == 'Shop' || $categoryName == 'Vegetables') {
					$expense->display_name = $expense->product_name ?: 'N/A';
				} else {
					$expense->display_name = $expense->entity_name ?: 'N/A';
				}
			}
			
			// Show sample of data found for debugging
			if (!empty($expenses)) {
				$sampleDates = array_slice(array_map(function($exp) { return $exp->expense_date; }, $expenses), 0, 5);
				$sampleNames = array_slice(array_map(function($exp) { return $exp->display_name; }, $expenses), 0, 5);
				log_message('debug', 'Sample expense dates: ' . implode(', ', $sampleDates));
				log_message('debug', 'Sample display names: ' . implode(', ', $sampleNames));
				// Store in global for view display
				if (!isset($GLOBALS['queryLog'])) $GLOBALS['queryLog'] = array();
				$GLOBALS['queryLog'][] = "SAMPLE EXPENSE DATES: " . implode(', ', $sampleDates);
				$GLOBALS['queryLog'][] = "SAMPLE DISPLAY NAMES: " . implode(', ', $sampleNames);
			}
			
			return $expenses;
		} catch (Exception $e) {
			log_message('error', 'Exception in _getFilteredExpensesByCategory: ' . $e->getMessage());
			return array();
		}
	}
	
	/**
	 * Get filtered detailed customer type sales
	 */
	private function _getFilteredDetailedCustomerTypeSales($startDate, $endDate)
	{
		try {
			$this->db->select('
				ct.customer_type,
				ct.customer_type_id,
				SUM(co.totalamount) as total_amount,
				COUNT(co.order_id) as total_orders,
				AVG(co.totalamount) as avg_order_value
			');
			$this->db->from('customer_order co');
			$this->db->join('customer_type ct', 'co.cutomertype = ct.customer_type_id', 'left');
			$this->db->where('co.order_status', 4); // Completed orders
			$this->db->where("CONCAT(co.order_date, ' ', IFNULL(co.order_time, '00:00:00')) BETWEEN '$startDate' AND '$endDate'");
			$this->db->group_by('co.cutomertype, ct.customer_type');
			$this->db->order_by('total_amount', 'DESC');
			$query = $this->db->get();
			
			if ($query === false) {
				return array();
			}
			
			return $query->result();
		} catch (Exception $e) {
			log_message('error', 'Exception in _getFilteredDetailedCustomerTypeSales: ' . $e->getMessage());
			return array();
		}
	}
	
	/**
	 * Get filtered kitchen sales data with enhanced structure
	 */
	private function _getFilteredKitchenSales($startDate, $endDate)
	{
		try {
			log_message('debug', '=== KITCHEN QUERY DEBUG ===');
			log_message('debug', 'Start Date: ' . $startDate);
			log_message('debug', 'End Date: ' . $endDate);
			
			// First, let's check if we have any orders in the date range
			$this->db->select('COUNT(*) as total');
			$this->db->from('customer_order');
			$this->db->where("order_date BETWEEN '$startDate' AND '$endDate'");
			$this->db->where('order_status', 4);
			$orderCheck = $this->db->get();
			$totalOrders = $orderCheck ? $orderCheck->row()->total : 0;
			log_message('debug', 'Orders in date range: ' . $totalOrders);
			echo "<!-- ORDERS IN RANGE: " . $totalOrders . " -->\n";
			
			// Try both possible kitchen table names
			$kitchenTableName = 'kitchen';
			if (!$this->db->table_exists('kitchen') && $this->db->table_exists('tbl_kitchen')) {
				$kitchenTableName = 'tbl_kitchen';
			}
			log_message('debug', 'Using kitchen table: ' . $kitchenTableName);
			
			// Get basic kitchen sales data with simplified query
			$this->db->select('
				k.kitchen_name,
				k.kitchenid,
				COUNT(DISTINCT co.order_id) as total_orders,
				COALESCE(SUM(om.price * om.menuqty), 0) as total_sales,
				COALESCE(SUM(om.menuqty), 0) as total_items_count
			');
			$this->db->from('customer_order co');
			$this->db->join('order_menu om', 'co.order_id = om.order_id', 'inner');
			$this->db->join('item_foods if', 'om.menu_id = if.ProductsID', 'inner');
			$this->db->join($kitchenTableName . ' k', 'if.kitchenid = k.kitchenid', 'inner');
			$this->db->where('co.order_status', 4);
			$this->db->where("co.order_date BETWEEN DATE('$startDate') AND DATE('$endDate')");
			$this->db->group_by('k.kitchenid, k.kitchen_name');
			$this->db->order_by('total_sales', 'DESC');
			
			$query = $this->db->get();
			
			// ALWAYS log the actual SQL query so you can check it
			$sqlQuery = $this->db->last_query();
			log_message('debug', 'ACTUAL KITCHEN SQL QUERY: ' . $sqlQuery);
			echo "<!-- KITCHEN SQL QUERY: " . $sqlQuery . " -->\n";
			
			if ($query === false) {
				log_message('error', 'Kitchen query failed');
				return array();
			}
			
			$kitchens = $query->result();
			log_message('debug', 'Found ' . count($kitchens) . ' kitchens with sales data');
			echo "<!-- FOUND " . count($kitchens) . " KITCHENS WITH SALES -->\n";
			
			// Enhance each kitchen with additional data expected by the view
			foreach ($kitchens as &$kitchen) {
				// Add customer type breakdown (simplified)
				$kitchen->customer_types = array(
					'employee' => array('qty' => 0, 'amount' => 0),
					'guest' => array('qty' => 0, 'amount' => 0),
					'charity' => array('qty' => 0, 'amount' => 0),
					'regular' => array('qty' => intval($kitchen->total_items_count), 'amount' => floatval($kitchen->total_sales))
				);
				
				// Add top items sold (simplified - we'll show generic data)
				$kitchen->items_sold = array(
					(object)array('product_name' => 'Items from ' . $kitchen->kitchen_name, 'quantity_sold' => $kitchen->total_items_count)
				);
				
				// Add cost analysis (simplified)
				$kitchen->total_cost = floatval($kitchen->total_sales) * 0.6; // Assume 60% cost ratio
				$kitchen->net_profit = floatval($kitchen->total_sales) - $kitchen->total_cost;
				$kitchen->profit_margin = $kitchen->total_sales > 0 ? ($kitchen->net_profit / $kitchen->total_sales * 100) : 0;
				
				// Add employee and product counts (default values)
				$kitchen->employee_count = 2; // Default
				$kitchen->product_count = 10; // Default
			}
			
			return $kitchens;
		} catch (Exception $e) {
			log_message('error', 'Exception in _getFilteredKitchenSales: ' . $e->getMessage());
			return array();
		}
	}
	
	/**
	 * Get filtered payment method data
	 */
	private function _getFilteredPaymentMethods($startDate, $endDate)
	{
		try {
			$this->db->select('
				pm.payment_method,
				pm.payment_method_id,
				SUM(bo.amount) as total_amount,
				COUNT(bo.bill_id) as total_transactions
			');
			$this->db->from('bill_card_payment bo');
			$this->db->join('payment_method pm', 'bo.payment_method_id = pm.payment_method_id', 'inner');
			$this->db->join('customer_order co', 'bo.order_id = co.order_id', 'inner');
			$this->db->where('co.order_status', 4);
			$this->db->where("CONCAT(co.order_date, ' ', IFNULL(co.order_time, '00:00:00')) BETWEEN '$startDate' AND '$endDate'");
			$this->db->group_by('pm.payment_method_id, pm.payment_method');
			$this->db->order_by('total_amount', 'DESC');
			$query = $this->db->get();
			
			if ($query === false) {
				return array();
			}
			
			return $query->result();
		} catch (Exception $e) {
			log_message('error', 'Exception in _getFilteredPaymentMethods: ' . $e->getMessage());
			return array();
		}
	}
	
	/**
	 * Get filtered shop sales data (Kitchen ID 13)
	 */
	private function _getFilteredShopSales($startDate, $endDate)
	{
		try {
			$this->db->select('
				ct.customer_type,
				ct.customer_type_id,
				SUM(om.price * om.menuqty) as total_price,
				COUNT(DISTINCT co.order_id) as total_orders
			');
			$this->db->from('customer_order co');
			$this->db->join('order_menu om', 'co.order_id = om.order_id', 'inner');
			$this->db->join('item_foods if', 'om.menu_id = if.ProductsID', 'inner');
			$this->db->join('customer_type ct', 'co.cutomertype = ct.customer_type_id', 'left');
			$this->db->where('co.order_status', 4);
			$this->db->where('if.kitchenid', 13); // Shop - Beverages
			$this->db->where("CONCAT(co.order_date, ' ', IFNULL(co.order_time, '00:00:00')) BETWEEN '$startDate' AND '$endDate'");
			$this->db->group_by('co.cutomertype, ct.customer_type');
			$query = $this->db->get();
			
			if ($query === false) {
				return (object)array(
					'shop_regular_sales' => 0,
					'shop_employee_sales' => 0,
					'shop_guest_sales' => 0,
					'total_shop_sales' => 0
				);
			}
			
			$results = $query->result();
			$shopRegularSales = 0;
			$shopEmployeeSales = 0;
			$shopGuestSales = 0;
			$shopCharitySales = 0;
			
			foreach ($results as $result) {
				$typeName = strtolower($result->customer_type ?: 'regular');
				if (strpos($typeName, 'employee') !== false) {
					$shopEmployeeSales += $result->total_price;
				} elseif (strpos($typeName, 'guest') !== false) {
					$shopGuestSales += $result->total_price;
				} elseif (strpos($typeName, 'charity') !== false) {
					$shopCharitySales += $result->total_price;
				} else {
					$shopRegularSales += $result->total_price;
				}
			}
			
			return (object)array(
				'shop_regular_sales' => $shopRegularSales,
				'shop_employee_sales' => $shopEmployeeSales,
				'shop_guest_sales' => $shopGuestSales,
				'total_shop_sales' => $shopRegularSales + $shopEmployeeSales + $shopGuestSales + $shopCharitySales
			);
		} catch (Exception $e) {
			log_message('error', 'Exception in _getFilteredShopSales: ' . $e->getMessage());
			return (object)array(
				'shop_regular_sales' => 0,
				'shop_employee_sales' => 0,
				'shop_guest_sales' => 0,
				'total_shop_sales' => 0
			);
		}
	}
	
	/**
	 * Get opening balance for filtered register
	 */
	private function _getFilteredOpeningBalance($startDate, $endDate, $selectedRegister)
	{
		try {
			if ($selectedRegister) {
				return floatval($selectedRegister->opening_balance ?? 0);
			}
			
			// For date ranges, get the earliest register's opening balance
			$this->db->select('opening_balance');
			$this->db->from('tbl_cashregister');
			$this->db->where("opendate >= '$startDate'");
			$this->db->order_by('opendate', 'ASC');
			$this->db->limit(1);
			$query = $this->db->get();
			
			if ($query && $query->num_rows() > 0) {
				$result = $query->row();
				return floatval($result->opening_balance ?? 0);
			}
			
			return 0;
		} catch (Exception $e) {
			log_message('error', 'Exception in _getFilteredOpeningBalance: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Get cancelled orders between two dates
	 * @param string $startDate
	 * @param string $endDate
	 * @return array
	 */
	private function _getCancelledOrdersBetweenDates($startDate, $endDate)
	{
		try {
			// Validate input dates
			if (empty($startDate) || empty($endDate)) {
				log_message('error', 'Invalid date parameters in _getCancelledOrdersBetweenDates');
				return array();
			}
			
			// Use full datetime for comparison to check between cash register opened time and current time
			$startDateTime = date('Y-m-d H:i:s', strtotime($startDate));
			$endDateTime = date('Y-m-d H:i:s', strtotime($endDate));
			
			// Validate converted dates
			if ($startDateTime === false || $endDateTime === false) {
				log_message('error', 'Failed to convert dates in _getCancelledOrdersBetweenDates: ' . $startDate . ' - ' . $endDate);
				return array();
			}
			
			// Query for cancelled orders (order_status = 5) between the datetime range
			$this->db->select('customer_order.*, customer_info.customer_name, customer_type.customer_type, employee_history.first_name, employee_history.last_name, rest_table.tablename, customer_order.anyreason as cancel_reason');
			$this->db->from('customer_order');
			$this->db->join('customer_info', 'customer_order.customer_id = customer_info.customer_id', 'left');
			$this->db->join('customer_type', 'customer_order.cutomertype = customer_type.customer_type_id', 'left');
			$this->db->join('employee_history', 'customer_order.waiter_id = employee_history.emp_his_id', 'left');
			$this->db->join('rest_table', 'customer_order.table_no = rest_table.tableid', 'left');
			$this->db->where('customer_order.order_status', 5); // Cancelled status
			// Check orders created within the cash register time period using CONCAT for datetime comparison
			$this->db->where("CONCAT(customer_order.order_date, ' ', IFNULL(customer_order.order_time, '00:00:00')) BETWEEN '$startDateTime' AND '$endDateTime'");
			$this->db->order_by('customer_order.order_date', 'DESC');
			$this->db->order_by('customer_order.order_time', 'DESC');
			
			$query = $this->db->get();
			
			// Check if query was successful
			if ($query === false) {
				// Log database error for debugging
				log_message('error', 'Failed to get cancelled orders between dates: ' . $startDateTime . ' - ' . $endDateTime);
				return array();
			}
			
			return $query->result();
		} catch (Exception $e) {
			// Log exception for debugging
			log_message('error', 'Exception in _getCancelledOrdersBetweenDates: ' . $e->getMessage());
			return array();
		}
	}

	/**
	 * Get the total value of a cancelled order
	 * @param int $orderId
	 * @return float
	 */
	private function _getCancelledOrderValue($orderId)
	{
		try {
			// Get order menu items to calculate total value
			$this->db->select('SUM(order_menu.price * order_menu.menuqty) as total_value');
			$this->db->from('order_menu');
			$this->db->where('order_menu.order_id', $orderId);
			$query = $this->db->get();
			
			// Check if query was successful
			if ($query === false) {
				// Log database error for debugging
				log_message('error', 'Failed to get cancelled order value for order ID: ' . $orderId);
				return 0;
			}
			
			if ($query->num_rows() > 0) {
				$result = $query->row();
				return $result->total_value ?: 0;
			}
			
			return 0;
		} catch (Exception $e) {
			// Log exception for debugging
			log_message('error', 'Exception in _getCancelledOrderValue: ' . $e->getMessage());
			return 0;
		}
	}




	public function closecashregister()
	{
		$this->form_validation->set_rules('totalamount', display('amount'), 'required');
		$saveid = $this->session->userdata('id');
		$counter = $this->input->post('counter');
		$openingamount = $this->input->post('totalamount');
		$cashclose = $this->input->post('registerid');
		if ($this->form_validation->run() === true) {
			$postData = array(
				'id' 				=> $cashclose,
				'closing_balance' 	=> $this->input->post('totalamount', true),
				'closedate' 	    => date('Y-m-d H:i:s'),
				'status' 	        => 1,
				'closing_note' 	    => $this->input->post('closingnote', true),
			);
			if ($this->order_model->closeresister($postData)) {
				echo 1;
			} else {
				echo 0;
			}
		}
	}


	public function closecashregisterprint()
	{
		$this->form_validation->set_rules('totalamount', display('amount'), 'required');
		$saveid = $this->session->userdata('id');
		$counter = $this->input->post('counter');
		$openingamount = $this->input->post('totalamount');
		$cashclose = $this->input->post('registerid');
		if ($this->form_validation->run() === true) {
			$postData = array(
				'id' 				=> $cashclose,
				'closing_balance' 	=> $this->input->post('totalamount', true),
				'closedate' 	    => date('Y-m-d H:i:s'),
				'status' 	        => 1,
				'closing_note' 	    => $this->input->post('closingnote', true),
			);
			if ($this->order_model->closeresister($postData)) {
				$checkuser = $this->db->select('*')->from('tbl_cashregister')->where('userid', $saveid)->where('status', 1)->order_by('id', 'DESC')->get()->row();
				$iteminfo = $this->order_model->summeryiteminfo($saveid, $checkuser->opendate, $checkuser->closedate);

				$i = 0;
				$order_ids = array('');
				foreach ($iteminfo as $orderid) {
					$order_ids[$i] = $orderid->order_id;
					$i++;
				}
				$addonsitem  = $this->order_model->closingaddons($order_ids);
				$k = 0;
				$test = array();
				foreach ($addonsitem as $addonsall) {
					$addons = explode(",", $addonsall->add_on_id);
					$addonsqty = explode(",", $addonsall->addonsqty);
					$x = 0;
					foreach ($addons as $addonsid) {
						$test[$k][$addonsid] = $addonsqty[$x];
						$x++;
					}
					$k++;
				}

				$final = array();
				array_walk_recursive($test, function ($item, $key) use (&$final) {
					$final[$key] = isset($final[$key]) ?  $item + $final[$key] : $item;
				});
				$totalprice = 0;
				foreach ($final as $key => $item) {
					$addonsinfo = $this->db->select("*")->from('add_ons')->where('add_on_id', $key)->get()->row();
					$totalprice = $totalprice + ($addonsinfo->price * $item);
				}
				$data['addonsprice'] = $totalprice;
				$data['registerinfo'] = $checkuser;
				//$data['invsetting'] =$this->db->select('*')->from('tbl_invoicesetting')->where('invstid',1)->get()->row();
				$data['billinfo'] = $this->order_model->billsummery($saveid, $checkuser->opendate, $checkuser->closedate);
				$data['totalamount'] = $this->order_model->collectcashsummery($saveid, $checkuser->opendate, $checkuser->closedate);
				$data['totalchange'] = $this->order_model->changecashsummery($saveid, $checkuser->opendate, $checkuser->closedate);
				$data['itemsummery'] = $this->order_model->closingiteminfo($order_ids);
				echo $viewprint = $this->load->view('cashclosingsummery', $data, true);
			} else {
				echo 0;
			}
		}
	}

	public function closecashregisterprinttest()
	{
		$saveid = $this->session->userdata('id');
		//$data['invsetting'] =$this->db->select('*')->from('tbl_invoicesetting')->where('invstid',1)->get()->row();
		$checkuser = $this->db->select('*')->from('tbl_cashregister')->where('userid', $saveid)->where('status', 1)->order_by('id', 'DESC')->get()->row();
		$iteminfo = $this->order_model->summeryiteminfo($saveid, $checkuser->opendate, $checkuser->closedate);
		$i = 0;
		$order_ids = array('');
		foreach ($iteminfo as $orderid) {
			$order_ids[$i] = $orderid->order_id;
			$i++;
		}
		$addonsitem  = $this->order_model->closingaddons($order_ids);
		$k = 0;
		$test = array();
		foreach ($addonsitem as $addonsall) {
			$addons = explode(",", $addonsall->add_on_id);
			$addonsqty = explode(",", $addonsall->addonsqty);
			$x = 0;
			foreach ($addons as $addonsid) {
				$test[$k][$addonsid] = $addonsqty[$x];
				$x++;
			}
			$k++;
		}

		$final = array();
		array_walk_recursive($test, function ($item, $key) use (&$final) {
			$final[$key] = isset($final[$key]) ?  $item + $final[$key] : $item;
		});
		$totalprice = 0;
		foreach ($final as $key => $item) {
			$addonsinfo = $this->db->select("*")->from('add_ons')->where('add_on_id', $key)->get()->row();
			$totalprice = $totalprice + ($addonsinfo->price * $item);
		}
		$data['addonsprice'] = $totalprice;
		$data['registerinfo'] = $checkuser;
		$data['billinfo'] = $this->order_model->billsummery($saveid, $checkuser->opendate, $checkuser->closedate);
		$data['totalamount'] = $this->order_model->collectcashsummery($saveid, $checkuser->opendate, $checkuser->closedate);
		$data['totalchange'] = $this->order_model->changecashsummery($saveid, $checkuser->opendate, $checkuser->closedate);
		$data['itemsummery'] = $this->order_model->closingiteminfo($order_ids);
		$this->load->view('cashclosingsummery', $data);
	}

	/**
	 * Get item sales data with customer type breakdown
	 */
	private function _getItemSalesData($startDate, $endDate = null)
	{
		try {
			// If no end date provided, use current time (for active cash counter)
			$endDate = $endDate ?: date('Y-m-d H:i:s');
			
			$this->db->select("
				item_foods.ProductName as item_name,
				variant.variantName as variant_name,
				item_foods.ProductsID as product_id,
				variant.variantid as variant_id,
				variant.price as item_price,
				
				-- Regular customers (cutomertype NOT IN (5,6,7) or NULL)
				SUM(CASE 
					WHEN (customer_order.cutomertype NOT IN (5,6,7) OR customer_order.cutomertype IS NULL) 
					THEN order_menu.menuqty 
					ELSE 0 
				END) as regular_qty,
				SUM(CASE 
					WHEN (customer_order.cutomertype NOT IN (5,6,7) OR customer_order.cutomertype IS NULL) 
					THEN (order_menu.menuqty * order_menu.price) 
					ELSE 0 
				END) as regular_revenue,
				
				-- Employee customers (cutomertype = 5)
				SUM(CASE 
					WHEN customer_order.cutomertype = 5 
					THEN order_menu.menuqty 
					ELSE 0 
				END) as employee_qty,
				SUM(CASE 
					WHEN customer_order.cutomertype = 5 
					THEN (order_menu.menuqty * order_menu.price) 
					ELSE 0 
				END) as employee_revenue,
				
				-- Guest customers (cutomertype = 6)
				SUM(CASE 
					WHEN customer_order.cutomertype = 6 
					THEN order_menu.menuqty 
					ELSE 0 
				END) as guest_qty,
				SUM(CASE 
					WHEN customer_order.cutomertype = 6 
					THEN (order_menu.menuqty * order_menu.price) 
					ELSE 0 
				END) as guest_revenue,
				
				-- Charity customers (cutomertype = 7)
				SUM(CASE 
					WHEN customer_order.cutomertype = 7 
					THEN order_menu.menuqty 
					ELSE 0 
				END) as charity_qty,
				SUM(CASE 
					WHEN customer_order.cutomertype = 7 
					THEN (order_menu.menuqty * order_menu.price) 
					ELSE 0 
				END) as charity_revenue,
				
				-- Total quantities and revenue
				SUM(order_menu.menuqty) as total_qty,
				SUM(order_menu.menuqty * order_menu.price) as total_revenue
			");
			
			$this->db->from('order_menu');
			$this->db->join('customer_order', 'customer_order.order_id = order_menu.order_id', 'inner');
			$this->db->join('item_foods', 'item_foods.ProductsID = order_menu.menu_id', 'left');
			$this->db->join('variant', 'variant.variantid = order_menu.varientid', 'left');
			
			// Date filtering
			$this->db->where("CONCAT(customer_order.order_date, ' ', IFNULL(customer_order.order_time, '00:00:00')) BETWEEN '$startDate' AND '$endDate'");
			
			// Only completed orders
			$this->db->where('customer_order.order_status', 4);
			
			// Group by item and variant
			$this->db->group_by('order_menu.menu_id, order_menu.varientid');
			
			// Order by total quantity sold (highest first)
			$this->db->order_by('total_qty', 'DESC');
			
			$query = $this->db->get();
			
			// Store query for display in view (only if debug mode)
			if (isset($GLOBALS['debugMode']) && $GLOBALS['debugMode']) {
				if (!isset($GLOBALS['queryLog'])) $GLOBALS['queryLog'] = array();
				$GLOBALS['queryLog'][] = "ITEM SALES QUERY: " . $this->db->last_query();
			}
			
			if ($query === false) {
				log_message('error', 'Failed to get item sales data: ' . $this->db->last_query());
				return array();
			}
			
			$results = $query->result();
			
			// Debug log the results count
			if (isset($GLOBALS['debugMode']) && $GLOBALS['debugMode']) {
				$GLOBALS['queryLog'][] = "ITEM SALES RESULTS: Found " . count($results) . " different items";
			}
			
			return $results;
			
		} catch (Exception $e) {
			log_message('error', 'Exception in _getItemSalesData: ' . $e->getMessage());
			if (isset($GLOBALS['debugMode']) && $GLOBALS['debugMode']) {
				if (!isset($GLOBALS['queryLog'])) $GLOBALS['queryLog'] = array();
				$GLOBALS['queryLog'][] = "ITEM SALES ERROR: " . $e->getMessage();
			}
			return array();
		}
	}

	private function taxchecking()
	{
		$taxinfos = '';
		if ($this->db->table_exists('tbl_tax')) {
			$taxsetting = $this->db->select('*')->from('tbl_tax')->get()->row();
		}
		if (!empty($taxsetting)) {
			if ($taxsetting->tax == 1) {
				$taxinfos = $this->db->select('*')->from('tax_settings')->get()->result_array();
			}
		}

		return $taxinfos;
	}
	public function soundsetting()
	{
		$this->permission->method('ordermanage', 'read')->redirect();
		$data['title'] = display('sound_setting');
		$data['soundsetting'] = $this->order_model->read('*', 'tbl_soundsetting', array('soundid' => 1));
		$data['module'] = "ordermanage";
		$data['page']   = "soundsetting";
		echo Modules::run('template/layout', $data);
	}
	public function addsound()
	{
		$soundfile = $this->fileupload->do_upload(
			'assets/',
			'notifysound'
		);
		if ($soundfile === false) {
			$this->session->set_flashdata('exception', "Invalid Sound format.Only .mp3 supported");
			redirect('ordermanage/order/soundsetting');
		}
		$data['soundsetting'] = (object)$postData = array(
			'soundid'          => $this->input->post('id'),
			'nofitysound' 	   => (!empty($soundfile) ? $soundfile : $this->input->post('old_notifysound', true))
		);
		if ($this->order_model->soundcreate($postData)) {
			#set success message
			$this->session->set_flashdata('message', display('save_successfully'));
		} else {
			#set exception message
			$this->session->set_flashdata('exception', display('please_try_again'));
		}
		redirect('ordermanage/order/soundsetting');
	}

	public function possettingjs()
	{
		$data['possetting'] = $this->order_model->read('*', 'tbl_posetting', array('possettingid' => 1));
		header('Content-Type: text/javascript');
		echo ('window.possetting = ' . json_encode($data['possetting']) . ';');
		exit();
	}
	public function quickorderjs()
	{
		$data['quickordersetting'] = $this->order_model->read('*', 'tbl_quickordersetting', array('quickordid' => 1));
		header('Content-Type: text/javascript');
		echo ('window.quickordersetting = ' . json_encode($data['quickordersetting']) . ';');
		exit();
	}
	public function basicjs()
	{
		$soundinfo = $this->order_model->read('*', 'tbl_soundsetting', array('soundid' => 1));
		$possetting = $this->order_model->read('*', 'tbl_posetting', array('possettingid' => 1));
		$settinginfo = $this->order_model->settinginfo();
		$openingtimerv = strtotime($settinginfo->reservation_open);
		$closetimerv = strtotime($settinginfo->reservation_close);
		$compareretime = strtotime(date("H:i:s A"));
		if (($compareretime >= $openingtimerv) && ($compareretime < $closetimerv)) {
			$reservationopen = 1;
		} else {
			$reservationopen = 0;
		}

		$currency = $this->order_model->currencysetting($settinginfo->currency);
		$allbasicinfo = array(
			'segment1' => $this->uri->segment(1),
			'segment2' => $this->uri->segment(2),
			'segment3' => $this->uri->segment(3),
			'segment4' => $this->uri->segment(4),
			'segment5' => $this->uri->segment(5),
			'baseurl' => base_url(),
			'curr_icon' => $currency->curr_icon,
			'position' => $currency->position,
			'discount_type' => $settinginfo->discount_type,
			'discountrate' => $settinginfo->discountrate,
			'service_chargeType' => $settinginfo->service_chargeType,
			'servicecharge' => $settinginfo->servicecharge,
			'vat' => $settinginfo->vat,
			'opentime' => $settinginfo->opentime,
			'closetime' => $settinginfo->closetime,
			'reservationopen' => $reservationopen,
			'storename' => $settinginfo->storename,
			'title' => $settinginfo->title,
			'address' => $settinginfo->address,
			'email' => $settinginfo->email,
			'phone' => $settinginfo->phone,
			'isvatnumshow' => $settinginfo->isvatnumshow,
			'vattinno' => $settinginfo->vattinno,
			'logo' => $settinginfo->logo,
			'timezone' => $settinginfo->timezone,
			'printtype' => $settinginfo->printtype,
			'kitchenrefreshtime' => $settinginfo->kitchenrefreshtime,
			'nofitysound' => $soundinfo->nofitysound,
			'addtocartsound' => $soundinfo->addtocartsound,
			'csrftokeng' => $this->security->get_csrf_hash(),
		);
		$data['basicinfo'] = $allbasicinfo;
		header('Content-Type: text/javascript');
		echo ('window.basicinfo = ' . json_encode($data['basicinfo']) . ';');
		exit();
	}
	public function updateorderjs($id)
	{
		$data['customerorder'] = $this->order_model->read('*', 'customer_order', array('order_id' => $id));
		echo ('window.orderinfo = ' . json_encode($data['customerorder']) . ';');
	}
}
