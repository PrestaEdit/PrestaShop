<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ProductsComparisonCompareModuleFrontController extends ModuleFrontController
{
	public function __construct()
	{
		parent::__construct();

		$this->display_column_left = false;
		$this->display_column_right = false;
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS($this->module->getPathUri().'css/comparator.css');
	}

	/**
	 * Display ajax content (this function is called instead of classic display, in ajax mode)
	 */
	public function displayAjax()
	{
		// Add or remove product with Ajax
		if (Tools::getValue('ajax') && Tools::getValue('id_product') && Tools::getValue('action'))
		{
			if (Tools::getValue('action') == 'add')
			{
				$id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: false;
				if (CompareProduct::getNumberProducts($id_compare) < $this->module->comparator_max_item)
					CompareProduct::addCompareProduct($id_compare, (int)Tools::getValue('id_product'));
				else
					$this->ajaxDie('0');
			}
			elseif (Tools::getValue('action') == 'remove')
			{
				if (isset($this->context->cookie->id_compare))
					CompareProduct::removeCompareProduct((int)$this->context->cookie->id_compare, (int)Tools::getValue('id_product'));
				else
					$this->ajaxDie('0');
			}
			else
				$this->ajaxDie('0');
			$this->ajaxDie('1');
		}
		$this->ajaxDie('0');
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		if (Tools::getValue('ajax'))
			return;
		parent::initContent();

		//Clean compare product table
		CompareProduct::cleanCompareProducts('week');

		$hasProduct = false;

		if (!(int)$this->module->comparator_max_item)
			return Tools::redirect('index.php?controller=404');

		$ids = null;
		if (($product_list = Tools::getValue('compare_product_list')) && ($postProducts = (isset($product_list) ? rtrim($product_list, '|') : '')))
			$ids = array_unique(explode('|', $postProducts));
		elseif (isset($this->context->cookie->id_compare))
		{
			$ids = CompareProduct::getCompareProducts($this->context->cookie->id_compare);
			if (count($ids))
				Tools::redirect($this->context->link->getModuleLink($this->module->name, 'compare', array('compare_product_list' => implode('|', $ids)), null, (int)$this->context->language->id));
		}

		if ($ids)
		{
			if (count($ids) > 0)
			{
				if (count($ids) > (int)$this->module->comparator_max_item)
					$ids = array_slice($ids, 0, (int)$this->module->comparator_max_item);

				$listProducts = array();
				$listFeatures = array();

				foreach ($ids as $k => &$id)
				{
					$current_product = new Product((int)$id, true, $this->context->language->id);
					if (!Validate::isLoadedObject($current_product) || !$current_product->active || !$current_product->isAssociatedToShop())
					{
						if (isset($this->context->cookie->id_compare))
							CompareProduct::removeCompareProduct($this->context->cookie->id_compare, $id);
						unset($ids[$k]);
						continue;
					}

					foreach ($current_product->getFrontFeatures($this->context->language->id) as $feature)
						$listFeatures[$current_product->id][$feature['id_feature']] = $feature['value'];

					$cover = Product::getCover((int)$id);

					$current_product->id_image = Tools::htmlentitiesUTF8(Product::defineProductImage(array('id_image' => $cover['id_image'], 'id_product' => $id), $this->context->language->id));
					$current_product->allow_oosp = Product::isAvailableWhenOutOfStock($current_product->out_of_stock);
					$listProducts[] = $current_product;
				}

				if (count($listProducts) > 0)
				{
					$width = 80 / count($listProducts);

					$hasProduct = true;
					$ordered_features = CompareProduct::getFeaturesForComparison($ids, $this->context->language->id);
					$this->context->smarty->assign(array(
						'ordered_features' => $ordered_features,
						'product_features' => $listFeatures,
						'products' => $listProducts,
						'width' => $width,
						'HOOK_COMPARE_EXTRA_INFORMATION' => Hook::exec('displayCompareExtraInformation', array('list_ids_product' => $ids)),
						'HOOK_EXTRA_PRODUCT_COMPARISON' => Hook::exec('displayProductComparison', array('list_ids_product' => $ids)),
						'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
					));
				}
				elseif (isset($this->context->cookie->id_compare))
				{
					$object = new CompareProduct((int)$this->context->cookie->id_compare);
					if (Validate::isLoadedObject($object))
					  $object->delete();
				}
			}
		}
		$this->context->smarty->assign('hasProduct', $hasProduct);

		$this->context->smarty->assign('compare_controller_link', $this->context->link->getModuleLink($this->module->name, 'compare', array(), null, (int)$this->context->language->id));

		$this->setTemplate('products-comparison.tpl');
	}
}