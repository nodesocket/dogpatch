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
    define("IS_EMPTY", "IS_EMPTY");
    define("USE_REGEX", true);
    define("DONT_USE_REGEX", false);
    define("VAR_EXPORT", true);
    define("DONT_VAR_EXPORT", false);
    define("PRINT_JSON", true);
    define("DONT_PRINT_JSON", false);

    class Dogpatch extends Curl {
        private $response;
        private $status_code;
        private $headers;
        private $body;

        public function __construct(array $curl_options = array()) {
            $default_curl_options = array(
                "username" => null,
                "password" => null,
                "timeout" => 60,
                "ssl_verifypeer" => true,
                "verbose" => false
            );

            $curl_options = array_merge($default_curl_options, $curl_options);

            ////
            // Really php? This makes baby jesus cry.
            ////
            call_user_func_array("parent::__construct", $curl_options);
        }

        public function get($url, array $headers = array()) {
            $this->unset_class_vars();
            $this->response = $this->get_request($url, $headers);
            return $this;
        }

        public function post($url, array $post_data = array(), array $headers = array()) {
            $this->unset_class_vars();
            $this->response = $this->post_request($url, $post_data, $headers);
            return $this;
        }

        public function put($url, array $headers = array()) {
            $this->unset_class_vars();
            $this->response = $this->put_request($url, $headers);
            return $this;
        }

        public function delete($url, array $headers = array()) {
            $this->unset_class_vars();
            $this->response = $this->delete_request($url, $headers);
            return $this;
        }

        public function head($url, array $headers = array()) {
            $this->unset_class_vars();
            $this->response = $this->head_request($url, $headers);
            return $this;
        }

        public function assert_status_code($asserted_staus_code) {
            if(empty($this->status_code)) {
                $this->status_code = $this->get_curl_info(CURLINFO_HTTP_CODE);
            }

            if(intval($asserted_staus_code) !== intval($this->status_code)) {
                throw new Exception("Asserted status code '$asserted_staus_code' does not equal response status code '$this->status_code'.");
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
                            throw new Exception("Asserted header '$k' exists, but the response header value '$v' is not equal.");
                        }
                    } else {
                        if($v !== $this->headers[$k]) {
                            throw new Exception("Asserted header '$k=$v' does not equal response header '$k=" . $this->headers[$k] . "'.");
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

        public function assert_body($asserted_body, $use_regular_expression = false) {
            if(empty($this->body)) {
                $this->body = substr($this->response, $this->get_curl_info(CURLINFO_HEADER_SIZE));
            }

            if($asserted_body === IS_EMPTY) {
                if($this->body === false || $this->body === "") {
                    return $this;
                } else {
                    throw new Exception("Response body is not empty.");
                }
            }

            if($asserted_body === IS_VALID_JSON) {
                if(json_decode($this->body === null)) {
                    throw new Exception("Response body is invalid JSON.");
                }

                return $this;
            }

            if($use_regular_expression) {
                if(!@preg_match($asserted_body, $this->body)) {
                    throw new Exception("Asserted body '$asserted_body' does not match response body of '$this->body'.");
                }
            } else {
                if(strpos($asserted_body, $this->body)) {
                    throw new Exception("Asserted body '$asserted_body' does not equal response body of '$this->body'.");
                }
            }

            return $this;
        }

        public function assert_body_php($asserted, $on_not_equal_var_export = false) {
            if(empty($this->body)) {
                $this->body = substr($this->response, $this->get_curl_info(CURLINFO_HEADER_SIZE));
            }

            $body = json_decode($this->body);

            if($body === null) {
                throw new Exception("Response body is invalid JSON.");
            }

            if($asserted != $body) {
                if($on_not_equal_var_export) {
                    throw new Exception("Asserted body does not equal response body.\n\n--------------- ASSERTED BODY ---------------\n" . var_export($asserted, true) . "\n\n--------------- RESPONSE BODY ---------------\n" . var_export($body, true) . "\n\n");
                } else {
                    throw new Exception("Asserted body does not equal response body.");
                }
            }

            return $this;
        }

        public function assert_body_json_file($asserted_json_file, $on_not_equal_print_json = false) {
            if(!file_exists($asserted_json_file)) {
                throw new Exception("Asserted JSON file '$asserted_json_file' does not exist.");
            }

            $asserted = file_get_contents($asserted_json_file);
            if(json_decode($asserted) === null) {
                throw new Exception("Asserted JSON file is invalid JSON.");
            }

            if(empty($this->body)) {
                $this->body = substr($this->response, $this->get_curl_info(CURLINFO_HEADER_SIZE));
            }

            if(json_decode($this->body) === null) {
                throw new Exception("Response body is invalid JSON.");
            }

            $asserted = prettyPrintJSON($asserted);
            $body = prettyPrintJSON($this->body);

            if($asserted != $body) {
                if($on_not_equal_print_json) {
                    throw new Exception("Asserted JSON file does not equal response body.\n\n--------------- ASSERTED JSON FILE ---------------\n" . $asserted . "\n\n--------------- RESPONSE BODY ---------------\n" . $body . "\n\n");
                } else {
                    throw new Exception("Asserted JSON file does not equal response body.");
                }
            }

            return $this;
        }

        public function close() {
            parent::close();
            $this->unset_class_vars();
        }

        private function unset_class_vars() {
            unset($this->response);
            unset($this->status_code);
            unset($this->headers);
            unset($this->body);
        }
    }
?>