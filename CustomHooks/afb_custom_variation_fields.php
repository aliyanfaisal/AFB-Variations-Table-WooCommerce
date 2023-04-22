<?php
add_action( 'woocommerce_product_after_variable_attributes', 'rudr_fields', 10, 3 );

function rudr_fields( $loop, $variation_data, $variation ) {
	//get tiers 
	$args = array(
		'post_type' => 'pricing_table_tieres',
		'meta_key' => 'afb_tier_status',
		'meta_value' => 'on',
		'posts_per_page' => 1, // limit to only one post
		'orderby' => 'date',
    	'order' => 'DESC'
	);

	$query = new WP_Query( $args );

	$meta_value="";
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$meta_value = get_post_meta(get_the_ID());
			// Do something with the post here
		}
	} else {
		// No posts found
	}

	wp_reset_postdata();

	
	

	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field[' . $loop . '_rudr_pack_qty]',
			'label'         => 'Pack Quantity',
			"type"			=> "number",
			'wrapper_class' => 'form-row',
			'placeholder'   => 'Type Pack Quantity here...',
			'desc_tip'      => 'true',
			'description'   => 'Add Pack Quantity.',
			'value'         => get_post_meta( $variation->ID, '_rudr_pack_qty', true )
		)
	);
	

	
	 for ($i = 1; $i <= 4; $i++){
		 $this_val=get_post_meta( $variation->ID,  $loop . '_afb_tier_'.$i.'_discount', true );
			woocommerce_wp_text_input(
				array(
					'id'            => 'text_field[' . $loop . '_afb_tier_'.$i.'_discount]',
					'label'         => '(<strong>'.( $meta_value!="" ? $meta_value['afb_tier_'.$i.'_qty'][0]: "" ).'</strong>) Tier '.$i.' Discount ( % / fixed ) <b>Note</b>: put % with percentages',
					"type"			=> "text",
					'wrapper_class' => 'form-row',
					'placeholder'   => 'e.g 10% or 400 etc',
					'desc_tip'      => 'true',
					'description'   => 'Add Discount for this Tier in % or fixed amount.',
					'value'         => $this_val =="" ? ( $meta_value!="" ? $meta_value['afb_tier_'.$i.'_discount'][0] : "" ) : $this_val
				)
			);
			
		}
	
	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field[' . $loop . '_rudr_qty_per_pallet]',
			'label'         => 'Quantity Per Pallet',
			'type'			=> 'number',
			'wrapper_class' => 'form-row',
			'placeholder'   => 'Type Qty here...',
			'desc_tip'      => 'false',
			'value'         => get_post_meta( $variation->ID, '_rudr_qty_per_pallet', true )
		)
	);
	
	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field[' . $loop . '_rudr_discount_per_pallet]',
			'label'         => 'Discount Per Pallet ( % / fixed ) <b>Note</b>: put % with percentages',
			'type'			=> 'text',
			'wrapper_class' => 'form-row',
			'placeholder'   => 'Type Discount here...',
			'desc_tip'      => 'false',
			'value'         => get_post_meta( $variation->ID, '_rudr_discount_per_pallet', true )
		)
	);
	
	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field[' . $loop . '_rudr_board_grade]',
			'label'         => 'Board Grade',
			'wrapper_class' => 'form-row',
			'placeholder'   => 'Type Grade here...',
			'desc_tip'      => 'true',
			'description'   => 'Add Board Grade.',
			'value'         => get_post_meta( $variation->ID, '_rudr_board_grade', true )
		)
	);
	
	
	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field[' . $loop . '_rudr_style]',
			'label'         => 'Style',
			'wrapper_class' => 'form-row',
			'placeholder'   => 'Style Here...',
			'desc_tip'      => 'true',
			'description'   => 'Add Style.',
			'value'         => get_post_meta( $variation->ID, '_rudr_style', true )
		)
	);

	


