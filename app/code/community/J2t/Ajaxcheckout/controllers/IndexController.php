<?php

/**
 * J2T-DESIGN.
 *
 * @category   J2t
 * @package    J2t_Ajaxcheckout
 * @copyright  Copyright (c) 2003-2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    OSL
 */

class J2t_Ajaxcheckout_IndexController extends /*Mage_Checkout_CartController*/ Mage_Core_Controller_Front_Action
{

    const CONFIGURABLE_PRODUCT_IMAGE= 'checkout/cart/configurable_product_image';
    const USE_PARENT_IMAGE          = 'parent';

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function cartdeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                Mage::getSingleton('checkout/cart')->removeItem($id)
                  ->save();
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addError($this->__('Cannot remove item'));
            }
        }
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');

        $this->renderLayout();
    }

    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function cartAction()
    {
        if ($this->getRequest()->getParam('cart')){
            if ($this->getRequest()->getParam('cart') == "delete"){
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    try {
                        Mage::getSingleton('checkout/cart')->removeItem($id)
                          ->save();
                    } catch (Exception $e) {
                        Mage::getSingleton('checkout/session')->addError($this->__('Cannot remove item'));
                    }
                }
            }
			
			
		
			
			
			### Start ###
            if($this->getRequest()->getParam('cart') == 'updateItemOptions') {
        		$cart   = Mage::getSingleton('checkout/cart');
        		$id = (int) $this->getRequest()->getParam('id');
        		$params = $this->getRequest()->getParams();
        		//Mage::log($id);
        		if (!isset($params['options'])) {
				    $params['options'] = array();
				}
				try {
				    if (isset($params['qty'])) {
				        $filter = new Zend_Filter_LocalizedToNormalized(
				            array('locale' => Mage::app()->getLocale()->getLocaleCode())
				        );
				        $params['qty'] = $filter->filter($params['qty']);
				    }

				    $quoteItem = $cart->getQuote()->getItemById($id);
				    if (!$quoteItem) {
				        Mage::throwException($this->__('Quote item is not found.'));
				    }
				    Mage::log($params);
				    $item = $cart->updateItem($id, new Varien_Object($params));
				    $quoteItem->setDevedate($params['devedate']);
					$quoteItem->save();
				    if (is_string($item)) {
				        Mage::throwException($item);
				    }
				    if ($item->getHasError()) {
				        Mage::throwException($item->getMessage());
				    }

				    $related = $this->getRequest()->getParam('related_product');
				    if (!empty($related)) {
				        $cart->addProductsByIds(explode(',', $related));
				    }

				    $cart->save();

				    $this->_getSession()->setCartWasUpdated(true);

				    Mage::dispatchEvent('checkout_cart_update_item_complete',
				        array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				    );
				    
				    //add product img info
				    $productId = (int) $this->getRequest()->getParam('product');
				    
				    if ($productId) {
				    	$product = Mage::getModel('catalog/product')
				            ->setStoreId(Mage::app()->getStore()->getId())
				            ->load($productId);
			            $img = '';
		                Mage::dispatchEvent('checkout_cart_add_product_complete', array('product'=>$product, 'request'=>$this->getRequest()));

		                $photo_arr = explode("x",Mage::getStoreConfig('j2tajaxcheckout/default/j2t_ajax_cart_image_size', Mage::app()->getStore()->getId()));

		                $prod_img = $product;
		                if($product->isConfigurable() && isset($params['super_attribute'])){
		                    $attribute = $params['super_attribute'];
		                    if (Mage::getStoreConfig(self::CONFIGURABLE_PRODUCT_IMAGE) != self::USE_PARENT_IMAGE) {
		                        $prod_img_temp = Mage::getModel("catalog/product_type_configurable")->getProductByAttributes($attribute, $product);
		                        if ($prod_img_temp->getImage() != 'no_selection' && $prod_img_temp->getImage()){
		                            $prod_img = $prod_img_temp;
		                        }
		                    }
		                }
		                $img = '<img src="'.Mage::helper('catalog/image')->init($prod_img, 'thumbnail')->resize($photo_arr[0],$photo_arr[1]).'" width="'.$photo_arr[0].'" height="'.$photo_arr[1].'" />';
		                $message = $this->__('%s was updated to your shopping cart.', $product->getName());
						$messageB = $this->__('Warm tips:');
						$message1 = $this->__('As the quantity of the gift is changing all the time , exchange result is only associated with the success status of the order.');
		                Mage::getSingleton('checkout/session')->addSuccess('<div class="j2tajax-checkout-img">'.$img.'</div><div class="j2tajax-checkout-txt">'.$message.'</div>'.'<div class="j2tajax-sucess-add">' . '<b>' . $messageB .'</b>' . $message1 .'</div>'
						);
				    }
				    
				    /*if (!$this->_getSession()->getNoCartRedirect(true)) {
				        if (!$cart->getQuote()->getHasError()){
				            $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
				            $this->_getSession()->addSuccess($message);
				        }
				        $this->_goBack();
				    }*/
				    
				} catch (Mage_Core_Exception $e) {
				    if ($this->_getSession()->getUseNotice(true)) {
				        $this->_getSession()->addNotice($e->getMessage());
				    } else {
				        $messages = array_unique(explode("\n", $e->getMessage()));
				        foreach ($messages as $message) {
				            $this->_getSession()->addError($message);
				        }
				    }
				    
				} catch (Exception $e) {
				    Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot update the item.'));
				}
        	}
        	### end ###
			
			
        }

        if ($this->getRequest()->getParam('product') && $this->getRequest()->getParam('cart') != 'updateItemOptions') {
		
			if(Mage::helper('customer')->isLoggedIn()){
				$cart   = Mage::getSingleton('checkout/cart');
				$params = $this->getRequest()->getParams();
				$related = $this->getRequest()->getParam('related_product');
				
				$productId = (int) $this->getRequest()->getParam('product');
				
				//查询这个客户是否在一个月中兑换过礼品
				$month_day = Mage::helper('points')->getthemonth(date('Y-m-d')); 
				$already_has_gift_order = Mage::helper('points')->PostErpGetCanPlaceInMonth($month_day);
				if($already_has_gift_order == false){
					if ($productId) {
						$product = Mage::getModel('catalog/product')
							->setStoreId(Mage::app()->getStore()->getId())
							->load($productId);
						try {

							if (!isset($params['qty'])) {
								$params['qty'] = 1;
							}
							
							//查询客户积分是否够用加入此商品
							if($product->getTypeId() == 'configurable'){
								$childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($params['super_attribute'], $product);
								$can_add = Mage::helper('points')->CanAddThisProductByPoints($childProduct, $params['qty']);
							}else{
								$can_add = Mage::helper('points')->CanAddThisProductByPoints($product, $params['qty']);
							}
							
							//粉丝不允许加入购物车（暂时）
							if(Mage::getSingleton('customer/session')->getCustomer()->getVipLevel() == '粉丝'){
								
								Mage::getSingleton('checkout/session')->addError($this->__('Sorry, the FANS level cannot exchange any gifts.'));		
								
							}else{
								// Mage::log($can_add);
								if($can_add){
									$cart->addProduct($product, $params);
									if (!empty($related)) {
										$cart->addProductsByIds(explode(',', $related));
									}
									$cart->save();
									
									
									$this->_getSession()->setCartWasUpdated(true);


									Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
									Mage::getSingleton('checkout/session')->setCartInsertedItem($product->getId());

									$img = '';
									Mage::dispatchEvent('checkout_cart_add_product_complete', array('product'=>$product, 'request'=>$this->getRequest()));

									$photo_arr = explode("x",Mage::getStoreConfig('j2tajaxcheckout/default/j2t_ajax_cart_image_size', Mage::app()->getStore()->getId()));

									$prod_img = $product;
									if($product->isConfigurable() && isset($params['super_attribute'])){
										$attribute = $params['super_attribute'];
										if (Mage::getStoreConfig(self::CONFIGURABLE_PRODUCT_IMAGE) != self::USE_PARENT_IMAGE) {
											$prod_img_temp = Mage::getModel("catalog/product_type_configurable")->getProductByAttributes($attribute, $product);
											if ($prod_img_temp->getImage() != 'no_selection' && $prod_img_temp->getImage()){
												$prod_img = $prod_img_temp;
											}
										}
									}
									$img = '<img src="'.Mage::helper('catalog/image')->init($prod_img, 'thumbnail')->resize($photo_arr[0],$photo_arr[1]).'" width="'.$photo_arr[0].'" height="'.$photo_arr[1].'" />';
									$message = $this->__('%s was successfully added to your gift cart.', $product->getName());
									$messageB = $this->__('Warm tips:');
									$message1 = $this->__('As the quantity of the gift is changing all the time , exchange result is only associated with the success status of the order.');
									Mage::getSingleton('checkout/session')->addSuccess('<div class="j2tajax-checkout-img">'.$img.'</div><div class="j2tajax-checkout-txt">'.$message.'</div>'.'<div class="j2tajax-sucess-add">' . '<b>' . $messageB .'</b>' . $message1 .'</div>'
									);
								}else{
									Mage::getSingleton('checkout/session')->addError($this->__('You Have Not Enough Points To Exchange This Product'));			
									
								}
							
							
							}
							
							
							
							
							
						}
						catch (Mage_Core_Exception $e) {
							if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
								Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
							} else {
								$messages = array_unique(explode("\n", $e->getMessage()));
								foreach ($messages as $message) {
									Mage::getSingleton('checkout/session')->addError($message);
								}
							}
						}
						catch (Exception $e) {
							Mage::getSingleton('checkout/session')->addException($e, $this->__('Can not add item to shopping cart'));
						}

					}
				}else{
					Mage::getSingleton('checkout/session')->addError($this->__('You Have Already Exchange Product In This Month'));				
				}
				
			
			
			}else{
			
				Mage::getSingleton('checkout/session')->addError($this->__('You Must Login.'));
			}
            
        }
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');

        $this->renderLayout();
    }


    public function productoptionAction()
    {
        //getProductUrlSuffix
        echo 'test';
        die;
    }
    
    public function productcheckAction()
    {
        $params = $this->getRequest()->getParams();
        
        $productTypes = array(
            Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
            Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
            Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
        );
        
        $storeId = Mage::app()->getStore()->getId();
        
        if($product_id = $params['product']){
            
            $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product_id);
            if ($product->getId()){
                if($product->getHasOptions() || in_array($product->getTypeId(), $productTypes)){
                    //echo get product url
                    echo $product->getProductUrl();
                    die;
                }
            }
        }
        echo 0;
        die;
    }

    public function addtocartAction()
    {
        $this->indexAction();
    }



    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
    }


}