<?php
class ControllerExtensionPaymentPayanyway extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/payanyway');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_payanyway', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_account'] = $this->language->get('entry_account');
		$data['entry_secret'] = $this->language->get('entry_secret');
		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_demo'] = $this->language->get('entry_demo');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_secret'] = $this->language->get('help_secret');
		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['account'])) {
			$data['error_account'] = $this->error['account'];
		} else {
			$data['error_account'] = '';
		}

		if (isset($this->error['secret'])) {
			$data['error_secret'] = $this->error['secret'];
		} else {
			$data['error_secret'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/payanyway', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/payanyway', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_payanyway_account'])) {
			$data['payment_payanyway_account'] = $this->request->post['payment_payanyway_account'];
		} else {
			$data['payment_payanyway_account'] = $this->config->get('payment_payanyway_account');
		}

		if (isset($this->request->post['payment_payanyway_secret'])) {
			$data['payment_payanyway_secret'] = $this->request->post['payment_payanyway_secret'];
		} else {
			$data['payment_payanyway_secret'] = $this->config->get('payment_payanyway_secret');
		}

		if (isset($this->request->post['payment_payanyway_test'])) {
			$data['payment_payanyway_test'] = $this->request->post['payment_payanyway_test'];
		} else {
			$data['payment_payanyway_test'] = $this->config->get('payment_payanyway_test');
		}

		if (isset($this->request->post['payment_payanyway_demo'])) {
			$data['payment_payanyway_demo'] = $this->request->post['payment_payanyway_demo'];
		} else {
			$data['payment_payanyway_demo'] = $this->config->get('payment_payanyway_demo');
		}

		if (isset($this->request->post['payment_payanyway_total'])) {
			$data['payment_payanyway_total'] = $this->request->post['payment_payanyway_total'];
		} else {
			$data['payment_payanyway_total'] = $this->config->get('payment_payanyway_total');
		}

		if (isset($this->request->post['payment_payanyway_order_status_id'])) {
			$data['payment_payanyway_order_status_id'] = $this->request->post['payment_payanyway_order_status_id'];
		} else {
			$data['payment_payanyway_order_status_id'] = $this->config->get('payment_payanyway_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_payanyway_geo_zone_id'])) {
			$data['payment_payanyway_geo_zone_id'] = $this->request->post['payment_payanyway_geo_zone_id'];
		} else {
			$data['payment_payanyway_geo_zone_id'] = $this->config->get('payment_payanyway_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_payanyway_status'])) {
			$data['payment_payanyway_status'] = $this->request->post['payment_payanyway_status'];
		} else {
			$data['payment_payanyway_status'] = $this->config->get('payment_payanyway_status');
		}

		if (isset($this->request->post['payment_payanyway_sort_order'])) {
			$data['payment_payanyway_sort_order'] = $this->request->post['payment_payanyway_sort_order'];
		} else {
			$data['payment_payanyway_sort_order'] = $this->config->get('payment_payanyway_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/payanyway', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/payanyway')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_payanyway_account']) {
			$this->error['account'] = $this->language->get('error_account');
		}

		if (!$this->request->post['payment_payanyway_secret']) {
			$this->error['secret'] = $this->language->get('error_secret');
		}

		return !$this->error;
	}
}