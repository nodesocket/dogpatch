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

    require_once(__dir__ . "/Curl.php");
    require_once(__dir__ . "/Util.php");

    class Dogpatch {
        private $curl_instance;
        private $curl_response;
        private $status_code;
        private $headers;
        private $body;

        public function __construct(array $options = array()) {
            $this->curl_instance = new Curl($options);
        }

        public function get($url, array $headers = array()) {
            $this->curl_response = $this->curl_instance->get_request($url, $headers);
        }

        public function post($url, array $data = array(), array $headers = array()) {
            $this->curl_response = $this->curl_instance->post_request($url, $data, $headers);
        }

        public function put($url, array $headers = array()) {
            $this->curl_response = $this->curl_instance->put_request($url, $headers);
        }

        public function delete($url, array $headers = array()) {
            $this->curl_response = $this->curl_instance->delete_request($url, $headers);
        }

        public function head($url , array $headers = array()) {
            $this->curl_response = $this->curl_instance->head_request($url, $headers);
        }

        public function assetStatusCode($asserted_staus_code) {
            if(empty($this->status_code)) {
                $this->status_code = $this->curl_instance->get_curl_info(CURLINFO_HTTP_CODE);
            }

            if(intval($asserted_staus_code) !== intval($this->status_code)) {
                throw new Exception("Asserted status code '$asserted_staus_code' does not equal returned status code '$status_code'.");
            }
        }

        public function assertHeaders(array $asserted_headers = array()) {
            if(empty($this->headers)) {
                $headers_raw = substr($this->curl_response, 0, $this->curl_instance->get_curl_info(CURLINFO_HEADER_SIZE));
                $this->headers = http_parse_headers($headers_raw);
            }

            foreach($asserted_headers as $k => $v) {
                if(!array_key_exists($k, $this->headers)) {
                    throw new Exception("Asserted header '$k' is not set.");
                }

                if(is_array($v)) {
                    foreach($v as $inner_v) {
                        if(!in_array($inner_v, $this->headers[$k])) {
                            throw new Exception("Asserted header '$k=$inner_v' is not set.");
                        }
                    }
                } else {
                    if($v != $this->headers[$k]) {
                        throw new Exception("Asserted header '$k=$v' does not equal returned header '$k=" . $this->headers[$k] . "'.");
                    }
                }
            }
        }
    }
?>