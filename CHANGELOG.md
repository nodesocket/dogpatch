CHANGELOG
=========

### v0.1.2 - *3/8/2014*
- Fixed serious bug in **Curl.php**. Was not resetting curl options `CURLOPT_POST`, `CURLOPT_CUSTOMREQUEST`, and `CURLOPT_POSTFIELDS`.

### v0.1.1 - *3/8/2014*
- Added a special flag `IS_EMPTY` which can be passed into `assert_body()` to assert an empty response body.