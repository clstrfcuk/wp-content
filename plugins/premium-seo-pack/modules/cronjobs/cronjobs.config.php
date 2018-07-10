<?php
/*
* Define class pspCronjobsConfig
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspCronjobsConfig') != true) {
    class pspCronjobsConfig
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
        private $module_folder_path = '';
        private $module = '';
        
        static protected $_instance;
        
        public $is_admin = false;
        
        public $alias = '';
        public $localizationName = '';
        
        public $custom_schedules = array();
        public $config = array();
        

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct($psp)
        {
            //global $psp;
   
            $this->the_plugin = $psp;
            $this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/cronjobs/';
            $this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/cronjobs/';
            $this->module = isset($this->the_plugin->cfg['modules']['cronjobs']) ? $this->the_plugin->cfg['modules']['cronjobs'] : array();
            
            $this->alias = $this->the_plugin->alias;
            $this->localizationName = $this->the_plugin->localizationName;
 
            $this->is_admin = $this->the_plugin->is_admin;
        }
        
        /**
        * Singleton pattern
        *
        * @return pspCronjobsConfig Singleton instance
        */
        static public function getInstance()
        {
            if (!self::$_instance) {
                self::$_instance = new self;
            }

            return self::$_instance;
        }
        
        public function get_custom_schedules() {
            $this->custom_schedules = array(
                '1min'    => array(
                    'interval'  => 60,
                    'display'   => __('Once every minute.', $this->localizationName)
                ),
                '2min'    => array(
                    'interval'  => 120,
                    'display'   => __('Once every 2 minutes.', $this->localizationName)
                ),
                '3min'    => array(
                    'interval'  => 180,
                    'display'   => __('Once every 3 minutes.', $this->localizationName)
                ),
                '5min'    => array(
                    'interval'  => 300,
                    'display'   => __('Once every 5 minutes.', $this->localizationName)
                ),
                '10min'    => array(
                    'interval'  => 600,
                    'display'   => __('Once every 10 minutes.', $this->localizationName)
                ),
                '15min'    => array(
                    'interval'  => 900,
                    'display'   => __('Once every 15 minutes.', $this->localizationName)
                ),
                '30min'    => array(
                    'interval'  => 1800,
                    'display'   => __('Once every half hour.', $this->localizationName)
                ),
                '45min'    => array(
                    'interval'  => 2700,
                    'display'   => __('Once every 45 minutes.', $this->localizationName)
                ),
            );
            $this->custom_schedules['debug'] = $this->custom_schedules['2min'];
            
            return $this->custom_schedules;
        }

        /**
         * Cronjobs config array (key = cron_id ; (plugin alias + cron_id) pair is used as wp hook)
         *      - status_default: default status for a new cron (new, failed, done, running, stop)
         *      - recurrence: cron recurrence (in seconds)
         *      - recurrence_wp: cron recurrence using WP Cron schedules
         *      - max_execution_time: maximum execution time for a cron (in seconds)
         *      - start_hour: cron start hour
         *      - extra: extra parameters per cron if necessary
         *        - depedency: cron script is related to the crons in this array!
         * 
         * Dynamic fields: saved in options table or (distinct cronjobs table - in the future maybe)
         *      - status: current status (new, failed, done, running, stop)
         *      - start_time: cron start time (timestamp - to be compared with max_execution_time)
         *      - end_time: cron end time (just for debugging purpose)
         *      - last_status_message: cron last status message
         *      - run_duration: end_time - start_time
         *      -- next_run_date: cron next running date (NOT implemented; we use WP schedule functions)
         */
        public function get_config() {
            $this->config = array(
                'serp'   => array( // cycle to sync all current products in database
                    'is_active_default'     => 'no',
                    'status_default'        => 'new',
                    'recurrence'            => '1800',
                    'recurrence_wp'         => '30min',
                    'max_execution_time'    => '600', // 10 minutes
                    'start_hour'            => 'now',
                    'depedency'             => array(),
                    'extra'                 => array(
                    ),
                ),
                'facebook'       => array( // products assets download
                    'is_active_default'     => 'no',
                    'status_default'        => 'new',
                    'recurrence'            => '2700',
                    'recurrence_wp'         => '45min',
                    'max_execution_time'    => '600', // 10 minutes
                    'start_hour'            => 'now',
                    'depedency'             => array(),
                    'extra'                 => array(
                    ),
                ),
                'alexa_rank'       => array( // products assets download
                    'is_active_default'     => 'yes',
                    'status_default'        => 'new',
                    'recurrence'            => '3600',
                    'recurrence_wp'         => 'hourly',
                    'max_execution_time'    => '300', // 5 minutes
                    'start_hour'            => 'now',
                    'depedency'             => array(),
                    'extra'                 => array(
                    ),
                ),
                'unblock_crons'			=> array( // unblock cronjobs
                    'is_active_default'     => 'yes',
                    'status_default'        => 'new',
                    'recurrence'            => '600',
                    'recurrence_wp'         => '10min',
                    'max_execution_time'    => '60', // 1 minute
                    'start_hour'            => 'now',
                    'depedency'             => array(),
                    'extra'                 => array(
                    ),
                ),

            );
            if ( empty($this->config) ) return;
            
            // depedency
            //foreach ($this->config as $cron_id => $cron) {
            //    if ( in_array($cron_id, array('serp', 'facebook', 'unblock_crons')) ) continue 1;
            //}
            
            // cron not set yet!
            //unset($this->config['assets_download']);
            
            return $this->config;
        }
    }
}

//$pspCronjobsConfig = new pspCronjobsConfig();
//$pspCronjobsConfig = pspCronjobsConfig::getInstance();