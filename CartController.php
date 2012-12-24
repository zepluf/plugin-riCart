<?php

namespace plugins\riCart;

use Symfony\Component\HttpFoundation\Request;
use Zepluf\Bundle\StoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function ajaxAddToCartAction(Request $request){
        $_SESSION['cart']->addCart($request->get('products_id'), $request->get('cart_quantity'), $request->get('id'));


        return $this->renderJson($this->_getCart());
    }

    public function ajaxRemoveFromCartAction(Request $request){
        global $currencies;

        $_SESSION['cart']->remove($request->get('id'));

        return $this->renderJson($this->_getCart());
    }

    public function ajaxGetCartAction(){
        return $this->renderJson((array(
                    'box_cart_content' => $this->view->render("riCart::_box_shopping_cart.php"),
                    'messages' => $this->container->get('riLog.Logs')->getAsArray())
            )
        );
    }

    public function ajaxEditCartAction(Request $request){
        $totalParams = $request->get('totalParams');
        for($i=0; $i<$totalParams; $i++){
            $param = explode('|', $request->get('param'.$i));
            $_SESSION['cart']->contents[$param[0]][qty] = $param[1];
        }
        return $this->renderJson(
            array(
                'bool'=> true,
            ));
    }

    private function _getCart(){
        global $currencies;
        $products = array();

        foreach ($_SESSION['cart']->get_products() as $product) {
            $products_id = zen_get_prid($product['id']);
            $product['productsLink'] = zen_href_link(zen_get_info_page($products_id), 'products_id=' . $products_id);
            $product['display_price'] = $currencies->format($product['price'] * $product['quantity']);
            $product['productsImage'] = zen_image(DIR_WS_IMAGES . $product['image'], $product['name'], '66', '66');
            $products[] = $product;
        }

        return array(
            'products' => $products,
            'total' => $currencies->format($_SESSION['cart']->show_total())
        );
    }
}