// 	// Select
// 	woocommerce_wp_select(
// 		array(
// 			'id'            => 'select_field[' . $loop . ']',
// 			'label'         => 'Select field',
// 			'wrapper_class' => 'form-row',
// 			'description'   => 'We can add some description for a field.',
// 			'value'         => get_post_meta( $variation->ID, 'rudr_select', true ),
// 			'options'       => array(
// 				'one'   => 'Option 1',
// 				'two'   => 'Option 2',
// 				'three' => 'Option 3'
// 			)
// 		)
// 	);

// 	woocommerce_wp_radio(
// 		array(
// 			'id'            => 'radio_field[' . $loop . ']',
// 			'label'         => 'Radio field',
// 			'wrapper_class' => 'form-row',
// 			'value'         => get_post_meta( $variation->ID, 'rudr_radio', true ),
// 			'options'       => array(
// 				'one'   => 'Option 1',
// 				'two'   => 'Option 2',
// 				'three' => 'Option 3'
// 			)
// 		)
// 	);

// 	woocommerce_wp_checkbox(
// 		array(
// 			'id'            => 'my_check[' . $loop . ']',
// 			'label'         => 'Checkbox field',
// 			'wrapper_class' => 'form-row',
// 			'value'         => get_post_meta( $variation->ID, 'rudr_check', true ),
// 		)
// 	);



}





add_action( 'woocommerce_save_product_variation', 'rudr_save_fields', 10, 2 );

function rudr_save_fields( $variation_id, $loop ) {

	// Text Field
	$text_field = ! empty( $_POST[ 'text_field' ][ $loop ."_rudr_pack_qty"] ) ? $_POST[ 'text_field' ][ $loop."_rudr_pack_qty" ] : '';
	update_post_meta( $variation_id, '_rudr_pack_qty', sanitize_text_field( $text_field ) );
	
// 	$text_field = ! empty( $_POST[ 'text_field' ][ $loop ."_rudr_discount_per_pack"] ) ? $_POST[ 'text_field' ][ $loop."_rudr_discount_per_pack" ] : '';
// 	update_post_meta( $variation_id, '_rudr_discount_per_pack', sanitize_text_field( $text_field ) );
	
	$text_field = ! empty( $_POST[ 'text_field' ][ $loop."_rudr_qty_per_pallet" ] ) ? $_POST[ 'text_field' ][ $loop ."_rudr_qty_per_pallet"] : '';
	update_post_meta( $variation_id, '_rudr_qty_per_pallet', sanitize_text_field( $text_field ) );
	
	$text_field = ! empty( $_POST[ 'text_field' ][ $loop."_rudr_discount_per_pallet" ] ) ? $_POST[ 'text_field' ][ $loop ."_rudr_discount_per_pallet"] : '';
	update_post_meta( $variation_id, '_rudr_discount_per_pallet', sanitize_text_field( $text_field ) );
	
	$text_field = ! empty( $_POST[ 'text_field' ][ $loop."_rudr_board_grade" ] ) ? $_POST[ 'text_field' ][ $loop ."_rudr_board_grade"] : '';
	update_post_meta( $variation_id, '_rudr_board_grade', sanitize_text_field( $text_field ) );
	
	$text_field = ! empty( $_POST[ 'text_field' ][ $loop."_rudr_style" ] ) ? $_POST[ 'text_field' ][ $loop ."_rudr_style"] : '';
	update_post_meta( $variation_id, '_rudr_style', sanitize_text_field( $text_field ) );
	
	
	
	foreach($_POST[ 'text_field' ] as $key=>$field){
		
		 if (str_contains($key, "afb_")) {

            update_post_meta($variation_id, $key,sanitize_text_field( $field ) );
        }
	
	}


}


// <!-- 
// add_filter( 'woocommerce_available_variation', function( $variation ) {

// 	$variation[ 'text_field_anything' ] = get_post_meta( $variation[ 'variation_id' ], 'rudr_text', true );
// 	return $variation;

// } ); -->

