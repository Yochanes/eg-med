<?php
class ControllerCommonMenupopup extends Controller {
	public function index() {
		$this->load->language('common/menu');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

        $this->load->model('tool/image');


        foreach ($categories as $category) {
			if ($category['top']) {

				// Level 1

                if ($category['image']) {
                    $imagecat = $this->model_tool_image->resize($category['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
                } else {
                    $imagecat = '';
                }

				$data['categories'][] = array(
					'name'     => $category['name'],
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id']),
                    'image'    => $imagecat
				);

			}
		}

        return $this->load->view('common/menupopup', $data);

	}
}
