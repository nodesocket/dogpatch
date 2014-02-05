<?php
    /*
    # Copyright 2014 NodeSocket, LLC
    #
    # Licensed under the Apache License, Version 2.0 (the "License");
    # you may not use this file except in compliance with the License.
    # You may obtain a copy of the License at
    #
    # http://www.apache.org/licenses/LICENSE-2.0
    #
    # Unless required by applicable law or agreed to in writing, software
    # distributed under the License is distributed on an "AS IS" BASIS,
    # WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    # See the License for the specific language governing permissions and
    # limitations under the License.
    */

    class Curl {
        private $curl_object;

        protected function __construct($username = null, $password = null, $timeout = 60, $ssl_verifypeer = true, $verbose = false) {
            $this->curl_object = curl_init();

            curl_setopt($this->curl_object, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($this->curl_object, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl_object, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->curl_object, CURLOPT_MAXREDIRS, 10);
            curl_setopt($this->curl_object, CURLOPT_HEADER, 1);

            if($ssl_verifypeer) {
                curl_setopt($this->curl_object, CURLOPT_CAINFO, __DIR__ . '/assets/ssl/ca-bundle.crt');
                curl_setopt($this->curl_object, CURLOPT_SSL_VERIFYPEER, true);
            } else {
                curl_setopt($this->curl_object, CURLOPT_SSL_VERIFYPEER, false);
            }

            curl_setopt($this->curl_object, CURLOPT_USERAGENT, "dogpatch");

            if(!empty($username) || !empty($password)) {
                curl_setopt($this->curl_object, CURLOPT_USERPWD, $username . ":" . $password);
                curl_setopt($this->curl_object, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            }

            if($verbose) {
                curl_setopt($this->curl_object, CURLOPT_STDERR, fopen(__DIR__ . "/logs/curl_debug.log", "a+"));
                curl_setopt($this->curl_object, CURLOPT_VERBOSE, true);
            }
        }

        protected function get_request($url, array $headers = array()) {
            curl_setopt($this->curl_object, CURLOPT_URL, $url);

            if(!empty($headers)) {
                curl_setopt($this->curl_object, CURLOPT_HTTPHEADER, $headers);
            }

            return curl_exec($this->curl_object);
        }

        protected function post_request($url, array $post_data = array(), array $headers = array()) {
            curl_setopt($this->curl_object, CURLOPT_URL, $url);
            curl_setopt($this->curl_object, CURLOPT_POST, true);

            if(!empty($headers)) {
                curl_setopt($this->curl_object, CURLOPT_HTTPHEADER, $headers);
            }

            curl_setopt($this->curl_object, CURLOPT_POSTFIELDS, $post_data);

            return curl_exec($this->curl_object);
        }

        protected function put_request($url, array $headers = array()) {
            curl_setopt($this->curl_object, CURLOPT_URL, $url);
            curl_setopt($this->curl_object, CURLOPT_CUSTOMREQUEST, 'PUT');

            if(!empty($headers)) {
                curl_setopt($this->curl_object, CURLOPT_HTTPHEADER, $headers);
            }

            return curl_exec($this->curl_object);
        }

        protected function delete_request($url, array $headers = array()) {
            curl_setopt($this->curl_object, CURLOPT_URL, $url);
            curl_setopt($this->curl_object, CURLOPT_CUSTOMREQUEST, 'DELETE');

            if(!empty($headers)) {
                curl_setopt($this->curl_object, CURLOPT_HTTPHEADER, $headers);
            }

            return curl_exec($this->curl_object);
        }

        protected function head_request($url, array $headers = array()) {
            curl_setopt($this->curl_object, CURLOPT_URL, $url);
            curl_setopt($this->curl_object, CURLOPT_CUSTOMREQUEST, 'HEAD');

            if(!empty($headers)) {
                curl_setopt($this->curl_object, CURLOPT_HTTPHEADER, $headers);
            }

            return curl_exec($this->curl_object);
        }

        protected function get_curl_info($curl_option) {
            return curl_getinfo($this->curl_object, $curl_option);
        }

        protected function close() {
            curl_close($this->curl_object);
        }
    }
?>