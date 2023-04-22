<?php

function afb_price_table(){
	
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

	
	
ob_start();	
	
?>

<link rel="stylesheet" href="<?php echo AFB_base_url."/CustomHooks/customcss.css" ?>">

<style>
	td,th{
		color: rgb(80,80,80); 
		border: 1px solid rgb(80,80,80);
		font-size: 16px
	}
</style>
<div class="elemetor-woo-price-table-single-product-wrap">
    <form method="post" id="process-to-cart">
<!--         <input type="hidden" id="nonce-add-cart" name="nonce-add-cart" value="0c3a170398"> -->
		<?php 
	wp_nonce_field("upc_add_cart","nonce-add-cart");
	?>
		<input type="hidden" name="product_id"
            value="<?php echo get_the_ID(); ?>">
<!-- 		<input type="hidden"
            name="_wp_http_referer" value="/product/single-wall-boxes/">  -->
        <input type="hidden" name="action" value="afb_price_table">
        <table>
            <tbody>
                <tr>
                    <th rowspan="2"><strong>Ref</strong></th>
                    <th rowspan="2">Internal (LxWxH) mm
                    </th>
                    <th rowspan="2">Internal (LxWxH) inches
                    </th>
                    <th rowspan="2">Board Grade
                    </th>
                    <th rowspan="2">Style
                    </th>
                    <th rowspan="2">Pack Quantity</th>
                    <!-- <th rowspan="2">Packs Per Pallet</th> -->
                    <th colspan="5" rowspan="1">Price ex. VAT per box</th>
                    <th rowspan="2">Qty Per Pallet</th>

                    <th rowspan="2" style="width:150px;"><strong>Quantity</strong></th>
                </tr>
                <tr>
					
                    <th class="price-label-elm" rowspan="1"> <?php echo $tiers=="" ? 25: ($tiers['afb_tier_1_qty'][0]) ?>+</th>
                    <th class="price-label-elm" rowspan="1"><?php echo $tiers=="" ? 50: ($tiers['afb_tier_2_qty'][0]) ?>+</th>
                    <th class="price-label-elm" rowspan="1"><?php echo $tiers=="" ? 100: ($tiers['afb_tier_3_qty'][0]) ?>+</th>
                    <th class="price-label-elm" rowspan="1"><?php echo $tiers=="" ? 500: ($tiers['afb_tier_4_qty'][0]) ?>+</th>
                    <th class="price-label-elm" rowspan="1">Pallet</th>
                </tr>


				
				<?php
	
	$product = new WC_Product_Variable(get_the_ID());
	// Get the variations
	$variations = $product->get_available_variations();
	$currency_symbl= get_woocommerce_currency_symbol();
	foreach( $variations as $variation):
		$vari_id=$variation["variation_id"];
		$variation_meta=  get_post_meta( $variation["variation_id"]);
		
		$dimensions= $variation['dimensions'];
		$dim_in_mm= "";
		$dim_in_inches="";
		$key=1;
		foreach( $dimensions as $dimension){
			$dim_in_mm .= $dimension *10;
			$dim_in_inches .= intval($dimension/2.54);
			
 			if($key != count($dimensions)){
				$dim_in_mm .="X";
				$dim_in_inches .= "X";
			}
			$key++;
		}
	
		
	
	// calculate the actual discount amount to minus from regular price
		$variation_tier_discounts=[];
		$no_found_var_tiers_discounts=[1,2,3,4];
		foreach($variation_meta as $key=> $v_meta){
			
			if( str_contains($key, "_discount") && str_contains($key, "_afb_tier_") ){
				
				$i_here= substr( $key, strpos($key,"tier_") +5 , 1 );

				if (($keyx = array_search($i_here, $no_found_var_tiers_discounts)) !== false) {
					unset($no_found_var_tiers_discounts[$keyx]);
				}
// 				
				$val=0;
				if($v_meta[0]=="0%"){
					$val=0;
				}
				elseif( str_contains($v_meta[0],"%") ){
					$val= ($variation["display_price"] * $v_meta[0]) /100;
				}
				else{
					$val=$v_meta[0];
				}
				
				$variation_tier_discounts[  substr( $key, strpos($key,"afb_tier_")) ]= $val;
			}
		}

	
	foreach($no_found_var_tiers_discounts as $not_found){
		
		$val_from_def= $tiers['afb_tier_'.$not_found.'_discount'][0];
		
		$val=0;
		if($val_from_def=="0%"){
			$val=0;
		}
		elseif( str_contains($val_from_def,"%") ){
			$val= ($variation["display_price"] * $val_from_def) /100;
		}
		else{
			$val=$val_from_def;
		}
		
		$variation_tier_discounts["afb_tier_".$not_found."_discount"]= $val;
		
	}
	
// 	echo "<pre>";
// 	print_r( $variation_tier_discounts);
// 	echo "<pre>";
	
				?>

                <tr style="border: solid #ddd; !important; border-width: 1px 0px; !important;">
                    <td>
                        <strong><?php echo $variation["sku"]; ?></strong>
                    </td>
                    <td><?php echo $dim_in_mm; ?> </td>
                    <td><?php echo $dim_in_inches; ?></td>
                    <td><?php echo isset($variation_meta['_rudr_board_grade'][0]) ? $variation_meta['_rudr_board_grade'][0] : ""; ?></td>
                    <td> <?php echo isset($variation_meta['_rudr_board_grade'][0]) ? $variation_meta['_rudr_board_grade'][0] : ""; ?> </td>
                    <td> <?php echo isset($variation_meta['_rudr_pack_qty'][0]) ? $variation_meta['_rudr_pack_qty'][0] : ""; ?> </td>
                    <td class="price-elm" style="width: 75px;">
                        <?php echo  $currency_symbl. number_format($variation["display_price"] - $variation_tier_discounts["afb_tier_1_discount"], 2,".",","); ?>
                    </td>
                    <td class="price-elm" style="width: 75px;">
                        <strong> <?php echo  $currency_symbl.  number_format($variation["display_price"] - $variation_tier_discounts["afb_tier_2_discount"], 2,".",","); ?> </strong>
                    </td>
                    <td class="price-elm" style="width: 75px;">
                        <strong> <?php echo  $currency_symbl.  number_format($variation["display_price"] - $variation_tier_discounts["afb_tier_3_discount"], 2,".",",") ?>  </strong>
                    </td>
                    <td class="price-elm" style="width: 75px;">
                        <strong>  <?php echo  $currency_symbl.  number_format($variation["display_price"] - $variation_tier_discounts["afb_tier_4_discount"], 2,".",",") ?> </strong>
                    </td>
                    <td class="price-elm" style="width: 75px;">
                        <strong>  <?php echo $currency_symbl.number_format( $variation["display_price"] - ( (isset($variation_meta['_rudr_discount_per_pallet'][0]) && $variation_meta['_rudr_discount_per_pallet'][0]!="" ) ? ( ($variation["display_price"] * $variation_meta['_rudr_discount_per_pallet'][0] ) /  100  ) : 0 ),2,".",",") ?> </strong>
                    </td>
                    <td>
                        <a class="btn-add-pack-qty" data-id="<?php echo $vari_id; ?>" data-qty="<?php echo isset($variation_meta['_rudr_qty_per_pallet'][0]) ? $variation_meta['_rudr_qty_per_pallet'][0] : ""; ?> " href="#"> <?php echo isset($variation_meta['_rudr_qty_per_pallet'][0]) ? $variation_meta['_rudr_qty_per_pallet'][0] : ""; ?> </a>
                    </td>
					
                    <td class="qty-wrapper">
						<a class="btn-qty minus" data-qty="<?php echo isset($variation_meta['_rudr_pack_qty'][0]) ? $variation_meta['_rudr_pack_qty'][0] : ""; ?>" href="#">-</a>
						
						<input data-id="<?php echo $vari_id; ?>" type="text" value="0" class="input-text qty text" name="products[<?php echo $vari_id; ?>][qty]">
						
						<a class="btn-qty plus" data-qty="<?php echo isset($variation_meta['_rudr_pack_qty'][0]) ? $variation_meta['_rudr_pack_qty'][0] : ""; ?>" href="#">+</a>
					</td>
                </tr>

				<?php
		endforeach;
	?>
            </tbody>
        </table>
        <div class="text-wrap">
            <button class="button" type="submit">Add to Basket</button>
        </div>
    </form>
    <script>jQuery(document).ready(function () {
    jQuery(".btn-add-pack-qty").click(function (e) {
        e.preventDefault();
        var id = jQuery(this).data("id");
        var qty = parseInt(jQuery(this).data("qty"));
        var pQty = parseInt(jQuery('input[data-id="' + id + '"]').val());
        pQty += qty;
        jQuery('input[data-id="' + id + '"]').val(pQty);
    });
    jQuery(".btn-qty").click(function (e) {
        e.preventDefault();
        var input = jQuery(this).parent().find('input[type="text"]');
        var qty = parseInt(input.val() ? input.val() : 0);
        var dataQty = parseInt(jQuery(this).data("qty"));
        if (jQuery(this).hasClass("minus")) {
            if (qty != 0) {
                qty -= dataQty;
            }
            if (qty < 0) {
                qty = 0;
            }
        } else {
            qty += dataQty;
        }
        jQuery(input).val(qty);
    });
    jQuery("#process-to-cart").submit(function (event) {
        event.preventDefault();
        var form = new FormData(event.target);
		console.log("form data", event.target, form)
		var ajaxurl= "https://molane.biz/wp-admin/admin-ajax.php"
        jQuery.ajax({
            url: ajaxurl,
            contentType: false,
            processData: false,
            method: "POST",
            beforeSend: function (xhr) {
                show_message("Please wait..", false);
            },
            data: form,
            success: function (data) {
                show_message("Products Added Successfully...", false);
                window.location = "/cart";
            },
            error: function (data) {
                console.log(data);
                show_message("errors are: " + data);
            },
        });
    });
			
			
	jQuery(document).ready(function(){jQuery('body').on('click','.close-message',function(e){e.preventDefault();jQuery.unblockUI();});})
});
		
 function show_message(message, closebutton) {
                closebutton = typeof closebutton !== 'undefined' ? closebutton : true;
                if (closebutton)
                    message += '<br><br><p><a href="#" class="button close-message">Close</a></p>';
                jQuery.blockUI({
                    message: message,
                    css: {
                        border: '10px solid #0644ce',
                        padding: '30px 20px',
                        backgroundColor: 'rgba(255,255,255,1)',
                        color: '#000',
                        fontSize: '18px',
                        zIndex: '99999',
                        borderRadius: '10px'
                    }
                });
            }
</script>
</div>
<?php
	
	$content= ob_get_contents();
	ob_clean();
	
	echo $content;
}
/**
 * 
 * HOOK over Woocommerce Product page to show the table
 */

add_action( 'woocommerce_after_single_product_summary', 'afb_price_table', 1 );






add_shortcode("afb_get_variation", function($args){

	
	if(!isset($args['vari_id']) || !isset($args['vari_field'])){
		return false;
	}
	$variation_id=$args['vari_id'];
	$variation = wc_get_product($variation_id);

	// Get the parent product ID
	$product_id = $variation->get_parent_id();

	$product = new WC_Product_Variable($product_id);
	// Get the variations
	$variations = $product->get_available_variations();

	ob_start();
	// Loop through each variation
	foreach ($variations as $variation) {
		  // Get the variation ID and attributes
	 	$variation_id = $variation['variation_id'];
		if($args['vari_id']!=$variation_id){
			continue;
		}
	  // Get the variation ID and attributes
	  echo $variation[$args['vari_field']];
	  
	
	}
	
	$content= ob_get_contents();
	ob_clean();
	
	return $content;
});
