<?php
class ControllerExtensionModuleStoreSetting extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/store_setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		$this->load->model('tool/image');

        $this->load->model('setting/setting');
        $this->document->addScript('view/javascript/colorpicker/colorpicker.min.js');
        $this->document->addLink('view/javascript/colorpicker/colorpicker.min.css', 'stylesheet');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $action = isset($this->request->post["action"]) ? $this->request->post["action"] : "";
            
            $this->model_setting_setting->editSetting('config', $this->request->post);

			if ($this->config->get('config_currency_auto')) {
				$this->load->model('localisation/currency');

				$this->model_localisation_currency->refresh();
			}

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

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        
		if (isset($this->error)) {
			$data['error'] = $this->error;
		} else {
			$data['error'] = [];
		}
        // var_dump($data['error']);

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

        $this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

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
			$this->load->language('extension/captcha/' . $code, '/extension');

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

        // images
        foreach ($data['languages'] as $language) {

           if (isset($this->request->post['config_logo_admin']['language_' . $language['language_id']]) 
            && is_file(DIR_IMAGE . $this->request->post['config_logo_admin']['language_' . $language['language_id']])) {
                $data['logo_admin']['language_' . $language['language_id']] = $this->model_tool_image->resize($this->request->post['config_logo_admin'], 100, 100);
                } elseif (isset($data['config_logo_admin']['language_' . $language['language_id']]) && 
                !empty($data['config_logo_admin']['language_' . $language['language_id']]) &&
                is_file(DIR_IMAGE . $data['config_logo_admin']['language_' . $language['language_id']])) {
                $data['logo_admin']['language_' . $language['language_id']] = $this->model_tool_image->resize($data['config_logo_admin']['language_' . $language['language_id']], 100, 100);
                } else {
                $data['logo_admin']['language_' . $language['language_id']] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }

                if (isset($this->request->post['config_logo']['language_' . $language['language_id']]) 
                    && is_file(DIR_IMAGE . $this->request->post['config_logo']['language_' . $language['language_id']])) {
                    $data['logo']['language_' . $language['language_id']] = $this->model_tool_image->resize($this->request->post['config_logo']['language_' . $language['language_id']], 100, 100);
                } elseif (isset($data['config_logo']['language_' . $language['language_id']]) &&
                 !empty($data['config_logo']['language_' . $language['language_id']]) &&
                is_file(DIR_IMAGE . $data['config_logo']['language_' . $language['language_id']])) {
                    $data['logo']['language_' . $language['language_id']] = $this->model_tool_image->resize($data['config_logo']['language_' . $language['language_id']], 100, 100);
                } else {
                    $data['logo']['language_' . $language['language_id']] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }


                 if (isset($this->request->post['config_icon']['language_' . $language['language_id']]) 
                    && is_file(DIR_IMAGE . $this->request->post['config_icon']['language_' . $language['language_id']])) {
                    $data['icon']['language_' . $language['language_id']] = $this->model_tool_image->resize($this->request->post['config_icon']['language_' . $language['language_id']], 100, 100);
                } elseif (isset($data['config_icon']['language_' . $language['language_id']]) &&
                !empty($data['config_icon']['language_' . $language['language_id']]) &&
                is_file(DIR_IMAGE . $data['config_icon']['language_' . $language['language_id']])) {
                    $data['icon']['language_' . $language['language_id']] = $this->model_tool_image->resize($data['config_icon']['language_' . $language['language_id']], 100, 100);
                } else {
                    $data['icon']['language_' . $language['language_id']] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }

        }
        
        


		$data['save_section'] = $this->load->controller('extension/component/save_section', [
			'form_id' 		=> "form-module",
			'save_new' 		=> false,
			'save_edit' 	=> true,
			'save' 			=> true,
			'cancel' 		=> $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
		]);
		
		$data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/store_setting/store_setting', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/store_setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} 

        if(isset($this->request->post['config_meta_title'] )) {
            foreach($this->request->post['config_meta_title'] as $key => $value) {
                if (empty($value)) {
                $this->error['meta_title'][$key] = $this->language->get('error_meta_title');
                }
            }
        }

        if (utf8_strlen($this->request->post['config_mail_smtp_username']) != '') {
            if (utf8_strlen($this->request->post['config_mail_smtp_timeout']) == '') {
                $this->error['mail_smtp_timeout'] = $this->language->get('error_mail_smtp_timeout');
            }
        }

        if (!$this->request->post['config_input_ovt_width']) {
            $this->error['error_config_input_ovt_width'] = 'Width Required';
        }

        if (!$this->request->post['config_input_ovt_height']) {
            $this->error['error_config_input_ovt_height'] = 'Height Required';
        }

        if(isset($this->request->post['config_name'] )) {
            foreach($this->request->post['config_name'] as $key => $value) {
                if (empty($value)) {
                    $this->error['name'][$key] = $this->language->get('error_name');
                }
            }
        }

		if (!$this->request->post['config_name']) {
			$this->error['name'] = $this->language->get('error_name');
		}

		
        if(isset($this->request->post['config_owner'] )) {
            foreach($this->request->post['config_owner'] as $key => $value) {
                if ((utf8_strlen($value) < 3) || (utf8_strlen($value) > 64)) {
                    $this->error['owner'][$key] = $this->language->get('error_owner');
                }
            }
		}
		
        if(isset($this->request->post['config_address'] )) {
            foreach($this->request->post['config_address'] as $key => $value) {
                if ((utf8_strlen($value) < 3) || (utf8_strlen($value) > 64)) {
                    $this->error['address'][$key] = $this->language->get('error_address');
                }
            }
		}

        if(isset($this->request->post['config_email'] )) {
            foreach($this->request->post['config_email'] as $key => $value) {
                if ((utf8_strlen($value) > 96) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->error['email'][$key] = $this->language->get('error_email');
                }
            }
		}
		
        if(isset($this->request->post['config_telephone'] )) {
            foreach($this->request->post['config_telephone'] as $key => $value) {
                if ((utf8_strlen($value) < 3) || (utf8_strlen($value) > 32)) {
                    $this->error['telephone'][$key] = $this->language->get('error_telephone');
                }
            }
		}

		if (!empty($this->request->post['config_customer_group_display']) && !in_array($this->request->post['config_customer_group_id'], $this->request->post['config_customer_group_display'])) {
			$this->error['customer_group_display'] = $this->language->get('error_customer_group_display');
		}

		if (!$this->request->post['config_limit_admin']) {
			$this->error['limit_admin'] = $this->language->get('error_limit');
		}

		if ($this->request->post['config_login_attempts'] < 1) {
			$this->error['login_attempts'] = $this->language->get('error_login_attempts');
		}

		if (!$this->request->post['config_voucher_min']) {
			$this->error['voucher_min'] = $this->language->get('error_voucher_min');
		}

		if (!$this->request->post['config_voucher_max']) {
			$this->error['voucher_max'] = $this->language->get('error_voucher_max');
		}

		if (!isset($this->request->post['config_processing_status'])) {
			$this->error['processing_status'] = $this->language->get('error_processing_status');
		}

		if (!isset($this->request->post['config_complete_status'])) {
			$this->error['complete_status'] = $this->language->get('error_complete_status');
		}
		
		if (!$this->request->post['config_error_filename']) {
			$this->error['log'] = $this->language->get('error_log_required');
		} elseif (preg_match('/\.\.[\/\\\]?/', $this->request->post['config_error_filename'])) {
			$this->error['log'] = $this->language->get('error_log_invalid');
		} elseif (substr($this->request->post['config_error_filename'], strrpos($this->request->post['config_error_filename'], '.')) != '.log') {
			$this->error['log'] = $this->language->get('error_log_extension');
		}
		
		if ((utf8_strlen($this->request->post['config_encryption']) < 32) || (utf8_strlen($this->request->post['config_encryption']) > 1024)) {
			$this->error['encryption'] = $this->language->get('error_encryption');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}


        foreach ($this->request->post['config_social'] as $key => $value) {
            if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 64)) {
                $this->error['social_name'][$key] = $this->language->get('error_social_name');
            }
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
                    $setting_data[$result['key']]['language_' . $result['language_id']] = $result['value'];
                } else {
                    $setting_data[$result['key']] = $result['value'];
                }
			} else {
                if($result['language_id'] > 0) {
                    $setting_data[$result['key']]['language_' .$result['language_id']] = json_decode($result['value'], true);
                } else {
                    $setting_data[$result['key']] = json_decode($result['value'], true);
                }
			}
		}

		return $setting_data;
	}

    

}