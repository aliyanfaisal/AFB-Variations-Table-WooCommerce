<?php



function afb_custom_post_type()
{

    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x('Pricing Table Tieres', 'Post Type General Name', 'twentytwentyone'),
        'singular_name' => _x('Pricing Table Tiere', 'Post Type Singular Name', 'twentytwentyone'),
        'menu_name' => __('Pricing Table Tieres', 'twentytwentyone'),
        'parent_item_colon' => __('Parent Pricing Table Tiere', 'twentytwentyone'),
        'all_items' => __('All Pricing Table Tieres', 'twentytwentyone'),
        'view_item' => __('View Pricing Table Tiere', 'twentytwentyone'),
        'add_new_item' => __('Add New Pricing Table Tiere', 'twentytwentyone'),
        'add_new' => __('Add New', 'twentytwentyone'),
        'edit_item' => __('Edit Pricing Table Tiere', 'twentytwentyone'),
        'update_item' => __('Update Pricing Table Tiere', 'twentytwentyone'),
        'search_items' => __('Search Pricing Table Tiere', 'twentytwentyone'),
        'not_found' => __('Not Found', 'twentytwentyone'),
        'not_found_in_trash' => __('Not found in Trash', 'twentytwentyone'),
    );

    // Set other options for Custom Post Type

    $args = array(
        'label' => __('Pricing Table Tieres', 'twentytwentyone'),
        'description' => __('Pricing Table Tieres', 'twentytwentyone'),
        'labels' => $labels,
        // Features this CPT supports in Post Editor
        'supports' => array('title'),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        // 'taxonomies'          => array( 'genres' ),
        /* A hierarchical CPT is like Pages and can have
         * Parent and child items. A non-hierarchical CPT
         * is like Posts.
         */
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'show_in_rest' => true,

    );

    // Registering your Custom Post Type
    register_post_type('pricing_table_tieres', $args);

}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not 
 * unnecessarily executed. 
 */

add_action('init', 'afb_custom_post_type');




/**
 * 
 * ADD META BOXES
 * 
 */


// Add number meta box to custom post type
function add_custom_meta_boxes()
{
    add_meta_box(
        'afb_number_meta_box',
        'Tier Details',
        'render_afb_number_meta_box',
        'pricing_table_tieres',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_custom_meta_boxes');

// Render number meta box
function render_afb_number_meta_box($post)
{
    // Retrieve the current value of the meta box
    $post_meta = get_post_meta($post->ID);
	
// 	echo "<pre>";
// 	print_r($post_meta);
// 	echo "</pre>";

    // Output the meta box HTML

    ?>
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }
		
		.m_flex{
			display: flex;
			margin-bottom:20px;
			margin-top:20px;
			border-bottom: 1px solid #ddd;
		}
		
		.m_flex > div{
			display: flex;
			flex-direction: column;
			margin-right: 30px;
		}
		
		label{
			font-weight: bold;
			font-size:16px;
		}
    </style>

<p>
	<strong>Note:</strong> These are the Tier Levels for all products. You cant create Different Tiers for Different Product. <br>
	Create Tier and assign <b>Default </b>discounts on every Tier.<br>
	<b>You can override these Discounts for every product from Edit Product page in WooCommerce. </b>
</p>
<hr>
    <div>
		<label>Status: </label>
        <label class="switch"> 
            <input autocomplete='false' <?php echo isset($post_meta['afb_tier_status'][0]) ? ($post_meta['afb_tier_status'][0]=="on" ? "checked":"") : "" ?> name="afb_tier_status" type="checkbox">
            <span class="slider round"></span>
        </label>
	
    </div>

<hr>
    <?php
	
    for ($i = 1; $i <= 4; $i++):
        ?>

        <div class="m_flex">
            <div>
                <label for="afb_tier_<?php echo $i; ?> _qty">Tier <?php echo $i; ?> Quantity:</label>
                <input required type="number" id="afb_tier_<?php echo $i; ?>_qty" name="afb_tier_<?php echo $i; ?>_qty"
                    value="<?php echo $post_meta['afb_tier_' . $i . '_qty'][0]; ?>" />
            </div>

            <div>
                <label for="afb_tier_<?php echo $i; ?> _discount">Tier <?php echo $i; ?> Discount ( % / fixed ) :</label>
                <input type="text" id="afb_tier_<?php echo $i; ?> _discount" name="afb_tier_<?php echo $i; ?>_discount"
                    value="<?php echo $post_meta['afb_tier_' . $i . '_discount'][0]; ?>"  value="0"/>
                <span>E.g 10% , 200 etc</span>
            </div>
        </div>


        <?php
    endfor;
}

// Save number meta box data
function afb_save_pricing_table_tieres($post_id)
{
    // Check if the current user is authorized to save the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    foreach ($_POST as $key => $field) {
		
		$status_on=false;
        if (str_contains($key, "afb_")) {
				
			if($key=="afb_tier_status" && $_POST['afb_tier_status']=="on"){
				$status_on=true;
			}
            update_post_meta($post_id, $key, $field);
        }
		
		if(!$status_on){
			update_post_meta($post_id, "afb_tier_status", "off");
		}

    }

}
add_action('save_post_pricing_table_tieres', 'afb_save_pricing_table_tieres');



// Add custom column to post list
add_filter('manage_pricing_table_tieres_posts_columns', 'add_custom_column');
function add_custom_column($columns) {
    $columns['afb_tier_status'] = 'Active';
    return $columns;
}

// Populate custom column with meta field value
add_action('manage_pricing_table_tieres_posts_custom_column', 'afb_populate_custom_column', 10, 2);
function afb_populate_custom_column($column_name, $post_id) {
    if ($column_name === 'afb_tier_status') {
        $meta_value = get_post_meta($post_id, 'afb_tier_status', true);
        
		if($meta_value=="on"){
			echo '<svg style="width:30px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
			  <path d="M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18zm0-2a7 7 0 1 0 0-14 7 7 0 0 0 0 14zm-2-6l-2-2-1.4 1.4 3.4 3.4 7-7-1.4-1.4z"/>
			</svg>';
		}
		else{
			echo '<svg style="width:26px;" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
				width="50" height="50"
				viewBox="0 0 50 50">
					<path d="M25,2C12.317,2,2,12.318,2,25s10.317,23,23,23s23-10.318,23-23S37.683,2,25,2z M7,25c0-4.062,1.371-7.8,3.65-10.815 L35.815,39.35C32.8,41.629,29.062,43,25,43C15.075,43,7,34.925,7,25z M39.35,35.815L14.185,10.65C17.2,8.371,20.938,7,25,7 c9.925,0,18,8.075,18,18C43,29.062,41.629,32.8,39.35,35.815z"></path>
				</svg>';
		}
    }
}
