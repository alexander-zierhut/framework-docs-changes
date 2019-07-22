<?php 
    /**
     * ResponseRequest handler
     */

    /**
     * Base class for the response and request object
     */
    class RequestResponseHandler {

        /**
         * @var z_framework $booter The framework object
         */
        public $booter;

        /**
         * Constructor every request and response object should have
         * @param z_framework $booter The framework object
         */
        public function __construct($booter) {
            $this->booter = $booter;
        }

        /**
         * Returns the ZViews directory
         * @return String
         */
        public function getZViews() {
            return $this->booter->z_views;
        }

        /**
         * Returns the ZControllers directory
         * @return String
         */
        public function getZControllers() {
            return $this->booter->z_controllers;
        }

        /**
         * Returns the framework root directory
         * @return String
         */
        public function getZRoot() {
            return $this->booter->z_framework_root; 
        }

        /**
         * Gets a booter settings
         * @param String $key Key of the settings
         * @return Any Value of the key
         */
        public function getBooterSettings($key = null) {
            return $key !== null ? $this->booter->settings[$key] : $this->booter->settings;
        }

    }
?>