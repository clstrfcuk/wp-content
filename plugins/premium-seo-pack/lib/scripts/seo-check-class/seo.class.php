<?php
/**
 * SEO check Class
 * http://www.aa-team.com
 * ======================
 *
 * @package			pspSeoCheck
 * @author			AA-Team
 */
if ( !class_exists('pspSeoCheck') ) {
class pspSeoCheck
{
	/*
	 * Some required plugin information
	 */
	const VERSION = '1.0';

	/*
	 * Store some helpers config
	 */
	public $the_plugin = null;

	private $module_folder = '';

	static protected $_instance;

	protected $optimizeSettings = array();
	protected $rules_allowed = array();
	protected $rules_settings = array();

	public $current_post = array();
	public $current_keyword = '';

	protected $_content_html = array();
	protected $_content_words = array();
	protected $_content_elem = array();

	static protected $cache = true;


	/*
	 * Required __construct() function
	 */
	public function __construct()
	{
		global $psp;

		$this->the_plugin = $psp;

		$this->optimizeSettings = $this->the_plugin->get_theoption('psp_on_page_optimization');
		//$this->rules_allowed = array_keys( $this->the_plugin->get_content_analyzing_rules() );

		$this->rules_settings = array(
			// seo title chars length
			'seo_title_min_length'			=> 5,
			'seo_title_max_length'			=> 70,

			// seo title words
			'seo_title_min_words'			=> 3,

			// meta description chars length
			'meta_description_min_length'	=> 70,
			'meta_description_max_length'	=> 160,

			// content words
			'content_min_words'				=> 250,
			'content_min_words_tax'			=> 50,

			// keyword found in content first/last X words
			'keyword_first100_words'		=> 100,
			'keyword_last100_words'			=> 100,

			// keyword density based on occurences in content
			'keyword_density_good_min'		=> 2,
			'keyword_density_good_max'		=> 4.5,

			'keyword_density_poor_min'		=> 0.5,
			'keyword_density_poor_max'		=> 6,

			// page title chars length
			'page_title_length_good_min'	=> 300,
			'page_title_length_good_max'	=> 600,

			'page_title_length_poor_min'	=> 5,
			'page_title_length_poor_max'	=> 1000,
		);

		require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
	}

	/**
	 * Singleton pattern
	 *
	 * @return pspSeoCheck Singleton instance
	 */
	static public function getInstance()
	{
        if (!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
	}


	// get rules settings
	public function get_rules_settings() {
		return $this->rules_settings;
	}

	// set current post to be analyzed
	public function set_current_post( $p=0, $post_content='empty' ) {
		if ( $this->the_plugin->__tax_istax( $p ) ) //taxonomy data!
			$post_id = (int) $p->term_id;
		else
			$post_id = (int) $p;

		if ( $post_id == 0 ) die( __('Invalid Post ID', $this->the_plugin->localizationName) );

		$post_metas = false;
		$post_isvalid = false;

		// set allowed rules for content analyzing
		$this->rules_allowed = $this->the_plugin->get_content_analyzing_allowed_rules( array(
			'settings'	=> $this->optimizeSettings,
			'istax'		=> $this->the_plugin->__tax_istax( $p ),
		));

		// set current post
		if ( $this->the_plugin->__tax_istax( $p ) ) { //taxonomy data!
			$post = $this->the_plugin->__tax_get_post( $p, ARRAY_A );
			$post_title = $post['name'];
			$post_content = trim($post_content);
			if( $post_content == 'empty' ){
				$post_content = $this->the_plugin->getPageContent( $post, $post['description'], true );
			}
			if ( empty($post_content) ) {
				$post_content = $post['description'];
			}

			if ( count($post) > 0 ) {
				$post_isvalid = true;

				$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $p );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$post_metas = $this->the_plugin->get_psp_meta( $p, $psp_current_taxseo );
			}
		}
		else {
			$post = get_post( (int) $p, ARRAY_A );
			$post_title = $post['post_title'];
			$post_content = trim($post_content);
			if( $post_content == 'empty' ){
				$post_content = $this->the_plugin->getPageContent( $post, $post['post_content'] );
			}
			if ( empty($post_content) ) {
				$post_content = $post['post_content'];
			}
			$post_content = $this->strip_shortcode($post_content);

			if ( count($post) > 0 ) {
				$post_isvalid = true;

				$post_metas = $this->the_plugin->get_psp_meta( (int) $p );
			}
		}

		$post_metas = ! is_array($post_metas) ? array() : $post_metas;

		$post_permalink = '';
		$first_paragraph = '';
		if ( $post_isvalid ) { // post is valid
			$post_permalink = $this->get_permalink($p);
			$first_paragraph = $this->get_first_paragraph($post_content);
		}

		$ret = compact( 'p', 'post_id', 'post_isvalid', 'post', 'post_title', 'post_content', 'post_metas', 'post_permalink', 'first_paragraph' );
		$this->current_post = $ret;
	}

	// set current keyword to be analyzed
	public function set_current_keyword( $keyword='' ) {
		$this->current_keyword = $keyword;

		if ( is_array($this->current_keyword) && empty($this->current_keyword) ) {
			$this->current_keyword = array(''); // add fake '' string
		}
	}

	// get current (post, keyword) pair score
	public function get_seo_score( $returnAs='die' )
	{
		$keyword = $this->current_keyword;
		extract($this->current_post);

		$ret = array(
			'status' 		=> 'invalid',
			'post_id'		=> $post_id,
			'mkw'			=> array(), // each focus keyword with all details (text, score, checked rules)
			'multikw_list'	=> array(), // only each focus keyword name

			'kw'			=> '',
			'score'			=> '',
			'density'		=> array(),
			'data'			=> array(),
		);

		if ( $post_isvalid ) { // post is valid

			// :: check rules
			$metaseo_title = isset($post_metas['title']) ? $post_metas['title'] : '';
			$metaseo_desc = isset($post_metas['description']) ? $post_metas['description'] : '';
			$metaseo_kw = isset($post_metas['keywords']) ? $post_metas['keywords'] : '';

			$seo_title_min_words = $this->rules_settings['seo_title_min_words'];
			$content_min_words = $this->the_plugin->__tax_istax( $p )
				? $this->rules_settings['content_min_words_tax'] : $this->rules_settings['content_min_words'];

			$is_multiple_kw = is_array($keyword) ? true : false;
			$multiple_kw = is_array($keyword) ? $keyword : array($keyword);
			$multiple_status = array();

			foreach ( $multiple_kw as $kw ) { // foreach multiple keyword

				$rules_status = array();

				if ( in_array('title', $this->rules_allowed) ) {
					$rules_status['title'] 					= $this->score_seo_title( $metaseo_title, $kw );
				}
				if ( in_array('title_enough_words', $this->rules_allowed) ) {
					$rules_status['title_enough_words'] 	= $this->score_seo_title_enough_words( $metaseo_title, $seo_title_min_words );
				}
				if ( in_array('page_title', $this->rules_allowed) ) {
					$rules_status['page_title'] 			= $this->score_page_title( $post_title, $kw );
				}
				if ( in_array('meta_description', $this->rules_allowed) ) {
					$rules_status['meta_description'] 		= $this->score_meta_description( $metaseo_desc, $kw );
				}
				if ( in_array('meta_keywords', $this->rules_allowed) ) {
					$rules_status['meta_keywords'] 			= $this->score_meta_keywords( $metaseo_kw, $kw );
				}
				if ( in_array('permalink', $this->rules_allowed) ) {
					$rules_status['permalink'] 				= $this->score_permalink( $post_permalink, $kw );
				}
				if ( in_array('first_paragraph', $this->rules_allowed) ) {
					$rules_status['first_paragraph'] 		= $this->score_first_paragraph( $first_paragraph, $kw );
				}
				if ( in_array('embedded_content', $this->rules_allowed) ) {
					$rules_status['embedded_content'] 		= $this->score_embedded_content( $post_content );
				}
				if ( in_array('enough_words', $this->rules_allowed) ) {
					$rules_status['enough_words'] 			= $this->score_enough_words( $post_content, $content_min_words );
				}

				//if ( !$this->the_plugin->__tax_istax( $p ) ) { //taxonomy data!
				if ( in_array('images_alt', $this->rules_allowed) ) {
					$rules_status['images_alt'] 		= $this->score_images_alt( $post_content, $kw );
				}
				if ( in_array('html_bold', $this->rules_allowed) ) {
					$rules_status['html_bold'] 			= $this->score_html_bold( $post_content, $kw );
				}
				if ( in_array('html_italic', $this->rules_allowed) ) {
					$rules_status['html_italic'] 		= $this->score_html_italic( $post_content, $kw );
				}
				if ( in_array('html_underline', $this->rules_allowed) ) {
					$rules_status['html_underline'] 	= $this->score_html_underline( $post_content, $kw );
				}
				//}

				if ( in_array('subheadings', $this->rules_allowed) ) {
					$rules_status['subheadings'] 			= $this->score_subheadings( $post_content, $kw );
				}
				if ( in_array('first100words', $this->rules_allowed) ) {
					$rules_status['first100words'] 			= $this->score_first100words( $post_content, $kw );
				}
				if ( in_array('last100words', $this->rules_allowed) ) {
					$rules_status['last100words'] 			= $this->score_last100words( $post_content, $kw );
				}
				if ( in_array('links_external', $this->rules_allowed) ) {
					$rules_status['links_external']			= $this->score_links_external( $post_content, $kw, $post_permalink );
				}
				if ( in_array('links_internal', $this->rules_allowed) ) {
					$rules_status['links_internal']			= $this->score_links_internal( $post_content, $kw, $post_permalink );
				}
				if ( in_array('links_competing', $this->rules_allowed) ) {
					$rules_status['links_competing']		= $this->score_links_competing( $post_content, $kw, $post_permalink );
				}

				if ( in_array('kw_density', $this->rules_allowed) ) {
					$keyword_density = $this->get_keyword_density($post_content, $kw, false);
					$rules_status['kw_density'] 			= $this->score_keyword_density( $keyword_density );
				}
				// :: end check rules

				// :: DEBUG
				//$get_meta_keywords = $this->get_meta_keywords( $post_content );
				//var_dump('<pre>', $get_meta_keywords , '</pre>');
				//var_dump('<pre>', $rules_status , '</pre>');
				//echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
				// :: end DEBUG

				// :: calculate the scores
				$score = 0;
				foreach ($rules_status as $key => $value) {
					$score = $score + $value["score"];
				}

				// transform in percents
				if ( $score > 0 ) {
					$score = number_format( ( ( 100 * $score ) / count($rules_status) ), 1 );
				} else {
					$score = '0';
				}

				$multiple_status["$kw"] = array(
					'kw'			=> $kw,
					'score'			=> $score,
					'density'		=> array(
						'nb_words' 		=> $keyword_density['nb_words'],
						'kw_occurences' => $keyword_density['kw_occurences'],
						'density'		=> $keyword_density['density'],
					),
					'data'			=> $rules_status,
				);
			} // end foreach multiple keyword

			reset($multiple_status);
			$first = current($multiple_status);
			if ( false !== $first ) {
				$ret = array_merge($ret, $first);
			}

			if ( $is_multiple_kw ) {
				$ret = array_merge($ret, array(
					'status' 		=> 'valid',
					'post_id'		=> $post_id,
					'mkw'			=> $multiple_status,
					'multikw_list'	=> array_keys( $multiple_status ),
				));
			}
			else {
				$ret = array_merge($ret, array(
					'status' 		=> 'valid',
					'post_id'		=> $post_id,
				));
			}
		} // end post is valid

		if ( $returnAs == 'array' ) {
			return $ret;
		}
		die(json_encode($ret));
	}

	/**
	 * Get post permalink from a WordPress post.
	 *
	 * @return string
	 */
	public function get_permalink( $post )
	{
		$url = '';
		if ( $this->the_plugin->__tax_istax( $post ) ) { //taxonomy data!
			$url = get_term_link( $post->term_id, $post->taxonomy );
		} else {
			$url = get_permalink( (int) $post );
		}
		return $url;
	}

	/**
	 * Get first paragraph from a WordPress post.
	 *
	 * @return string
	 */
	public function gen_first_paragraph( $str )
	{
		$str = $this->strip_shortcode($str);
		$str = wpautop($str);

		$base = '';
		$c = 0; $pos = 0; $pos2 = 0;
		do {

			$str = substr($str, $pos);
			$pos2 = strpos( $str, '</p>' ) + 4;
			
			$base = substr($str, 0, $pos2);
			$base = strip_tags($base);
			$base = preg_replace('/\s(\s+)/im', ' ', $base);
			$base = trim($base);

			$pos = $pos2;
			$c++;
		} while ( $c < 20 && empty($base) );

		return $base;
	}

	public function get_first_paragraph( $str )
	{
		$ret = $this->get_cached_content_elem( 'first_paragraph', $str, array() );
		return $ret['result'];
	}
	
	/**
	 * Auto generate meta description from string
	 *
	 * @return string
	 */
	public function gen_meta_desc( $str )
	{
		$base = '';

		$str =  $this->strip_shortcode( $str );
		$str = strip_tags($str);
		$str = preg_replace('/\s(\s+)/im', ' ', $str);
		$str = trim($str);

		$max_length = (int) ( $this->rules_settings['meta_description_max_length'] - 3 ); // default = 157

		if (trim($str) != "") {
			$base = $this->the_plugin->utf8->substr($str, 0, $max_length);
			if ( $this->the_plugin->utf8->strlen($base) == $max_length ) {
				$base .= '...';
			}
		}

		return $base;
	}

	public function get_meta_desc( $str )
	{
		$ret = $this->get_cached_content_elem( 'meta_desc', $str, array() );
		return $ret['result'];
	}

	/**
	 * Auto generate meta keywords from string
	 *
	 * @return array
	 */
	public function gen_meta_keywords( $content, $how_many=10 )
	{
		$content = trim($content);

		if ( !empty( $content ) ) {
			$pms = array(
				'unique_words'			=> true,

				'word_min_chars'		=> $this->get_word_min_chars( 'meta_keywords' ),
				'stop_words'			=> $this->get_stop_words( 'meta_keywords' ),

				'how_many_words'		=> $how_many,
				//'first_words_limit'		=> 0,
				//'last_words_limit'		=> 0,

				'frequency'				=> true,
				'frequency_sort_dir'	=> 'DESC',
			);
			$content_words = $this->get_cached_content_words($content, $pms);
			return array_keys($content_words['words']);
		}
		return array();
	}

	public function get_meta_keywords( $content, $how_many=10 )
	{
		$ret = $this->get_cached_content_elem( 'meta_keywords', $content, array(
			'how_many' => $how_many
		));
		return $ret['result'];
	}

	/**
	 * Get keyword density in content
	 *
	 * @return array
	 */
	public function get_keyword_density( $content, $kw, $single=true )
	{
		$content = trim($content);

		//the total number of words in the content
		$nb_words = 0;
		if ( !empty( $content ) ) {
			$pms = array(
				'unique_words'			=> false,

				'word_min_chars'		=> $this->get_word_min_chars( 'keyword_density'),
				'stop_words'			=> $this->get_stop_words( 'keyword_density' ),

				'how_many_words'		=> -1,
				//'first_words_limit'		=> 0,
				//'last_words_limit'		=> 0,

				'frequency'				=> true,
				'frequency_sort_dir'	=> 'DESC',
			);
			$content_words = $this->get_cached_content_words($content, $pms);
			$nb_words = $content_words['words_len'];
		}

		//the total number of focus keyword occurences in the post cotent
		$kw_occ = 0;
		if ( !empty( $content ) && !empty( $kw ) ) {
			$kw_occ = $this->get_count_occurences( $content, $kw );
		}

		$__density = $this->get_density_by_formula(array(
			'nb_words'			=> $nb_words,
			'kw_occ'			=> $kw_occ,
		));
		
		$ret = array(
			'content'		=> $content,
			'kw'			=> $kw,
			'nb_words' 		=> $nb_words,
			'kw_occurences' => $kw_occ,
			'density'		=> $__density
		);
		return ($single!==true ? $ret : $ret['density']);
	}

	/**
	 * Get keyword occurences in first / last X words from content
	 *
	 * @return string
	 */
	public function get_first_last_words( $content, $kw, $pms=array() )
	{
		extract($pms);

		$selected_words = '';
		if ( !empty( $content ) ) {
			$pms = array(
				'unique_words'			=> false,

				'word_min_chars'		=> $this->get_word_min_chars( $what ),
				'stop_words'			=> $this->get_stop_words( $what ),

				'how_many_words'		=> -1,
				//'first_words_limit'		=> 0,
				//'last_words_limit'		=> 0,

				'frequency'				=> false,
				'frequency_sort_dir'	=> 'DESC',
			);
			$content_words = $this->get_cached_content_words($content, $pms);

			$keywords = $content_words['words'];
			//var_dump('<pre>',$keywords ,'</pre>');

			if ( 'first100_words' == $what ) {
				$keywords = array_slice($keywords, 0, $words_limit);
			}
			else if ( 'last100_words' == $what ) {
				$keywords = array_slice($keywords, -$words_limit);
			}
			//var_dump('<pre>', $what, $keywords,'</pre>');

			$selected_words = implode(' ', $keywords);
		}

		//the total number of focus keyword occurences in the post cotent
		$kw_occ = 0;
		if ( !empty( $content ) && !empty( $kw ) ) {
			$kw_occ = $this->get_count_occurences( $selected_words, $kw );
		}
		return $kw_occ;
	}

	/**
	 * Find content links & keyword occurences in them
	 *
	 * @return string
	 */
	public function get_content_links( $content, $kw, $permalink, $pms=array() )
	{
		$content = trim($content);

		$html = $this->get_cached_content_as_htmlquery($content);

		$kw = trim($kw);
		$kw = $this->the_plugin->utf8->strtolower($kw);

		$cached_elem = $this->get_cached_content_elem( 'html_links', $content, array('html' => $html, '__exclude' => array('html')) );
		//var_dump('<pre>', $cached_elem , '</pre>');

		$cached_elem2 = $this->get_cached_content_elem( 'parse_links', $content, array(
			'html' 			=> $html,
			'focus_kw'		=> $kw,
			'permalink'		=> $permalink,
			'cached_elem' 	=> $cached_elem,
			'__exclude'		=> array('html', 'focus_kw', 'cached_elem'),
		));
		$the_links = $cached_elem2['result'];
		return $the_links;
	}


	/**
	 * RULES
	 */

	/**
	 * Check if the keyword is contained in the seo title.
	 *
	 * @param string  $str
	 * @param string  $focus_kw
	 * @return array $results   The results array.
	 */
	public function score_seo_title( $str, $focus_kw )
	{
		$str = trim($str);
		$str = $this->the_plugin->utf8->strtolower($str);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$min_length 	= $this->rules_settings['seo_title_min_length'];
		$max_length 	= $this->rules_settings['seo_title_max_length'];

		$msgs = array(
			'missing' 			=> __( "Bad, please create a SEO title.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have a SEO title, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'less_characters' 	=> __( "Bad, the SEO title contains %d characters, which is less than the recommended minimum of %d characters.", $this->the_plugin->localizationName ),
			'more_characters' 	=> __( "Bad, the SEO title contains %d characters, which is more than the viewable limit of %d characters.", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the focus keyword <strong>%s</strong> does not appear in the SEO title.", $this->the_plugin->localizationName ),
			'poor' 				=> __( "Poor, the SEO title contains between %d and %d characters and contains your focus keyword, but it doesn't begin with it.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the SEO title contains between %d and %d characters and also it begins with your focus keyword.", $this->the_plugin->localizationName )
		);

		$ret = array(
			'debug'		=> array(
				'str'		=> $str,
				'focus_kw'	=> $focus_kw
			)
		);

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			$length = $this->the_plugin->utf8->strlen( $str );

			if ( $length < $min_length ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less_characters'], $length, $min_length)
				));
			}
			else if( $length > $max_length ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['more_characters'], $length, $max_length)
				));
			}
			else{
				// at the begining
				if( preg_match('/^' . preg_quote($focus_kw, '/') . '/i', $str) != false ){
					return array_merge($ret, array(
						'score' 	=> 1,
						'msg'		=> sprintf($msgs['good'], $min_length, $max_length)
					));
				}
				// contains
				else if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) != false ){
					return array_merge($ret, array(
						'score' 	=> 0.75,
						'msg'		=> sprintf($msgs['poor'], $min_length, $max_length)
					));
				}
				else{
					return array_merge($ret, array(
						'score' 	=> 0.5,
						'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw),
					));
				}
			}
		}
	}

	/**
	 * Check if seo title have enough words
	 *
	 * @param string  $page_content
	 * @return array  $results   The results array.
	 */
	public function score_seo_title_enough_words( $page_content, $min_words=3 )
	{
		$good_words_count = isset($min_words) && $min_words>0 ? $min_words : $this->rules_settings['seo_title_min_words'];

		$page_content = trim($page_content);

		//$nb_words = (int) @$this->the_plugin->utf8->str_word_count($page_content);
		$nb_words = 0;
		if ( !empty( $page_content ) ) {
			$pms = array(
				'unique_words'			=> false,

				'word_min_chars'		=> $this->get_word_min_chars( 'seo_title_enough_words' ),
				'stop_words'			=> $this->get_stop_words( 'seo_title_enough_words' ),

				'how_many_words'		=> -1,
				//'first_words_limit'		=> 0,
				//'last_words_limit'		=> 0,

				'frequency'				=> true,
				'frequency_sort_dir'	=> 'DESC',
			);
			$content_words = $this->get_cached_content_words($page_content, $pms);
			$nb_words = $content_words['words_len'];
		}

		$msgs = array(
			'missing' 			=> __( "Enough words - Bad, please create a SEO title.", $this->the_plugin->localizationName ),
			'less_words' 		=> __( "Bad, the SEO title contains %d allowed words, which is less than the recommended minimum of %d words.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the SEO title contains %d allowed words and the recommended minimum is %d words.", $this->the_plugin->localizationName )
		);

		if ( $page_content == "" ) {
			return array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			);
		}
		else {
			$length = $nb_words;

			if( $length < $good_words_count ){
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less_words'], $length, $good_words_count)
				);
			}
			else{
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $length, $good_words_count)
				);
			}
		}
	}

	/**
	 * Check if the keyword is contained in the page title.
	 *
	 * @param string  $str
	 * @param string  $focus_kw
	 * @return array $results   The results array.
	 */
	public function score_page_title( $str, $focus_kw )
	{
		$str = trim($str);
		$str = $this->the_plugin->utf8->strtolower($str);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$good_title_min = $this->rules_settings['page_title_length_good_min'];
		$good_title_max = $this->rules_settings['page_title_length_good_max'];
		$poor_title_min = $this->rules_settings['page_title_length_poor_min'];
		$poor_title_max = $this->rules_settings['page_title_length_poor_max'];

		$msgs = array(
			'missing' 			=> __( "Bad, please create a page title.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have a page title, but you must create a focus keyword.", $this->the_plugin->localizationName ),

			'bad' 				=> __( "Bad, the page title contains %d characters and it's not between %d and %d characters, which is the recommended interval.", $this->the_plugin->localizationName ),
			'poor' 				=> __( "Poor, the page title contains %d characters and it's not between %d and %d characters, which is the recommended interval.", $this->the_plugin->localizationName ),
			'good'				=> __( "Great, the page title contains %d characters and it's between %d and %d characters, which is the recommended interval.", $this->the_plugin->localizationName ),

			'no_focus_kw' 		=> __( "Bad, the focus keyword <strong>%s</strong> does not appear in the page title.", $this->the_plugin->localizationName ),
			'focus_kw_poor'		=> __( "Poor, the page title contains your focus keyword, but it doesn't begin with it.", $this->the_plugin->localizationName ),
			'focus_kw_good'		=> __( "Great, the page title begins with your focus keyword.", $this->the_plugin->localizationName )
		);

		$ret = array(
			'debug'		=> array(
				'str'		=> $str,
				'focus_kw'	=> $focus_kw
			)
		);

		$ret_tmp = array( 'chars' => array(), 'focus_kw' => array() );

		if ( $str == "" ) {
			$ret_tmp['chars'] = array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else {
			$length = $this->the_plugin->utf8->strlen( $str );

			if ( $length>=$good_title_min && $length<=$good_title_max ) {
				$ret_tmp['chars'] = array_merge($ret, array(
					'score' 	=> 0.5,
					'msg'		=> sprintf($msgs['good'], $length, $good_title_min, $good_title_max)
				));
			}
			else if ( $length>=$poor_title_min && $length<=$poor_title_max ) {
				$ret_tmp['chars'] = array_merge($ret, array(
					'score' 	=> 0.25,
					'msg'		=> sprintf($msgs['poor'], $length, $good_title_min, $good_title_max)
				));
			}
			else {
				$ret_tmp['chars'] = array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['bad'], $length, $good_title_min, $good_title_max)
				));
			}
		}
		
		if ( $focus_kw == "" ) {
			$ret_tmp['focus_kw'] = array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			// at the begining
			if( preg_match('/^' . preg_quote($focus_kw, '/') . '/i', $str) != false ){
				$ret_tmp['focus_kw'] = array_merge($ret, array(
					'score' 	=> 0.5,
					'msg'		=> sprintf($msgs['focus_kw_good'])
				));
			}
			// contains
			else if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) != false ){
				$ret_tmp['focus_kw'] = array_merge($ret, array(
					'score' 	=> 0.25,
					'msg'		=> sprintf($msgs['focus_kw_poor'])
				));
			}
			else{
				$ret_tmp['focus_kw'] = array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw),
				));
			}
		}

		$ret['score'] = number_format( ( $ret_tmp['chars']['score'] + $ret_tmp['focus_kw']['score'] ), 2 );
		$ret['msg'] = $ret_tmp['chars']['msg'] . ' ' . $ret_tmp['focus_kw']['msg'];
		return $ret;
	}

	/**
	 * Check if the keyword is contained in the meta description.
	 *
	 * @param string  $str
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_meta_description( $str, $focus_kw )
	{
		$str = trim($str);
		$str = $this->the_plugin->utf8->strtolower($str);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$min_length 	= $this->rules_settings['meta_description_min_length'];
		$max_length 	= $this->rules_settings['meta_description_max_length'];

		$msgs = array(
			'missing' 			=> __( "Bad, please create a page meta description.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have a page meta description, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'less_characters' 	=> __( "Bad, the page meta description contains %d characters, which is less than the recommended minimum of %d characters.", $this->the_plugin->localizationName ),
			'more_characters' 	=> __( "Bad, the page meta description contains %d characters, which is more than the viewable limit of %d characters.", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the focus keyword <strong>%s</strong> does not appear in the page meta description.", $this->the_plugin->localizationName ),
			'poor' 				=> __( "Poor, the meta description contains between %d and %d characters and contains your focus keyword, but it doesn't begin with it.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the meta description contains between %d and %d characters and also it begins with your focus keyword.", $this->the_plugin->localizationName )
		);

		$ret = array(
			'debug'		=> array(
				'str'		=> $str,
				'focus_kw'	=> $focus_kw
			)
		);

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			$length = $this->the_plugin->utf8->strlen( $str );

			if ( $length < $min_length ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less_characters'], $length, $min_length)
				));
			}
			else if( $length > $max_length ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['more_characters'], $length, $max_length)
				));
			}
			else{
				if( preg_match('/^' . preg_quote($focus_kw, '/') . '/i', $str) != false ){
					return array_merge($ret, array(
						'score' 	=> 1,
						'msg'		=> sprintf($msgs['good'], $min_length, $max_length),
					));
				}
				else if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) != false ){
					return array_merge($ret, array(
						'score' 	=> 0.75,
						'msg'		=> sprintf($msgs['poor'], $min_length, $max_length),
					));
				}
				else{
					return array_merge($ret, array(
						'score' 	=> 0.5,
						'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw),
						'debug'		=> array(
							'str' 		=> $str,
							'focus_kw' 	=> $focus_kw
						)
					));
				}
			}
		}
	}

	/**
	 * Check if the keyword is contained in the meta keywords.
	 *
	 * @param string  $str
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_meta_keywords( $str, $focus_kw )
	{
		$str = trim($str);
		$str = $this->the_plugin->utf8->strtolower($str);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$msgs = array(
			'missing' 			=> __( "Bad, please create the page meta keywords.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have the page meta keywords, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the focus keyword <strong>%s</strong> does not appear in the page meta keywords.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page meta keywords contains your focus keyword.", $this->the_plugin->localizationName )
		);

		$ret = array(
			'debug'		=> array(
				'str'		=> $str,
				'focus_kw'	=> $focus_kw
			)
		);

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) == false ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw)
				));
			}
			else{
				return array_merge($ret, array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good']),
				));
			}

		}
	}

	/**
	 * Check if the keyword is contained in the permalink.
	 *
	 * @param string  $str
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_permalink( $str, $focus_kw )
	{
		$str = trim($str);
		$str = $this->the_plugin->utf8->strtolower($str);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);
		$focus_kw = sanitize_title($focus_kw);
  
		$msgs = array(
			'missing' 			=> __( "Bad, please create a page permalink.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have a page permalink, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the focus keyword <strong>%s</strong> does not appear in the page permalink.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page permalink contains your focus keyword.", $this->the_plugin->localizationName )
		);

		$ret = array(
			'debug'		=> array(
				'str'		=> $str,
				'focus_kw'	=> $focus_kw
			)
		);

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) == false ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw)
				));
			}
			else{
				return array_merge($ret, array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good']),
				));
			}

		}
	}

	/**
	 * Check if the keyword is contained in the first paragraph.
	 *
	 * @param string  $str
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_first_paragraph( $str, $focus_kw )
	{
		$str = trim($str);
		$str = $this->the_plugin->utf8->strtolower($str);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$msgs = array(
			'missing' 			=> __( "Bad, please create at least one paragraph in the page content.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have at least one paragraph in the page content, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the focus keyword <strong>%s</strong> does not appear in the page content first paragraph.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content first paragraph contains your focus keyword.", $this->the_plugin->localizationName )
		);

		$ret = array(
			'debug'		=> array(
				'str'		=> $str,
				'focus_kw'	=> $focus_kw
			)
		);

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) == false ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw),
				));
			}
			else{
				return array_merge($ret, array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good']),
				));
			}

		}
	}

	/**
	 * Check if content have embedded content
	 *
	 * @param string  $str
	 * @return array  $results   The results array.
	 */
	public function score_embedded_content( $page_content )
	{
		$page_content = trim($page_content);

		$html = $this->get_cached_content_as_htmlquery($page_content);

		$msgs = array(
			'missing' 			=> __( "Embedded content - Bad, please add some content for your web page.", $this->the_plugin->localizationName ),
			'frame_detect' 		=> __( "Bad, frames can cause problems on your web page because search engines will not crawl or index the content within them.", $this->the_plugin->localizationName ),
			'iframe_detect' 	=> __( "Bad, iframes can cause problems on your web page because search engines will not crawl or index the content within them.", $this->the_plugin->localizationName ),
			'flash_detect' 		=> __( "Bad, flash can cause problems on your web page because search engines will not crawl or index the content within them.", $this->the_plugin->localizationName ),
			'video_detect' 		=> __( "Bad, video can cause problems on your web page because search engines will not crawl or index the content within them.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, your web page content don't have any embedded content <i>(frame, iframe, object, embed or HTML5 video)</i>.", $this->the_plugin->localizationName )
		);

		if ( $page_content == "" ) {
			return array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			);
		}
		else {
			$pms = array('html' => $html, '__exclude' => array('html'));
			if( ( $cached_elem = $this->get_cached_content_elem( 'html_frame', $page_content, $pms ) ) && $cached_elem['size'] ){
				return array(
					'score' 	=> 0,
					'msg'		=> $msgs['frame_detect']
				);
			}elseif( ( $cached_elem = $this->get_cached_content_elem( 'html_iframe', $page_content, $pms ) ) && $cached_elem['size'] ){
				return array(
					'score' 	=> 0,
					'msg'		=> $msgs['iframe_detect']
				);
			}elseif( ( $cached_elem = $this->get_cached_content_elem( 'html_object', $page_content, $pms ) ) && $cached_elem['size'] ){
				return array(
					'score' 	=> 0,
					'msg'		=> $msgs['flash_detect']
				);
			}elseif( ( $cached_elem = $this->get_cached_content_elem( 'html_video', $page_content, $pms ) ) && $cached_elem['size'] ){
				return array(
					'score' 	=> 0.7,
					'msg'		=> $msgs['video_detect']
				);
			}else{
				return array(
					'score' 	=> 1,
					'msg'		=> $msgs['good']
				);
			}
		}
	}

	/**
	 * Check if content has enough words
	 *
	 * @param string  $page_content
	 * @return array  $results   The results array.
	 */
	public function score_enough_words( $page_content, $min_words=250 )
	{
		$good_words_count = isset($min_words) && $min_words>0 ? $min_words : $this->rules_settings['content_min_words'];

		$page_content = trim($page_content);

		//$nb_words = (int) @$this->the_plugin->utf8->str_word_count($page_content);
		$nb_words = 0;
		if ( !empty( $page_content ) ) {
			$pms = array(
				'unique_words'			=> false,

				'word_min_chars'		=> $this->get_word_min_chars( 'enough_words' ),
				'stop_words'			=> $this->get_stop_words( 'enough_words' ),

				'how_many_words'		=> -1,
				//'first_words_limit'		=> 0,
				//'last_words_limit'		=> 0,

				'frequency'				=> true,
				'frequency_sort_dir'	=> 'DESC',
			);
			$content_words = $this->get_cached_content_words($page_content, $pms);
			$nb_words = $content_words['words_len'];
		}

		$msgs = array(
			'missing' 			=> __( "Enough words - Bad, please add some content for the page.", $this->the_plugin->localizationName ),
			'less_words' 		=> __( "Bad, the page content contains %d allowed words, which is less than the recommended minimum of %d words.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content contains %d allowed words and the recommended minimum is %d words.", $this->the_plugin->localizationName )
		);

		if ( $page_content == "" ) {
			return array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			);
		}
		else {
			$length = $nb_words;

			if( $length < $good_words_count ){
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less_words'], $length, $good_words_count)
				);
			}
			else{
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $length, $good_words_count)
				);
			}
		}
	}

	/**
	 * Check if the keyword is contained in the images alt.
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_images_alt( $page_content, $focus_kw )
	{
		$page_content = trim($page_content);

		$html = $this->get_cached_content_as_htmlquery($page_content);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$msgs = array(
			'missing' 			=> __( "Bad, the page content has no images.", $this->the_plugin->localizationName ),
			'missing_focus_kw' 	=> __( "Bad, the page content has %d images, but %d images contains your focus keyword in alt attribute.", $this->the_plugin->localizationName ),
			'poor' 				=> __( "Poor, the page content has %d images and %d images contains an alt attribute.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content has %d images and %d images contains your focus keyword in alt attribute.", $this->the_plugin->localizationName )
		);

		$cached_elem = $this->get_cached_content_elem( 'html_images', $page_content, array('html' => $html, '__exclude' => array('html')) );
		$the_images = $cached_elem['result'];
		$total_images = $cached_elem['size'];
		if( $total_images > 0 ){

			//$kw_images = $html->find('img[alt="' . ( $focus_kw ) . '"]')->size();
			// fix case sensivity problem!
			$kw_images_hasalt = 0;
			$kw_images = 0;
			$imgList = $the_images;
			foreach( $imgList as $tag ) {
				$tag = pspPQ($tag); // cache the object

				$attrAlt = $tag->attr('alt');
				$attrAlt = trim($attrAlt);

				if ( $attrAlt != '' ) {
					$attrAlt = $this->the_plugin->utf8->strtolower($attrAlt);

					$kw_images_hasalt++;
					if ( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $attrAlt) == true ) {
						$kw_images++;
					}
				}
			} // end foreach

			if( $kw_images > 0 ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $total_images, $kw_images )
				);
			}
			else if( $kw_images_hasalt > 0 ){
				return array(
					'score' 	=> 0.5,
					'msg'		=> sprintf($msgs['poor'], $total_images, $kw_images_hasalt )
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['missing_focus_kw'], $total_images, $kw_images )
				);
			}
		} else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'] )
			);
		}
	}

	/**
	 * Check if the keyword is contained in the HTML bold / strong tag.
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_html_bold( $page_content, $focus_kw )
	{
		$page_content = trim($page_content);

		$html = $this->get_cached_content_as_htmlquery($page_content);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$msgs = array(
			'missing' 				=> __( "Bad, the page content has no bold elements.", $this->the_plugin->localizationName ),
			'less' 					=> __( "Bad, the page content has %d bold elements, but none of them contains your focus keyword.", $this->the_plugin->localizationName ),
			'good' 					=> __( "Great, the page content has %d bold elements and at least 1 of them contains your focus keyword.", $this->the_plugin->localizationName )
		);

		$cached_elem = $this->get_cached_content_elem( 'html_bold', $page_content, array('html' => $html, '__exclude' => array('html')) );
		$the_bolds = $cached_elem['result'];
		$total_bolds = $cached_elem['size'];
		//var_dump('<pre>BOLD: ',$the_bolds->text() ,'</pre>'); 

		if( $total_bolds > 0 ){
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $the_bolds->text()) == true ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $total_bolds )
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less'], $total_bolds )
				);
			}
		}
		else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'], $total_bolds )
			);
		}
	}

	/**
	 * Check if the keyword is contained in the HTML italic tag.
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_html_italic( $page_content, $focus_kw )
	{
		$page_content = trim($page_content);

		$html = $this->get_cached_content_as_htmlquery($page_content);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$msgs = array(
			'missing' 			=> __( "Bad, the page content has no italic elements.", $this->the_plugin->localizationName ),
			'less' 				=> __( "Bad, the page content has %d italic elements and none of them contains your focus keyword.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content has %d italic elements and at least 1 of them contains your focus keyword.", $this->the_plugin->localizationName )
		);

		$cached_elem = $this->get_cached_content_elem( 'html_italic', $page_content, array('html' => $html, '__exclude' => array('html')) );
		$the_italics = $cached_elem['result'];
		$total_italics = $cached_elem['size'];
		//var_dump('<pre>ITALIC: ', $the_italics->text() , '</pre>');

		if( $total_italics > 0 ){
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $the_italics->text()) == true ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $total_italics )
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less'], $total_italics )
				);
			}
		}
		else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'], $total_italics )
			);
		}
	}

	/**
	 * Check if the keyword is contained in the HTML underline tag.
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_html_underline( $page_content, $focus_kw )
	{
		$page_content = trim($page_content);

		$html = $this->get_cached_content_as_htmlquery($page_content);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$msgs = array(
			'missing' 			=> __( "Bad, the page content has no underlined elements.", $this->the_plugin->localizationName ),
			'less' 				=> __( "Bad, the page content has %d underlined elements and none of them contains your focus keyword.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content has %d underlined elements and at least 1 of them contains your focus keyword.", $this->the_plugin->localizationName )
		);

		$cached_elem = $this->get_cached_content_elem( 'html_underline', $page_content, array('html' => $html, '__exclude' => array('html')) );
		$the_underlined = $cached_elem['result'];
		$total_underlined = $cached_elem['size'];
		//var_dump('<pre>UNDERLINE: ',$the_underlined->text() ,'</pre>'); 

		if( $total_underlined > 0 ){
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $the_underlined->text()) == true ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $total_underlined )
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less'], $total_underlined )
				);
			}
		}
		else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'], $total_underlined )
			);
		}
	}
	
	/**
	 * Check if the keyword is contained in the subheadings (h1, h2, h3).
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_subheadings( $page_content, $focus_kw )
	{
		$page_content = trim($page_content);

		$html = $this->get_cached_content_as_htmlquery($page_content);

		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$msgs = array(
			'missing' 			=> __( "Bad, the page content has no subheading tags (h1, h2, h3).", $this->the_plugin->localizationName ),
			'missing_focus_kw' 	=> __( "Bad, the page content has %d &lt;h1&gt;, %d &lt;h2&gt;, %d &lt;h3&gt; subheading tags and %d &lt;h1&gt;, %d &lt;h2&gt;, %d &lt;h3&gt; subheading tags contains your focus keyword.", $this->the_plugin->localizationName ),
			'poor' 				=> __( "Poor, the page content has %d &lt;h1&gt;, %d &lt;h2&gt;, %d &lt;h3&gt; subheading tags and %d &lt;h1&gt;, %d &lt;h2&gt;, %d &lt;h3&gt; subheading tags contains your focus keyword.", $this->the_plugin->localizationName ),
			'ok' 				=> __( "Ok, the page content has %d &lt;h1&gt;, %d &lt;h2&gt;, %d &lt;h3&gt; subheading tags and %d &lt;h1&gt;, %d &lt;h2&gt;, %d &lt;h3&gt; subheading tags contains your focus keyword.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content has %d &lt;h1&gt;, %d &lt;h2&gt;, %d &lt;h3&gt; subheading tags and %d &lt;h1&gt;, %d &lt;h2&gt;, %d &lt;h3&gt; subheading tags contains your focus keyword. Also your focus keyword appears at the begining in the &lt;h1&gt; tag.", $this->the_plugin->localizationName )
		);

		$cached_elem = $this->get_cached_content_elem( 'html_subheadings', $page_content, array('html' => $html, '__exclude' => array('html')) );
		$the_subheadings = $cached_elem['result'];
		$total_subheadings = $cached_elem['size'];
		//var_dump('<pre>', $total_subheadings , '</pre>');

		if( $total_subheadings > 0 ){

			$found_subheadings = array('h1' => 0, 'h2' => 0, 'h3' => 0);
			$kw_subheadings = array('h1' => 0, 'h2' => 0, 'h3' => 0, 'h1_begin' => 0);
			$subheadingsList = $the_subheadings;
			foreach( $subheadingsList as $tag ) {
				$text = $tag->textContent;
				$what_tag = strtolower($tag->tagName);

				if ( $text != '' ) {
					$text = $this->the_plugin->utf8->strtolower($text);
					$found_subheadings["$what_tag"]++;
					//var_dump('<pre>',$text, $what_tag, $found_subheadings["$what_tag"] ,'</pre>'); 

					if ( ('h1' == $what_tag) && preg_match('/^' . preg_quote($focus_kw, '/') . '/i', $text) == true ) {
						$kw_subheadings['h1_begin']++;
					}
					if ( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $text) == true ) {
						$kw_subheadings["$what_tag"]++;
					}
				}
			}
			//var_dump('<pre>', $found_subheadings, $kw_subheadings, '</pre>');

			if( $kw_subheadings['h1_begin'] ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'],
						$found_subheadings['h1'], $found_subheadings['h2'], $found_subheadings['h3'],
						$kw_subheadings['h1'], $kw_subheadings['h2'], $kw_subheadings['h3']
					)
				);
			}
			else if( $kw_subheadings['h1'] ){
				return array(
					'score' 	=> 0.75,
					'msg'		=> sprintf($msgs['ok'],
						$found_subheadings['h1'], $found_subheadings['h2'], $found_subheadings['h3'],
						$kw_subheadings['h1'], $kw_subheadings['h2'], $kw_subheadings['h3']
					)
				);
			}
			else if( $kw_subheadings['h2'] || $kw_subheadings['h3'] ){
				return array(
					'score' 	=> 0.5,
					'msg'		=> sprintf($msgs['poor'],
						$found_subheadings['h1'], $found_subheadings['h2'], $found_subheadings['h3'],
						$kw_subheadings['h1'], $kw_subheadings['h2'], $kw_subheadings['h3']
					)
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['missing_focus_kw'],
						$found_subheadings['h1'], $found_subheadings['h2'], $found_subheadings['h3'],
						$kw_subheadings['h1'], $kw_subheadings['h2'], $kw_subheadings['h3']
					)
				);
			}
		} else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'] )
			);
		}
	}

	/**
	 * Give a score based on keyword found in first 100 words from content
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_first100words( $page_content, $focus_kw, $words_limit=100 )
	{
		$words_limit = isset($words_limit) && $words_limit>0 ? $words_limit : $this->rules_settings['keyword_first100_words'];

		$page_content = trim($page_content);

		$kw_occ = $this->get_first_last_words($page_content, $focus_kw, array(
			'what'			=> 'first100_words',
			'words_limit'	=> $words_limit,
		));

		$msgs = array(
			'missing' 			=> __( "First 100 words - Bad, please add some content for the page.", $this->the_plugin->localizationName ),
			'less_words' 		=> __( "Bad, the page content doesn't contains your focus keyword in the first 100 words.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content contains your focus keyword in the first 100 words ( the number of focus keyword occurences is %d ).", $this->the_plugin->localizationName )
		);

		if ( $page_content == "" ) {
			return array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			);
		}
		else {
			if( $kw_occ ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $kw_occ)
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> $msgs['less_words']
				);
			}
		}
	}

	/**
	 * Give a score based on keyword found in last 100 words from content
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	public function score_last100words( $page_content, $focus_kw, $words_limit=100 )
	{
		$words_limit = isset($words_limit) && $words_limit>0 ? $words_limit : $this->rules_settings['keyword_last100_words'];

		$page_content = trim($page_content);

		$kw_occ = $this->get_first_last_words($page_content, $focus_kw, array(
			'what'			=> 'last100_words',
			'words_limit'	=> $words_limit,
		));

		$msgs = array(
			'missing' 			=> __( "Last 100 words - Bad, please add some content for the page.", $this->the_plugin->localizationName ),
			'less_words' 		=> __( "Bad, the page content doesn't contains your focus keyword in the last 100 words.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content contains your focus keyword in the last 100 words ( the number of focus keyword occurences is %d ).", $this->the_plugin->localizationName )
		);

		if ( $page_content == "" ) {
			return array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			);
		}
		else {
			if( $kw_occ ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $kw_occ)
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> $msgs['less_words']
				);
			}
		}
	}

	/**
	 * Give a score based on the number of external links from content
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @param string  $permalink
	 * @return array  $results   The results array.
	 */
	public function score_links_external( $page_content, $focus_kw, $permalink )
	{
		$page_content = trim($page_content);

		$links = $this->get_content_links( $page_content, $focus_kw, $permalink );
		//var_dump('<pre>', $links , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;   

		$msgs = array(
			'missing' 			=> __( "Bad, the page content has no external links.", $this->the_plugin->localizationName ),
			'bad' 				=> __( "Bad, the page content has %d external dofollow links and %d external nofollow links.", $this->the_plugin->localizationName ),
			'poor'				=> __( "Poor, the page content has %d external dofollow links and %d external nofollow links.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content has %d external links and none of them are nofollow.", $this->the_plugin->localizationName )
		);

		if( $links['external']['total'] > 0 ){
			if( $links['external']['dofollow'] == $links['external']['total'] ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $links['external']['dofollow'])
				);
			}
			else if( $links['external']['dofollow'] ){
				return array(
					'score' 	=> 0.5,
					'msg'		=> sprintf($msgs['poor'], $links['external']['dofollow'], $links['external']['nofollow'])
				);
			}
			else {
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['bad'], $links['external']['dofollow'], $links['external']['nofollow'])
				);
			}
		} else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'])
			);
		}
	}

	/**
	 * Give a score based on the number of internal links from content
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @param string  $permalink
	 * @return array  $results   The results array.
	 */
	public function score_links_internal( $page_content, $focus_kw, $permalink )
	{
		$page_content = trim($page_content);

		$links = $this->get_content_links( $page_content, $focus_kw, $permalink );
		//var_dump('<pre>', $links , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;   

		$msgs = array(
			'missing' 			=> __( "Bad, the page content has no internal links.", $this->the_plugin->localizationName ),
			'bad' 				=> __( "Bad, the page content has %d internal dofollow links and %d internal nofollow links.", $this->the_plugin->localizationName ),
			'poor'				=> __( "Poor, the page content has %d internal dofollow links and %d internal nofollow links.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the page content has %d internal links and none of them are nofollow.", $this->the_plugin->localizationName )
		);

		if( $links['internal']['total'] > 0 ){
			if( $links['internal']['dofollow'] == $links['internal']['total'] ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $links['internal']['dofollow'])
				);
			}
			else if( $links['internal']['dofollow'] ){
				return array(
					'score' 	=> 0.5,
					'msg'		=> sprintf($msgs['poor'], $links['internal']['dofollow'], $links['internal']['nofollow'])
				);
			}
			else {
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['bad'], $links['internal']['dofollow'], $links['internal']['nofollow'])
				);
			}
		} else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'])
			);
		}
	}

	/**
	 * Give a score based on the number of links from content, which contains keyword
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @param string  $permalink
	 * @return array  $results   The results array.
	 */
	public function score_links_competing( $page_content, $focus_kw, $permalink )
	{
		$page_content = trim($page_content);

		$links = $this->get_content_links( $page_content, $focus_kw, $permalink );
		//var_dump('<pre>', $links , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;   

		$msgs = array(
			'missing' 			=> __( "Competing Links - the page content has no links.", $this->the_plugin->localizationName ),
			'bad' 				=> __( "Bad, the page content has %d potential competing links (which contains your focus keyword): <ul class='psp-competing-links'><li>%s</li></ul>", $this->the_plugin->localizationName ),
			'great'				=> __( "Great, the page content doesn't have any potential competing links (which contains your focus keyword).", $this->the_plugin->localizationName )
		);

		if ( $links['all'] ) {
			if( count($links['hasKeywordList']['nonSelf']) > 0 ){

				$listToShow = array();
				foreach ($links['hasKeywordList']['nonSelf'] as $_link) {
					$listToShow[] = '<a href="' . $_link['href'] . '" target="_blank">' . $_link['text'] . '</a>';
				}
				$listToShow = implode('</li><li>', $listToShow);

				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['bad'], count($links['hasKeywordList']['nonSelf']), $listToShow)
				);
			}
			else {
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['great'])
				);
			}
		} else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'])
			);
		}
	}

	/**
	 * Give a score based on keyword density
	 *
	 * @param string  $density
	 * @return array $results   The results array.
	 */
	public function score_keyword_density( $density_arr=array() )
	{
		//keyword density
		$__nb_words = $density_arr['nb_words'];
		$__kw_occurences = $density_arr['kw_occurences'];
		$__density = $density_arr['density'];

		$good_density_min = $this->rules_settings['keyword_density_good_min'];
		$good_density_max = $this->rules_settings['keyword_density_good_max'];
		$poor_density_min = $this->rules_settings['keyword_density_poor_min'];
		$poor_density_max = $this->rules_settings['keyword_density_poor_max'];

		$msgs = array(
			'missing' 			=> __( "Keyword density - Bad, please add some content for the page.", $this->the_plugin->localizationName ),
			'missing_kw' 		=> __( "Bad, keyword density is 0%%, because the focus keyword <strong>%s</strong> does not appear in the page content.", $this->the_plugin->localizationName ),
			'bad' 				=> __( "Bad, keyword density is %.1f%% and it's not between %.1f%% and %.1f%%, which is the recommended interval. Your page content has %d allowed words and the number of focus keyword occurences in the content is %d.", $this->the_plugin->localizationName ),
			'poor' 				=> __( "Poor, keyword density is %.1f%% and it's not between %.1f%% and %.1f%%, which is the recommended interval. Your page content has %d allowed words and the number of focus keyword occurences in the content is %d.", $this->the_plugin->localizationName ),
			'good'				=> __( "Great, keyword density is %.1f%% and it's between %.1f%% and %.1f%%, which is the recommended interval. Your page content has %d allowed words and the number of focus keyword occurences in the content is %d.", $this->the_plugin->localizationName ),

			'__needed'			=> __( "You need between %d and %d focus keyword occurences in the page content to be situated in the recommended interval.", $this->the_plugin->localizationName ),
		);
		
		// needed occurences for good density
		$msgs['__needed'] = sprintf(
			$msgs['__needed'],
			$this->get_occurences_from_density(array(
				'nb_words'	=> $__nb_words,
				'density'	=> $good_density_min
			)),
			$this->get_occurences_from_density(array(
				'nb_words'	=> $__nb_words,
				'density'	=> $good_density_max
			))
		);

		$ret = array(
			'details'		=> array(
				'nb_words' 		=> $__nb_words,
				'kw_occurences' => $__kw_occurences,
				'density'		=> $__density
			)
		);

		if ( $__nb_words == 0 ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $__kw_occurences == 0 ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing_kw'], $density_arr['kw'])
			));
		}
		else {
			if ( $__density>=$good_density_min && $__density<=$good_density_max ) {
				return array_merge($ret, array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $__density, $good_density_min, $good_density_max, $__nb_words, $__kw_occurences)
				));
			}
			else if ( $__density>=$poor_density_min && $__density<=$poor_density_max ) {
				return array_merge($ret, array(
					'score' 	=> 0.5,
					'msg'		=> sprintf($msgs['poor'], $__density, $good_density_min, $good_density_max, $__nb_words, $__kw_occurences)
						. ' '.$msgs['__needed']
				));
			}
			else {
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['bad'], $__density, $good_density_min, $good_density_max, $__nb_words, $__kw_occurences)
						. ' '.$msgs['__needed']
				));
			}
		}
	}

	
	/**
	 * Utils
	 */
	// strip shortcodes
	public function strip_shortcode( $text ) {
		return preg_replace( '`\[[^\]]+\]`s', '', $text );
	}

	// get custom user stop words list
	public function get_stop_words( $from='' ) {
		$stopwords = array(); //array("a", "you", "if")

		// verify if it's allowed here
		$forContent = isset($this->optimizeSettings['meta_keywords_stop_words_content'])
			? (string) $this->optimizeSettings['meta_keywords_stop_words_content'] : 'yes';
		$forContent = 'yes' == $forContent ? true : false;

		// keyword_density, enough_words, meta_keywords
		if ( in_array($from, array('first100_words', 'last100_words')) ) {
			return $stopwords;
		}
		if ( in_array($from, array('keyword_density', 'enough_words', 'seo_title_enough_words', 'first100_words', 'last100_words')) && ! $forContent ) {
			return $stopwords;
		}


		// build it
		$stopwords_db = isset($this->optimizeSettings['meta_keywords_stop_words']) ?
			(string) $this->optimizeSettings['meta_keywords_stop_words'] : 'a, you, if';
		$stopwords_db = trim($stopwords_db);

		if( isset($stopwords_db) && $stopwords_db != '' ) {
			$stopwords_db = explode(',', $stopwords_db);
			$stopwords_db = array_map('trim', $stopwords_db);
			$stopwords = $stopwords_db;
		}
		return $stopwords;
	}

	// get custom min number characters for a word
	public function get_word_min_chars( $from='' ) {
		// verify if it's allowed here
		$forContent = isset($this->optimizeSettings['word_min_chars_content'])
			? (string) $this->optimizeSettings['word_min_chars_content'] : 'yes';
		$forContent = 'yes' == $forContent ? true : false;

		// keyword_density, enough_words, meta_keywords
		if ( in_array($from, array('first100_words', 'last100_words')) ) {
			return 0;
		}
		if ( in_array($from, array('keyword_density', 'enough_words', 'seo_title_enough_words', 'first100_words', 'last100_words')) && ! $forContent ) {
			return 0;
		}


		// build it
		$word_min_chars = isset($this->optimizeSettings['word_min_chars']) ? (int) $this->optimizeSettings['word_min_chars'] : 4;
		return $word_min_chars;
	}

	// number of occurences of needle in string
	public function get_count_occurences( $string, $needle ) {
		$string = $this->filter_content( $string );

		//$string = $this->the_plugin->utf8->strtolower($string); // already done in filter content
		$needle = $this->the_plugin->utf8->strtolower($needle);
		$found = $this->the_plugin->utf8->substr_count($string, $needle);
		//var_dump('<pre>', $string, $needle, $found, '</pre>');

		return $found;
	}

	// parse a text to retrieve it's words
	public function get_content_words( $string, $pms=array() ) {
		$ret = array(
			'words'			=> array(),
			'words_len'		=> 0,
		);

		$pms = array_replace_recursive(array(
			'unique_words'			=> false, // retrieve unique words

			'word_min_chars'		=> 0, // word min characters; 0 = deactivated
			'stop_words'			=> array(), // list of words which are removed from content

			'how_many_words'		=> -1, // how many words to retrive from content; -1 = retrieve all
			'first_words_limit'		=> 0, // retrieve first X words; 0 = deactivated
			'last_words_limit'		=> 0, // retrieve last X words; 0 = deactivated

			'frequency'				=> false, // get words by frequency? false = deactivated; word frequency = the number of occurences in content
			'frequency_sort_dir'	=> 'NONE', // get words by frequency and in sorted direction; values: NONE | ASC | DESC; NONE = not sorted
		), $pms);
		extract( $pms );

		$frequency_sort_dir = in_array($frequency_sort_dir, array('NONE', 'ASC', 'DESC')) ? $frequency_sort_dir : 'NONE';
		if ( $first_words_limit || $last_words_limit ) {
			$how_many_words = -1;
		}

		// :: filter content
		$string = $this->filter_content( $string );
		if ( '' == $string ) {
			return $ret;
		}

		// :: retrieve words
		//preg_match_all('/\b.*?\b/i', $string, $match_words);
		preg_match_all('/(?:\\pL|\\pN)[\\pL\\pN\\p{Mn}\'-]*/ui', $string, $match_words);
		//var_dump('<pre>', $string, $match_words ,'</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		$match_words = $match_words[0];
		foreach ( $match_words as $key => $item ) {
			if ( $item == '' ) {
				unset($match_words[$key]);
			}
			else if ( ! empty($stop_words) && in_array($this->the_plugin->utf8->strtolower($item), $stop_words) ) {
				unset($match_words[$key]);
			}
			else if ( $word_min_chars && ( $this->the_plugin->utf8->strlen($item) < $word_min_chars ) ) {
				unset($match_words[$key]);
			}
		}

		$keywords = is_array($match_words) ? $match_words : array();
		$keywords = array_values($keywords);
		//var_dump('<pre>', $keywords , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

		// frequency IS Activated
		if ( $frequency ) {
			$word_count = $this->the_plugin->utf8->str_word_count( implode(" ", $keywords) , 1);
			//var_dump('<pre>', $word_count , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			$keywords = array_count_values($word_count);

			if ( 'ASC' == $frequency_sort_dir ) {
				asort($keywords);
			} else if ( 'DESC' == $frequency_sort_dir ) {
				arsort($keywords);
			}

			if ( $unique_words ) {
				$keywords = $this->array_fill_keys( array_keys($keywords), 1 );
			}
		}
		// frequency is NOT activated
		else {
			if ( $unique_words ) {
				$keywords = array_flip( $keywords );
				$keywords = $this->array_fill_keys( array_keys($keywords), 1 );
			}
		}
		//var_dump('<pre>', $keywords , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

		if ( $first_words_limit ) {
			$keywords = array_slice($keywords, 0, $first_words_limit);
		}
		if ( $last_words_limit ) {
			$keywords = array_slice($keywords, -1, $last_words_limit);
		}
		if ( $how_many_words > 0 ) {
			$keywords = array_slice($keywords, 0, $how_many_words);
		}

		$ret['words'] = $keywords;
		$ret['words_len'] = ! $frequency && ! $unique_words ? count($keywords) : array_sum($keywords);
		//var_dump('<pre>', $ret , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		return $ret;
	}

	// get text words from cache if exists
	public function get_cached_content_words( $string, $pms=array() ) {
		extract($pms);
		$cache = self::$cache;

		$uniqueid = md5( $string . serialize($pms) );

		$is_cached = false;
		if ( $cache && isset($this->_content_words["$uniqueid"])
			//&& ! empty($this->_content_words["$uniqueid"])
		) {
			$content_words = $this->_content_words["$uniqueid"];
			$is_cached = true;
		} else {
			$content_words = $this->get_content_words($string, $pms);
			if ( $cache ) {
				$this->_content_words["$uniqueid"] = $content_words;
			}
		}
		//var_dump('<pre>', $uniqueid, $is_cached, $content_words['words_len'] ,'</pre>');
		return $content_words;
	}

	// transform text in html php query object & cache it
	public function get_cached_content_as_htmlquery( $page_content ) {
		$html = null;
		$cache = self::$cache;

		$uniqueid = md5( serialize($page_content) );

		$is_cached = false;
		if ( $cache && isset($this->_content_html["$uniqueid"])
			&& is_object($this->_content_html["$uniqueid"])
		) {
			$html = $this->_content_html["$uniqueid"];
			$is_cached = true;
		} else {
			if ( !empty($this->the_plugin->charset) )
				$html = pspphpQuery::newDocumentHTML( $page_content, $this->the_plugin->charset );
			else
				$html = pspphpQuery::newDocumentHTML( $page_content );
			
			if ( $cache ) {
				$this->_content_html["$uniqueid"] = $html;
			}
		}
		//var_dump('<pre>', $uniqueid, $is_cached, $html->documentID ,'</pre>');
		return $html;
	}

	// get text html tags & others from cache if exists
	public function get_cached_content_elem( $what, $page_content, $pms=array() ) {
		extract($pms);
		$cache = self::$cache;

		$pms2 = $pms;
		if ( isset($__exclude) ) {
			$pms2 = array_intersect_key( $pms, array_flip($__exclude), array('__exclude' => array()) );
		}
		$uniqueid = md5( $page_content . serialize($pms2) );

		$is_cached = false;
		if ( $cache && isset($this->_content_elem["$uniqueid"], $this->_content_elem["$uniqueid"]["$what"])
			//&& ! empty($this->_content_words["$uniqueid"]["$what"])
		) {
			$ret = $this->_content_elem["$uniqueid"]["$what"];
			$is_cached = true;
		} else {
			$ret = array(
				'result'			=> null,
			);
			switch ($what) {
				case 'first_paragraph':
					$ret['result'] = $this->gen_first_paragraph($page_content);
					break;

				case 'meta_desc':
					$ret['result'] = $this->gen_meta_desc($page_content);
					break;

				case 'meta_keywords':
					$ret['result'] = $this->gen_meta_keywords($page_content, $how_many);
					break;

				case 'html_frame':
					$ret['result'] = $html->find('frame');
					$ret['size'] = $ret['result']->size();
					break;

				case 'html_iframe':
					$ret['result'] = $html->find('iframe');
					$ret['size'] = $ret['result']->size();
					break;

				case 'html_object':
					$ret['result'] = $html->find('embed, object');
					$ret['size'] = $ret['result']->size();
					break;

				case 'html_video':
					$ret['result'] = $html->find('video');
					$ret['size'] = $ret['result']->size();
					break;

				case 'html_images':
					$ret['result'] = $html->find('img');
					$ret['size'] = $ret['result']->size();
					break;

				case 'html_bold':
					$ret['result'] = $html->find('bold, strong, b, span[style*=bold]');
					$ret['size'] = $ret['result']->size();
					break;

				case 'html_italic':
					$ret['result'] = $html->find('em, i, span[style*=italic]');
					$ret['size'] = $ret['result']->size();
					break;

				case 'html_underline':
					$ret['result'] = $html->find('u, span[style*=underline]');
					$ret['size'] = $ret['result']->size();
					break;

				case 'html_subheadings':
					$ret['result'] = $html->find('h1, h2, h3');
					$ret['size'] = $ret['result']->size();
					break;

				case 'html_links':
					$ret['result'] = $html->find('a');
					$ret['size'] = $ret['result']->size();
					break;

				case 'parse_links':
					$ret['result'] = $this->parse_content_links($cached_elem, $focus_kw, $permalink, array());
					break;
			}
			if ( $cache ) {
				$this->_content_elem["$uniqueid"]["$what"] = $ret;
			}
		}
		//var_dump('<pre>', $what, $uniqueid, $is_cached, ( isset($ret['size']) ? $ret['size'] : $ret ) ,'</pre>');
		return $ret;
	}

	// parse links to identify their properties
	public function parse_content_links( $links, $focus_kw, $permalink, $pms=array() ) {
		extract($pms);

		$the_links = $links['result'];
		$total_links = $links['size'];

		$ret = array(
			// all links found
			// all = external + internal + nonhttp
			// all = dofollow + nofollow
			'all'				=> 0,

			// all links found that contains keyword in text
			'hasKeyword'		=> array(
				'total'				=> 0,
				'external'			=> 0,
				'internal'			=> 0,
				'nonhttp'			=> 0,
				'dofollow'			=> 0,
				'nofollow'			=> 0,
				'pointToSelf'		=> 0,
			),
			// link href(s)
			'hasKeywordList'	=> array(
				'nonSelf'			=> array(),
				'pointToSelf'		=> array(),
			),

			// links grouped
			'external'			=> $this->_link_set_default(), // external links
			'internal'			=> $this->_link_set_default(), // internal links
			'nonhttp'			=> $this->_link_set_default(), // without http(s) or // prefix at href beginging
		);

		if( $total_links > 0 ){
			foreach( $the_links as $tag ) {
				$tag = pspPQ($tag); // cache the object

				$href = $tag->attr('href');
				$href = trim($href);
				$href_filtered = $this->_link_href_filter($href);
				$permalink_filtered = $this->_link_href_filter($permalink);

				$rel = $tag->attr('rel');
				$rel = trim($rel);

				$text = $tag->text();
				$text = trim($text);

				$linkFollow = ( ('' != $rel) && preg_match('/nofollow/i', $rel) == true ? 'nofollow' : 'dofollow' );
				$linkType = $this->_get_link_type( $href_filtered, $permalink_filtered );
				$linkPointToSelf = $this->_is_link_point_to_self( $href_filtered, $permalink_filtered );

				$linkHasKeyword = false;
				if ( $text != '' ) {
					$text = $this->the_plugin->utf8->strtolower($text);

					if ( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $text) == true ) {
						$linkHasKeyword = true;
					}
				}

				// add to - per group total
				$ret["$linkType"]["total"]++;
				$ret["$linkType"]["$linkFollow"]++;
				if ( $linkPointToSelf ) {
					$ret["$linkType"]["pointToSelf"]++;
				}
				if ( $linkHasKeyword ) {
					$ret["$linkType"]["hasKeyword"]["total"]++;
					$ret["$linkType"]["hasKeyword"]["$linkFollow"]++;
					if ( $linkPointToSelf ) {
						$ret["$linkType"]["hasKeyword"]["pointToSelf"]++;
					}
				}

				// add to - general total
				$ret['all']++;
				if ( $linkHasKeyword ) {
					$_toKeep = array( 'text' => $text, 'href' => $href, 'type' => $linkType );

					$ret["hasKeyword"]["total"]++;
					$ret["hasKeyword"]["$linkType"]++;
					$ret["hasKeyword"]["$linkFollow"]++;
					if ( $linkPointToSelf ) {
						$ret["hasKeyword"]["pointToSelf"]++;
						$ret["hasKeywordList"]["pointToSelf"][] = $_toKeep;
					}
					else {
						$ret["hasKeywordList"]["nonSelf"][] = $_toKeep;
					}
				}

				//var_dump('<pre>', $href, $text, $rel, '----', $linkFollow, $linkType, $linkPointToSelf, $linkHasKeyword ,'</pre>'); 
				//continue 1;
			}
		}

		//var_dump('<pre>', $ret , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		return $ret;
	}

	// set default link values
	private function _link_set_default() {
		return array(
			'total'			=> 0, // total number of links
			'dofollow'		=> 0, // links without nofollow attribute
			'nofollow'		=> 0, // links with nofollow attribute
			'pointToSelf'	=> 0, // links pointing to current page which is analyzed | only for internal links
			'hasKeyword'	=> array( // links with keyword in href
				'total'			=> 0,
				'dofollow'		=> 0,
				'nofollow'		=> 0,
				'pointToSelf'	=> 0,
			),
		);
	}

	// filter link
	private function _link_href_filter( $link ) {
		$link_ = $link;
		
		$link_ = explode('#', $link_); // remove hash
		$link_ = $link_[0];

		$link_ = explode('?', $link_); // remove query string
		$link_ = $link_[0];

		$link_ = trim($link_); // remove trailing spaces

		$link_ = preg_replace('/\/$/iu', '', $link_); // remove trailing slash if one exists
		$link_ = $link_ . '/'; // add trailing slash
		return $link_;
	}

	// get link type: nonhttp | external | internal
	private function _get_link_type( $link, $permalink ) {
		// compare link against permalink and return link type
		$type = 'nonhttp';

		$link_ = parse_url( $link );
		$link_scheme = isset($link_['scheme']) ? strtolower($link_['scheme']) : '';
		$link_host = isset($link_['host']) ? strtolower($link_['host']) : '';
		//var_dump('<pre>',$link_schema, $link_host ,'</pre>');

		$permalink_ = parse_url( $permalink );
		$permalink_host = isset($permalink_['host']) ? strtolower($permalink_['host']) : '';

		if ( in_array($link_scheme, array('', 'http', 'https')) ) {
			$type = 'external';

			if ( $link_host == $permalink_host ) {
				$type = 'internal';
			}
		}
		return $type;
	}

	// link point to current page?
	private function _is_link_point_to_self( $link, $permalink ) {
		// compare link against permalink to see if they are the same
		$regex = '/^((http(s?):\/\/)|\/\/)/iu';
		$link_ = preg_replace($regex, '', $link);
		$permalink_ = preg_replace($regex, '', $permalink);
		//var_dump('<pre>',$link_, $permalink_ ,'</pre>');

		if ( $link_ == $permalink_ ) {
			return true;
		}
		return false;
	}

	private function filter_content( $string ) {
		$string = $this->strip_shortcode( $string );

		$string = preg_replace('#<br\s*/?>#i', " ", $string);
		$string = strip_tags($string);

		$string = str_replace("\n", ' ', $string);
		$string = preg_replace('/\s(\s+)/im', ' ', $string); //preg_replace('/ss+/i', '', $string);

		// remove links (non tags)
		$string = preg_replace('/(((http(s?):\/\/)|\/\/)[^\s]+)/uis', '', $string);

		$string = trim($string); // trim the string

		//$string = preg_replace('/[^a-zA-Z -]/', '', $string); // only take alphabet characters, but keep the spaces and dashes too…
		$string = $this->the_plugin->utf8->strtolower($string); // make it lowercase

		// remove non (letters & numbers) - with look ahead & look behind assertions
		$string = preg_replace('/(?<=[\s\pP\pS\pC]|^)[^ \pL\pN]+(?=[\s\pP\pS\pC]|$)/uis', '', $string);

		return $string;
	}

	private function get_density_by_formula( $pms=array() ) {
		$pms = array_replace_recursive(array(
			'nb_words'			=> 0,
			'kw_occ'			=> 0,
		), $pms);
		extract( $pms );

		$density = 0;
		if ( $nb_words>0 && $kw_occ>0 ) {
			$density = ( $kw_occ / $nb_words ) * 100;
			$density = number_format($density, 1);
		}
		return $density;
	}

	private function get_occurences_from_density( $pms=array() ) {
		$pms = array_replace_recursive(array(
			'nb_words'			=> 0,
			'density'			=> 0,
		), $pms);
		extract( $pms );

		$kw_occ = 0;
		if ( $nb_words>0 && $density>0 ) {
			$kw_occ = ( $density * $nb_words ) / 100;
			$kw_occ = ceil($kw_occ);
		}
		return $kw_occ;
	}

	private function array_fill_keys( $target, $value='' ) {
		$filledArray = array();
		if ( is_array($target) ) {
			foreach ($target as $key => $val) {
				$filledArray[$val] = is_array($value) ? $value[$key] : $value;
			}
		}
		return $filledArray;
	}
}
} // end class exists!