<?php

add_action( 'wp_ajax_afb_price_table', 'ajax_afb_price_table' );
add_action( 'wp_ajax_nopriv_afb_price_table', 'ajax_afb_price_table' );


function ajax_afb_price_table(){

	$form_data= $_POST;
	$cart_items=[];
	foreach($form_data['products'] as $var_id=>$variation){
		
	    if ( isset( $form_data['product_id'] ) && isset( $var_id ) && isset( $variation['qty'] ) ) {
			$product_id = intval( $form_data['product_id'] );
			$variation_id = intval( $var_id );
			$quantity = intval( $variation['qty'] );

			// Add variation to cart with quantity
			$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );

			// Return cart item data in JSON format
			$cart_item = WC()->cart->cart_contents[ $cart_item_key ];
			
			$cart_items[]=$cart_item;
			
		} else {
			
		}

		
	}
	
	if(count($cart_items)>0){
		wp_send_json_success( $cart_items );
	}
	else{
		wp_send_json_error( 'Invalid request' );
	}
	

	
}




// Set price of cart item using $cart_item_key
function afb_set_cart_item_price( $cart_object ) {
	
	$tiers=getActiveTiers();
	$tier_qtys=array_keys($tiers);

	
    foreach ( $cart_object->cart_contents as $cart_item_key => $cart_item ) {
        // Replace 'YOUR_CART_ITEM_KEY' with the actual cart item key
        // 
        $variation_id= $cart_item['variation_id'];
		$qty= $cart_item['quantity'];
		
		if($variation_id){
			
			$variation_tiers_discounts= getProductTiers($variation_id);
			
			$discount_qty= find_nearest_number($tier_qtys,$qty);
			
			if($discount_qty){
				
				$ind=  array_search($discount_qty, array_keys($tiers));
				$discount_amount= $variation_tiers_discounts[$ind+1];
				
				if(""==$discount_amount){
					
					$discount_amount= $tiers[$discount_qty];
				}
				
				// calculate the actual unit price for this qty
				$price = get_post_meta($variation_id, '_price', true);

				if (!$price) {
					// If _price meta key is not set, fallback to _regular_price meta key
					$price = get_post_meta($variation_id, '_regular_price', true);
				}
				
				
				$val=0;
				if($discount_amount=="0%"){
					$val=0;
				}
				elseif( str_contains($discount_amount,"%") ){
					$val= ( $price * $discount_amount) / 100;
				}
				else{
					$val=$discount_amount;
				}
				
				$final_per_unit_price= $price- $val;
				
				$cart_item["data"]->set_price( $final_per_unit_price  );
				
			}
			
		}
        if ( $cart_item_key === 'YOUR_CART_ITEM_KEY' ) {
            // Set the new price for the cart item
            $new_price = 100; // Replace with your desired price
            $cart_item['data']->set_price( $new_price );
        }
    }
}
add_action( 'woocommerce_before_calculate_totals', 'afb_set_cart_item_price', 10, 1 );




/**
 * 
 * Functions here
 * */


function getActiveTiers(){
	
	$args = array(
		'post_type' => 'pricing_table_tieres',
		'meta_key' => 'afb_tier_status',
		'meta_value' => 'on',
		'posts_per_page' => 1, // limit to only one post
		'orderby' => 'date',
    	'order' => 'DESC'
	);

	$query = new WP_Query( $args );

	$tiers="";
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$tiers = get_post_meta(get_the_ID());
			// Do something with the post here
		}
	} else {
		// No posts found
	}

	wp_reset_postdata();


	$tier_qtys=[];
	if($tiers!=""){
		
		foreach($tiers as $key=>$tier){
			
			if ( preg_match( '/^afb_tier_\d+_qty$/', $key ) ) {
				
				$index= intval( substr( $key, strpos($key,"tier_")+5 ,1 ) );
				
				$tier_qtys[intval($tier[0])]= $tiers['afb_tier_'.$index.'_discount'][0];
				
			}
			
		}
		
	}
	else{
		return false;	
	}
	
	return $tier_qtys;
	
	
}



function getProductTiers($variation_id){
	
	$variation_meta=  get_post_meta( $variation_id );
	$tier_qtys=[];
		foreach($variation_meta as $key=> $v_meta){
			
			if ( preg_match( '/afb_tier_\d+_discount$/', $key ) ) {
				
				$index= intval( substr( $key, strpos($key,"tier_")+5 ,1 ) );
				
				$tier_qtys[$index]= ($v_meta[0]) ;
				
			}
		}
	
	return $tier_qtys;
}



function find_nearest_number($arr, $num) {
    $min_diff = INF;
    $nearest = null;
    
    foreach ($arr as $value) {
        $diff = $num - $value;
        if ($diff > 0 && $diff < $min_diff) {
            $min_diff = $diff;
            $nearest = $value;
        }
    }
    
    return $nearest;
}
