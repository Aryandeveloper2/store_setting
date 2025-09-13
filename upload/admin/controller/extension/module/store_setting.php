<?php
class ControllerExtensionModuleStoreSetting extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/store_setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		$this->load->model('tool/image');

        $this->load->model('setting/setting');
        
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $action = isset($this->request->post["action"]) ? $this->request->post["action"] : "";
	
			$this->session->data['success'] = $this->language->get('text_success');
			
            $this->load->controller('extension/component/save_section/redirect', [
				'action'    =>  $action,
				'save'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=module', 'SSL'),
				'save_edit' => $this->url->link('extension/module/store_setting', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			]);
		}
		
		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$module_info = $this->getSetting('config');
		    $data = $module_info;
		} else if($this->request->server['REQUEST_METHOD'] == 'POST') {
		    $module_info = $this->request->post;
		    $data = $module_info;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        
		if (isset($this->error)) {
			$data['error'] = $this->error;
		} else {
			$data['error'] = '';
		}


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/store_setting', 'user_token=' . $this->session->data['user_token'], true)
        );
		

        $data['action'] = $this->url->link('extension/module/store_setting', 'user_token=' . $this->session->data['user_token'], true);
	
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
	
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
        

        if ($this->request->server['HTTPS']) {
			$data['store_url'] = HTTPS_CATALOG;
		} else {
			$data['store_url'] = HTTP_CATALOG;
		}

        $data['themes'] = array();

		$this->load->model('setting/extension');

		$extensions = $this->model_setting_extension->getInstalled('theme');

		foreach ($extensions as $code) {
			$this->load->language('extension/theme/' . $code, 'extension');
			
			$data['themes'][] = array(
				'text'  => $this->language->get('extension')->get('heading_title'),
				'value' => $code
			);
		}

        $this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();


        $this->load->model('localisation/location');

		$data['locations'] = $this->model_localisation_location->getLocations();

        $data['shamsidate_formats'] = array();

		$data['shamsidate_formats'][] = array(
			'code' 	=> $this->language->get('date_format_short'),
			'title' => ($this->language->get('code') == 'fa') ? jdate($this->language->get('date_format_short')) : date($this->language->get('date_format_short'))
		);

		$data['shamsidate_formats'][] = array(
			'code' 	=> $this->language->get('date_format_long'),
			'title' => ($this->language->get('code') == 'fa') ? jdate($this->language->get('date_format_long')) : date($this->language->get('date_format_long'))
		);
		$data['shamsidate_formats'][] = array(
			'code' 	=> $this->language->get('datetime_format'),
			'title' => ($this->language->get('code') == 'fa') ? jdate($this->language->get('datetime_format')) : date($this->language->get('datetime_format'))
		);
		$data['shamsidate_formats'][] = array(
			'code' 	=> $this->language->get('datetime_format_short'),
			'title' => ($this->language->get('code') == 'fa') ? jdate($this->language->get('datetime_format_short')) : date($this->language->get('datetime_format_short'))
		);
		$data['shamsidate_formats'][] = array(
			'code' 	=> $this->language->get('datetime_format_long'),
			'title' => ($this->language->get('code') == 'fa') ? jdate($this->language->get('datetime_format_long')) : date($this->language->get('datetime_format_long'))
		);

        $this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();
        $this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
        $this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
	    $this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
	    $this->load->model('catalog/information');

		$data['informations'] = $this->model_catalog_information->getInformations();

        $this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $this->load->model('user/api');

		$data['apis'] = $this->model_user_api->getApis();
        $this->load->model('localisation/stock_status');

        $data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
        $this->load->model('localisation/return_status');

		$data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();
	    $this->load->model('setting/extension');

		$data['captchas'] = array();

		// Get a list of installed captchas
		$extensions = $this->model_setting_extension->getInstalled('captcha');

		foreach ($extensions as $code) {
			$this->load->language('extension/captcha/' . $code, 'extension');

			if ($this->config->get('captcha_' . $code . '_status')) {
				$data['captchas'][] = array(
					'text'  => $this->language->get('extension')->get('heading_title'),
					'value' => $code
				);
			}
		}		

	    $data['mail_alerts'] = array();

		$data['mail_alerts'][] = array(
			'text'  => $this->language->get('text_mail_account'),
			'value' => 'account'
		);

		$data['mail_alerts'][] = array(
			'text'  => $this->language->get('text_mail_affiliate'),
			'value' => 'affiliate'
		);

		$data['mail_alerts'][] = array(
			'text'  => $this->language->get('text_mail_order'),
			'value' => 'order'
		);

		$data['mail_alerts'][] = array(
			'text'  => $this->language->get('text_mail_review'),
			'value' => 'review'
		);

        
        $this->load->model('user/user_group');
        $data['user_groups'] = $this->model_user_user_group->getUserGroups();
        if($this->user->getGroupId() != $this->config->get('config_developer_user_group')) {
        $data['is_developer'] = 0;
        } else {
        $data['is_developer'] = 1;
        }

		$data['save_section'] = $this->load->controller('extension/component/save_section', [
			'form_id' 		=> "form-module",
			'save_new' 		=> false,
			'save_edit' 	=> true,
			'save' 			=> true,
			'cancel' 		=> $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
		]);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/store_setting/store_setting', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/store_setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} 



        
		return !$this->error;
	}

    protected function getSetting($code, $store_id = 0) {
		$setting_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE 
        store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
                if($result['language_id'] > 0) {
                    $setting_data[$result['key']][$result['language_id']] = $result['value'];
                } else {
                    $setting_data[$result['key']] = $result['value'];
                }
			} else {
                if($result['language_id'] > 0) {
                    $setting_data[$result['key']][$result['language_id']] = json_decode($result['value'], true);
                } else {
                    $setting_data[$result['key']] = json_decode($result['value'], true);
                }
			}
		}

		return $setting_data;
	}

}