<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

        $this->load->model('setting/extension');
        $data['name'] = $this->config->get('config_name');
//        echo "<pre>";
//		print_r($this->customer->isLogged()); exit;

        $data['logged'] = true;
//        $data['logged'] = $this->customer->isLogged();

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');

        $mob_det = new Mobile_Detect();

//        $data['mobile'] = $mob_det->isMobile();

        if($mob_det->isMobile()){
            $data['mobile'] = true;
        }else{
            $data['mobile'] = false;
        }



        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {

                $cat = [];
                $cat['filter_category_id'] = $category['category_id'];
                $cat['limit'] = 3;
                $cat['start'] = 0;

                $products = $this->model_catalog_product->getProducts($cat);
                // Level 1

                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'products' => $products,
                    'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id']),
                    'image'    => $category['image']
                );

            }
        }

//        print_r($data['categories']);

		$this->response->setOutput($this->load->view('common/home', $data));
	}
}
