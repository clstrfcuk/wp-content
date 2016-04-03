<?php
/**
 * @package   The_Grid
 * @author    Themeone <themeone.master@gmail.com>
 * @copyright 2015 Themeone
 */

// Exit if accessed directly
if (!defined('ABSPATH')) { 
	exit;
}

class The_Grid_Register_Item_Skin {

    private $skins = array();

    function __construct() {
        $this->skins = apply_filters( 'tg_register_item_skin', $this->skins );
		$register = array();
		if (is_array($this->skins)) {
			foreach($this->skins as $skin => $data) {
				$name = (is_array($data) && !empty($data)) ? $skin : $data;
				$data = (is_array($data) && !empty($data)) ? $data : '';		
				$register[$name] = array(
					'name'   => $name,
					'filter' => (isset($data['filter']) && !empty($data['filter'])) ? $data['filter'] : 'Standard',
					'col'    => (isset($data['col']) && !empty($data['col'])) ? $data['col'] : 1,
					'row'    => (isset($data['row']) && !empty($data['row'])) ? $data['row'] : 1,
				);
			}
			$this->skins = $register;
		} else {
			$this->skins = array();
		}

    }

	// get skin array
	function get_registered_skins() {
		return $this->skins;
    }

}

add_filter('tg_register_item_skin', function($skins){
	
	$skins = array(
		// grid skins
		'alofi',
		'apia',
		'bogota' => array(
			'filter' => 'Instagram'
		),
		'brasilia',
		'camberra',
		'caracas',
		'dacca',
		'honiara',
		'lisboa',
		'lome',
		'malabo',
		'male',
		'maputo' => array(
			'filter' => 'Instagram'
		),
		'oslo',
		'podgorica' => array(
			'filter' => 'Youtube/Vimeo'
		),
		'pracia',
		'roma',
		'sofia',
		'suva' => array(
			'filter' => 'Woocommerce'
		),
		// masonry skins
		'doha',
		'kampala',
		'lima',
		'lusaka',
		'maren',
		'panama',
		'praia',
		'quito',
		'riga',
		'sanaa' => array(
			'filter' => 'Woocommerce'
		),
		'victoria' => array(
			'filter' => 'Youtube/Vimeo'
		),
		'vaduz' => array(
			'filter' => 'Youtube/Vimeo'
		),
	);
	
	return $skins;
	
});
 
class The_Grid_Item_Skin {

    private $skins = array();

    function __construct() {
        $this->skins = apply_filters( 'tg_add_item_skin', $this->skins );
    }

	// get skin array
	function get_skin_names() {
		return $this->skins;
    }

}

add_filter('tg_add_item_skin', function($skins){
	
	$plugin_uri    = TG_PLUGIN_URL;
	$plugin_path   = TG_PLUGIN_PATH;
	$theme_uri     = get_stylesheet_directory_uri();
	$theme_path    = get_stylesheet_directory();
	$wp_upload_dir = wp_upload_dir();
	
	// dir path for native skins
	$dirname1 = TG_PLUGIN_PATH.'includes/item-skins';
	// dir path for theme skins
	$dirname2 = get_stylesheet_directory().'/the-grid';
	// dir path for custom skins
	$dirname3 = $wp_upload_dir['basedir'] .'/the-grid';
	
	$findphp = '*.php';
	$findcss = '*.css';
	$types   = array('grid','masonry');
	
	$register_skins_base = new The_Grid_Register_Item_Skin();
	$register_skins = $register_skins_base->get_registered_skins();
		
	// get subfolder for native skins	
	$sub_dirs1 = glob($dirname1.'/*', GLOB_ONLYDIR|GLOB_NOSORT);
	$sub_dirs1 = ($sub_dirs1) ? $sub_dirs1 : array();
	// get subfolder for theme skins
	$sub_dirs2 = glob($dirname2.'/*', GLOB_ONLYDIR|GLOB_NOSORT);
	$sub_dirs2 = ($sub_dirs2) ? $sub_dirs2 : array();
	// get subfolder for custom skins
	$sub_dirs3 = glob($dirname3.'/*', GLOB_ONLYDIR|GLOB_NOSORT);
	$sub_dirs3 = ($sub_dirs3) ? $sub_dirs3 : array();
	
	// merge all kind of source
	$sub_dirs  = array_merge($sub_dirs1,$sub_dirs2,$sub_dirs3);
	

	if(count($sub_dirs)) {
		foreach($sub_dirs as $sub_dir) {
			$sub_dir_name = basename($sub_dir);	
			$path = (str_replace('/the-grid/grid','',$sub_dir));
			$path = (str_replace('/the-grid/masonry','',$path));
			$filter = ($path == get_stylesheet_directory()) ? wp_get_theme() : 'standard';
			if (in_array($sub_dir_name,$types)) {
				$sub_sub_dirs = glob($sub_dir.'/*', GLOB_ONLYDIR|GLOB_NOSORT);
				if(count($sub_sub_dirs)) {
					foreach($sub_sub_dirs as $sub_sub_dir) {
						$php  = glob($sub_sub_dir.'/'.$findphp);
						$css  = glob($sub_sub_dir.'/'.$findcss);
						$name = basename($php[0], '.php');
						$filter = (isset($register_skins[$name]['filter']) && !empty($register_skins[$name]['filter'])) ?  $register_skins[$name]['filter'] : $filter;
						$sub_sub_dir_name =  basename($sub_sub_dir);
						if (array_key_exists($name, $register_skins) || strpos($php[0], $dirname3) !== false) {
							$skins[$sub_sub_dir_name] = array(
								'type'   => $sub_dir_name,
								'filter' => $filter,
								'slug'   => $sub_sub_dir_name,
								'name'   => $name,
								'php'    => $php[0],
								'css'    => $css[0],
								'col'    => (isset($register_skins[$name]['col'])) ? $register_skins[$name]['col'] : 1,
								'row'    => (isset($register_skins[$name]['row'])) ? $register_skins[$name]['row'] : 1
							);
						}
					}
				}
			}
		}
	}

    return $skins;
	
});

// add a skin in a plugin/theme
/*add_filter('tg_add_item_skin', function($skins){
	
	$URI = get_template_directory();
	
	// register a skin and add it to the main skins array
	$skins['new_skin1'] = array(
		'type'   => 'masonry',
		'filter' => 'your-filter',
		'slug'   => 'your-slug',
		'name'   => 'new skin 1',
		'php'    => $URI . '/your_path/new_skin1.php',
		'css'    => $URI . '/your_path/new_skin1.css'
	);
	
	$skins['new_skin2'] = array(
		'type'   => 'grid',
		'filter' => 'your-filter',
		'slug'   => 'your-slug',
		'name'   => 'new skin 2',
		'php'    => $URI . '/your_path/new_skin2.php',
		'css'    => $URI . '/your_path/new_skin2.css'
	);	
	
	// return the skins array + the new one you added (in this example 2 new skins was added)
	return $skins;
	
});*/