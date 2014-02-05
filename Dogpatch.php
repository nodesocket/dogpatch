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

    require_once(__DIR__ . "/Curl.php");
    require_once(__DIR__ . "/Util.php");

    define("IS_VALID_JSON", "IS_VALID_JSON");
    define("VAR_EXPORT", true);
    define("NO_VAR_EXPORT", false);

    class Dogpatch extends Curl {
        private $response;
        private $status_code;
        private $headers;
        private $body;

        public function __construct(array $options = array()) {
            $default_options = array(
                "username" => null,
                "password" => null,
                "timeout" => 60,
                "ssl_verifypeer" => true,
                "verbose" => false
            );

            $options = array_merge($default_options, $options);

            ////
            // Really php? This makes baby jesus cry.
            ////
            call_user_func_array("parent::__construct", $options);
        }

        public function get($url, array $headers = array()) {
            $this->response = $this->get_request($url, $headers);
            return $this;
        }

        public function post($url, array $post_data = array(), array $headers = array()) {
            $this->response = $this->post_request($url, $post_data, $headers);
            return $this;
        }

        public function put($url, array $headers = array()) {
            $this->response = $this->put_request($url, $headers);
            return $this;
        }

        public function delete($url, array $headers = array()) {
            $this->response = $this->delete_request($url, $headers);
            return $this;
        }

        public function head($url, array $headers = array()) {
            $this->response = $this->head_request($url, $headers);
            return $this;
        }

        public function assert_status_code($asserted_staus_code) {
            if(empty($this->status_code)) {
                $this->status_code = $this->get_curl_info(CURLINFO_HTTP_CODE);
            }

            if(intval($asserted_staus_code) !== intval($this->status_code)) {
                throw new Exception("Asserted status code '$asserted_staus_code' does not equal returned status code '$this->status_code'.");
            }

            return $this;
        }

        public function assert_headers_exist(array $asserted_headers = array()) {
            if(empty($this->headers)) {
                $headers_raw = substr($this->response, 0, $this->get_curl_info(CURLINFO_HEADER_SIZE));
                $this->headers = array_change_key_case(http_parse_headers($headers_raw), CASE_LOWER);
            }

            $asserted_headers = array_map('strtolower', $asserted_headers);

            foreach($asserted_headers as $header) {
                if(!isset($this->headers[$header])) {
                    throw new Exception("Asserted header '$header' is not set.");
                }
            }

            return $this;
        }

        public function assert_headers(array $asserted_headers = array()) {
            if(empty($this->headers)) {
                $headers_raw = substr($this->response, 0, $this->get_curl_info(CURLINFO_HEADER_SIZE));
                $this->headers = array_change_key_case(http_parse_headers($headers_raw), CASE_LOWER);
            }

            ////
            // Associated array
            ////
            if(is_assoc($asserted_headers)) {
                $asserted_headers = array_change_key_case($asserted_headers, CASE_LOWER);

                foreach($asserted_headers as $k => $v) {
                    if(!array_key_exists($k, $this->headers)) {
                        throw new Exception("Asserted header '$k' is not set.");
                    }

                    if(is_array($this->headers[$k])) {
                        if(!in_array($v, $this->headers[$k])) {
                            throw new Exception("Asserted header '$k' exists, but the value '$v' does not match.");
                        }
                    } else {
                        if($v !== $this->headers[$k]) {
                            throw new Exception("Asserted header '$k=$v' does not equal returned header '$k=" . $this->headers[$k] . "'.");
                        }
                    }
                }
            }
            ////
            // Standard indexed array, call assert_headers_exist() instead
            ////
            else {
                $this->assert_headers_exist($asserted_headers);
            }

            return $this;
        }

        public function assert_body($asserted_body) {
            if(empty($this->body)) {
                $this->body = substr($this->response, $this->get_curl_info(CURLINFO_HEADER_SIZE));
            }

            if($asserted_body === IS_VALID_JSON) {
                if(json_decode($this->body === null)) {
                    throw new Exception("Asserted body is not valid JSON.");
                }

                return $this;
            }

            if(!@preg_match($asserted_body, $this->body, $_matches)) {
                throw new Exception("Asserted body '$asserted_body' does not match.");
            }

            return $this;
        }

        public function assert_body_json($asserted, $on_mismatch_var_export = false) {
            if(empty($this->body)) {
                $this->body = substr($this->response, $this->get_curl_info(CURLINFO_HEADER_SIZE));
            }

            $body = json_decode($this->body);

            if($body === null) {
                throw new Exception("Asserted body is not valid JSON.");
            }

            if($asserted != $body) {
                if($on_mismatch_var_export) {
                    throw new Exception("Asserted body does not exactly match returned body.\n\n--------------- ASSERTED BODY ---------------\n" . var_export($asserted, true) . "\n\n--------------- RETURNED BODY ---------------\n" . var_export($body, true) . "\n\n");
                } else {
                    throw new Exception("Asserted body does not exactly match returned body.");
                }
            }

            return $this;
        }

        public function close() {
            parent::close();
            unset($this->response);
            unset($this->status_code);
            unset($this->headers);
            unset($this->body);
        }
    }
?>