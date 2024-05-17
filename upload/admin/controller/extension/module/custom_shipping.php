<?php
class ControllerExtensionModuleCustomShipping extends Controller {
	private $error = array();
	private $v_d 			= '';
	private $version 		= '1.0.0.0';
	private $extension_code = 'hpwbsint';
	private $extension_type = 'i0';
	private $domain 		= '';

	public function index() {
		$this->domain = str_replace("www.", "", $_SERVER['SERVER_NAME']);

		$this->houseKeeping();

		$this->language->load('extension/module/custom_shipping');

		$this->rightman();


		// if($_SERVER['SERVER_NAME'] != $this->v_d) {
		// 	$this->storeAuth();
		// } else {
		// 	$this->theData();
		// }
		$this->theData();

	}

	public function theData() {

		$url = $this->request->get['route'];
		if($this->checkDatabase()) {

			$this->language->load('extension/module/custom_shipping');

			$this->document->setTitle($this->language->get('heading_title2'));

			$data['install_database'] = $this->url->link('extension/module/custom_shipping/installDatabase', 'user_token=' . $this->session->data['user_token'], true);

			$data['text_install_message'] = $this->language->get('text_install_message');

			$data['text_upgrade'] = $this->language->get('text_upgrade');

			$data['error_database'] = $this->language->get('error_database');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
				'separator' => false
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('extension/module/hpwd_notification', $data));

		} else {
			$this->language->load('extension/module/custom_shipping');
			$this->load->model('extension/module/custom_shipping');

			$this->document->setTitle($this->language->get('heading_title2'));

			$this->getList();
		}
	}

	public function add() {
		$this->language->load('extension/module/custom_shipping');

		$this->document->setTitle($this->language->get('heading_title2'));

		$this->load->model('extension/module/custom_shipping');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_custom_shipping->addCustomShipping($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->language->load('extension/module/custom_shipping');

		$this->document->setTitle($this->language->get('heading_title2'));

		$this->load->model('extension/module/custom_shipping');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_custom_shipping->editCustomShipping($this->request->get['custom_shipping_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->language->load('extension/module/custom_shipping');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/custom_shipping');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $custom_shipping_id) {
				$this->model_extension_module_custom_shipping->deleteCustomShipping($custom_shipping_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id.title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/module/custom_shipping/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/module/custom_shipping/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['save'] = $this->url->link('extension/module/custom_shipping/save', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['custom_shippings'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$custom_shipping_total = $this->model_extension_module_custom_shipping->getTotalCustomShippings();

		$results = $this->model_extension_module_custom_shipping->getCustomShippings($filter_data);

		foreach ($results as $result) {
			$data['custom_shippings'][] = array(
				'custom_shipping_id'  => $result['custom_shipping_id'],
				'total'               => $result['total'],
				'country_name'       => $result['country_name'],
				'zone_name'           => $result['zone_name'],
				'city_name'           => $result['city_name'],
				'rate'                => $result['rate'],
				'etd'                 => $result['etd'],
				'edit'                => $this->url->link('extension/module/custom_shipping/edit', 'user_token=' . $this->session->data['user_token'] . '&custom_shipping_id=' . $result['custom_shipping_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title2');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['tab_free_shipping_list']  = $this->language->get('tab_free_shipping_list');
		$data['tab_setting']             = $this->language->get('tab_setting');

		$data['entry_default_image']        = $this->language->get('entry_default_image');
		$data['entry_shipping_name']        = $this->language->get('entry_shipping_name');
		$data['entry_shipping_description']  = $this->language->get('entry_shipping_description');

		$data['column_total']             = $this->language->get('column_total');
		$data['column_province']          = $this->language->get('column_province');
		$data['column_city']              = $this->language->get('column_city');
		$data['column_sub_district']      = $this->language->get('column_sub_district');
		$data['column_rate']              = $this->language->get('column_rate');
		$data['column_etd']               = $this->language->get('column_etd');
		$data['column_action']            = $this->language->get('column_action');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		$this->load->model('tool/image');

		if (isset($this->request->post['c_shipping_image']) && is_file(DIR_IMAGE . $this->request->post['c_shipping_image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['c_shipping_image'], 60, 30);
		} elseif ($this->config->get('c_shipping_image') && is_file(DIR_IMAGE . $this->config->get('c_shipping_image'))) {
			$data['thumb'] = $this->model_tool_image->resize($this->config->get('c_shipping_image'), 60, 30);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 60, 30);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 60, 30);


		if (isset($this->request->post['c_shipping_name'])) {
			$data['c_shipping_name'] = $this->request->post['c_shipping_name'];
		} else if($this->config->get('c_shipping_name')) {
			$data['c_shipping_name'] = $this->config->get('c_shipping_name');
		} else {
			$data['c_shipping_name'] = '';
		}

		if (isset($this->request->post['c_shipping_description'])) {
			$data['c_shipping_description'] = $this->request->post['c_shipping_description'];
		} else if($this->config->get('c_shipping_description')) {
			$data['c_shipping_description'] = $this->config->get('c_shipping_description');
		} else {
			$data['c_shipping_description'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_rate'] = $this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'] . '&sort=rate' . $url, true);
		$data['sort_sub_district_id'] = $this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'] . '&sort=sub_district_id' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $custom_shipping_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($custom_shipping_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($custom_shipping_total - $this->config->get('config_limit_admin'))) ? $custom_shipping_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $custom_shipping_total, ceil($custom_shipping_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/custom_shipping_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title2');

		$data['text_form'] = !isset($this->request->get['custom_shipping_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['text_none']    = $this->language->get('text_none');

		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_all_zone'] = $this->language->get('text_all_zone');
		$data['text_all_city'] = $this->language->get('text_all_city');

		$data['entry_total']          = $this->language->get('entry_total');
		$data['help_total']           = $this->language->get('help_total');
		$data['entry_rate_type']      = $this->language->get('entry_rate_type');
		$data['entry_country']        = $this->language->get('entry_country');
		$data['entry_zone']           = $this->language->get('entry_zone');
		$data['entry_city']           = $this->language->get('entry_city');
		$data['entry_rate']           = $this->language->get('entry_rate');
		$data['help_rate']            = $this->language->get('help_rate');
		$data['entry_etd']            = $this->language->get('entry_etd');
		$data['help_etd']             = $this->language->get('help_etd');
		$data['entry_status']         = $this->language->get('entry_status');

		$data['text_per_kg']          = $this->language->get('text_per_kg');
		$data['text_flat_rate']       = $this->language->get('text_flat_rate');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['province_id'])) {
			$data['error_zone_id'] = $this->error['zone_id'];
		} else {
			$data['error_zone_id'] = '';
		}

		if (isset($this->error['country_id'])) {
			$data['error_country_id'] = $this->error['country_id'];
		} else {
			$data['error_country_id'] = '';
		}

		if (isset($this->error['etd'])) {
			$data['error_etd'] = $this->error['etd'];
		} else {
			$data['error_etd'] = '';
		}

		if (isset($this->error['rate'])) {
			$data['error_rate'] = $this->error['rate'];
		} else {
			$data['error_rate'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['custom_shipping_id'])) {
			$data['action'] = $this->url->link('extension/module/custom_shipping/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {

		}

		$data['cancel'] = $this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['custom_shipping_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$custom_shipping_info = $this->model_extension_module_custom_shipping->getCustomShipping($this->request->get['custom_shipping_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();


		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($custom_shipping_info)) {
			$data['status'] = $custom_shipping_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['total'])) {
			$data['total'] = $this->request->post['total'];
		} elseif (!empty($custom_shipping_info)) {
			$data['total'] = $custom_shipping_info['total'];
		} else {
			$data['total'] = 0;
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} elseif (!empty($custom_shipping_info)) {
			$data['country_id'] = $custom_shipping_info['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->request->post['zone_id'])) {
			$data['zone_id'] = $this->request->post['zone_id'];
		} elseif (!empty($custom_shipping_info)) {
			$data['zone_id'] = $custom_shipping_info['zone_id'];
		} else {
			$data['zone_id'] = $this->config->get('config_zone_id');
		}

		if (isset($this->request->post['city_id'])) {
			$data['city_id'] = $this->request->post['city_id'];
		} elseif (!empty($custom_shipping_info)) {
			$data['city_id'] = $custom_shipping_info['city_id'];
		} else {
			$data['city_id'] = 0;
		}

		if (isset($this->request->post['country_name'])) {
			$data['country_name'] = $this->request->post['country_name'];
		} elseif (!empty($custom_shipping_info)) {
			$data['country_name'] = $custom_shipping_info['country_name'];
		} else {
			$data['country_name'] = '';
		}

		if (isset($this->request->post['zone_name'])) {
			$data['zone_name'] = $this->request->post['zone_name'];
		} elseif (!empty($custom_shipping_info)) {
			$data['zone_name'] = $custom_shipping_info['zone_name'];
		} else {
			$data['zone_name'] = '';
		}

		if (isset($this->request->post['city_name'])) {
			$data['city_name'] = $this->request->post['city_name'];
		} elseif (!empty($custom_shipping_info)) {
			$data['city_name'] = $custom_shipping_info['city_name'];
		} else {
			$data['city_name'] = '';
		}


		if (isset($this->request->post['rate'])) {
			$data['rate'] = $this->request->post['rate'];
		} elseif (!empty($custom_shipping_info)) {
			$data['rate'] = $custom_shipping_info['rate'];
		} else {
			$data['rate'] = 0;
		}

		if (isset($this->request->post['rate_type'])) {
			$data['rate_type'] = $this->request->post['rate_type'];
		} elseif (!empty($custom_shipping_info)) {
			$data['rate_type'] = $custom_shipping_info['rate_type'];
		} else {
			$data['rate_type'] = '';
		}
		if (isset($this->request->post['total_weight'])) {
			$data['total_weight'] = $this->request->post['total_weight'];
		} elseif (!empty($custom_shipping_info)) {
			$data['total_weight'] = $custom_shipping_info['total_weight'];
		} else {
			$data['total_weight'] = 0;
		}
		if (isset($this->request->post['for_every'])) {
			$data['for_every'] = $this->request->post['for_every'];
		} elseif (!empty($custom_shipping_info)) {
			$data['for_every'] = $custom_shipping_info['for_every'];
		} else {
			$data['for_every'] = 0;
		}

		if (isset($this->request->post['cost'])) {
			$data['cost'] = $this->request->post['cost'];
		} elseif (!empty($custom_shipping_info)) {
			$data['cost'] = $custom_shipping_info['cost'];
		} else {
			$data['cost'] = 0;
		}


		if (isset($this->request->post['etd'])) {
			$data['etd'] = $this->request->post['etd'];
		} elseif (!empty($custom_shipping_info)) {
			$data['etd'] = $custom_shipping_info['etd'];
		} else {
			$data['etd'] = '';
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/custom_shipping_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/custom_shipping')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post['country_id'] == '') {
			$this->error['country_id'] = $this->language->get('error_country_id');
		}

		if ($this->request->post['zone_id'] == '') {
			$this->error['zone_id'] = $this->language->get('error_zone_id');
		}

		if (utf8_strlen($this->request->post['rate']) < 3) {
			$this->error['rate']= $this->language->get('error_rate');
		}

		if (utf8_strlen($this->request->post['etd']) < 1) {
			$this->error['etd']= $this->language->get('error_etd');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/custom_shipping')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function installDatabase() {
		$this->load->model('extension/module/custom_shipping');

		$error=0;

		if($this->model_extension_module_custom_shipping->installTable()) {
			$error++;
		}

		if($error < 1)
			$this->response->redirect($this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token']."&install=true", true));
		else
			$this->response->redirect($this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token']."&install=false", true));

		$this->language->load('extension/module/custom_shipping');

		$this->load->model('extension/module/custom_shipping');

		$this->model_extension_module_custom_shipping->installTable();

		$this->session->data['success'] = $this->language->get('text_success_installed');

		$route = $this->request->get['url'];

		$this->response->redirect($this->url->link($route, 'user_token=' . $this->session->data['user_token'], true));
	}

	public function checkDatabase() {
		$database_not_found = $this->validateTable();

		if(!$database_not_found) {
			return true;
		}

		return false;
	}

	public function validateTable() {

		$query = $this->db->query("SHOW TABLES LIKE 'custom_shipping'");

		return $query->num_rows;
	}

	public function uninstall() {
		if(!$this->checkDatabase()) {
			$this->language->load('extension/module/custom_shipping');

			$this->document->setTitle($this->language->get('error_database'));

			$data['install_database'] = $this->url->link('extension/module/custom_shipping/uninstallDatabase', 'user_token=' . $this->session->data['user_token'], true);

			$data['text_install_message'] = $this->language->get('text_uninstall_message');

			$data['text_upgrade'] = $this->language->get('text_downgrade');

			$data['error_database'] = $this->language->get('text_found_database');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
				'separator' => false
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('extension/module/hpwd_notification', $data));
		}
	}

	public function uninstallDatabase() {
		$this->load->model('extension/module/custom_shipping');
		if($this->model_extension_module_custom_shipping->uninstallTable())
		{
			$this->response->redirect($this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token']."&uninstall=true", true));
		}
		else {
			$this->response->redirect($this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token']."&uninstall=false", true));
		}
	}

	public function save() {
		$this->language->load('extension/module/custom_shipping');

		$this->load->model('setting/setting');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {

			$this->model_setting_setting->editSetting('c_shipping', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success_seting_changed');
		}

		$this->response->redirect($this->url->link('extension/module/custom_shipping', 'user_token=' . $this->session->data['user_token'], true));

	}
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/custom_shipping')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function storeAuth() {
		$data['curl_status'] = $this->curlcheck();
		$data['extension_code'] = $this->extension_code;
		$data['extension_type'] = $this->extension_type;
		$data['user_token'] = $this->session->data['user_token'];
		$this->flushdata();

		$this->document->setTitle($this->language->get('text_validation'));

		$data['text_curl']                  = $this->language->get('text_curl');
		$data['text_disabled_curl']         = $this->language->get('text_disabled_curl');

		$data['text_validation']            = $this->language->get('text_validation');
		$data['text_validate_store']        = $this->language->get('text_validate_store');
		$data['text_information_provide']   = $this->language->get('text_information_provide');
		$data['domain_name'] = str_replace("www.","",$_SERVER['SERVER_NAME']);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/hp_advanced_affiliate', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/validation', $data));
	}

	public function getTheQ($username,$order_id) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.hpwebdesign.id/rest/".$username."/".$order_id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_POSTFIELDS => "module_name=bundle",
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$result = json_decode($response);
		}
		if($result)
			return $result->results->shared_api_key;
		else
			return '';
	}
	protected function hpcs($ref = 0, $date = NULL) {
		$pf = dirname(getcwd()).'/system/library/cache/hpcs_log';
		if(!file_exists($pf)) {
			fopen($pf,'w');
		}
		$fh = fopen($pf,'r');

		if(!fgets($fh) || $ref = 1) {
			$fh = fopen($pf, "wb");
			if(!$fh) {
				chmod($pf,644);
			}
			fwrite($fh, "// HPWD -> Dilarang mengedit isi file ini untuk tujuan cracking validasi atau tindakan terlarang lainnya".PHP_EOL);
			$date = $date ? $date : date("d-m-Y",strtotime(date("d-m-Y").' + 1 year'));
			fwrite($fh, $date.PHP_EOL);
			fwrite($fh, $_SERVER['SERVER_NAME'].PHP_EOL);
		}

		fclose($fh);
	}

	private function rightman() {
		if($this->internetAccess()) {
			$this->load->model('extension/module/system_startup');

			$license = $this->model_extension_module_system_startup->checkLicenseKey($this->extension_code);

			if ($license) {
				if (isset($this->model_extension_module_system_startup->licensewalker)) {
					$url = $this->model_extension_module_system_startup->licensewalker($license['license_key'],$this->extension_code,$this->domain);
					$data = $url;
					$domain = isset($data['domain']) ? $data['domain'] : '';

					if($domain == $this->domain) {
						$this->v_d = $domain;
					} else {
						$this->flushdata();
					}
				}
			}

		} else {
			$this->error['warning'] = $this->language->get('error_no_internet_access');
		}
	}

	public function curlcheck() {
		return in_array ('curl', get_loaded_extensions()) ? true : false;
	}

	private function houseKeeping() {
		$file = 'https://api.hpwebdesign.io/validate.zip';
		$newfile = DIR_APPLICATION . 'validate.zip';

		if (!file_exists(DIR_APPLICATION . 'controller/common/hp_validate.php') || !file_exists(DIR_APPLICATION . 'model/extension/module/system_startup.php') || !file_exists(DIR_APPLICATION . 'view/template/extension/module/validation.twig')) {

			$file = $this->curl_get_file_contents($file);

			if (file_put_contents($newfile, $file)) {
				$zip = new ZipArchive();
				$res = $zip->open($newfile);
				if ($res === TRUE) {
					$zip->extractTo(DIR_APPLICATION);
					$zip->close();
					unlink($newfile);
				}
			}
		}

		$this->load->model('extension/module/system_startup');
		if (!isset($this->model_extension_module_system_startup->checkLicenseKey) || !isset($this->model_extension_module_system_startup->licensewalker)) {

			$file = $this->curl_get_file_contents($file);

			if (file_put_contents($newfile, $file)) {
				$zip = new ZipArchive();
				$res = $zip->open($newfile);
				if ($res === TRUE) {
					$zip->extractTo(DIR_APPLICATION);
					$zip->close();
					unlink($newfile);
				}
			}
		}

		if (!file_exists(DIR_SYSTEM . 'system.ocmod.xml')) {
			$str = $this->curl_get_file_contents('https://api.hpwebdesign.io/system.ocmod.txt');

			file_put_contents(dirname(getcwd()) . '/system/system.ocmod.xml', $str);
		}
		$sql = "CREATE TABLE IF NOT EXISTS `hpwd_license`(
						`hpwd_license_id` INT(11) NOT NULL AUTO_INCREMENT,
						`license_key` VARCHAR(64) NOT NULL,
						`code` VARCHAR(32) NOT NULL,
						`support_expiry` date DEFAULT NULL,
						 PRIMARY KEY(`hpwd_license_id`)
					) ENGINE = InnoDB;";
		$this->db->query($sql);
	}

	private function curl_get_file_contents($URL) {
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $URL);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);

		$contents = curl_exec($c);
		curl_close($c);

		if ($contents) return $contents;
		else return FALSE;
	}

	public function install() {
		// $this->installEvent();
		$this->houseKeeping();
	}

	public function flushdata() {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` LIKE '%custom_shipping_status%'");
	}

	private function internetAccess() {
		//  $connected = @fopen("http://google.com","r");
		//return $connected ? true : false;
		return true;
	}
}
