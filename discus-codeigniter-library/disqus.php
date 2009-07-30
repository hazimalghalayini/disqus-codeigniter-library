<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Disqus {

    function MY_Disqus()
    {
        $this->ci =& get_instance();
    }

    function doCurl($URL, $asPost = FALSE, $postArray = NULL){
        //initialise curl handle
        $curl = curl_init();

        // set URL and other appropriate options PHP4 style
//        curl_setopt($curl, CURLOPT_URL, "http://disqus.com/api/get_forum_list?user_api_key=$APIKey&api_version=1.1");
        curl_setopt($curl, CURLOPT_URL, $URL);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); //needed to allow curl to follow 'redirects' given by Disqus API
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); //needed to output result to response (rather than simple success boolean)
        if ($asPost) {                                    //if set as POST rather than get if $asPost = TRUE
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postArray); //pass the array of post fields to curl
        }

        // execute the curl command
        $data_raw = curl_exec($curl);

        // process the output
        $data = json_decode($data_raw, TRUE);       //convert JSON to array
        $data['header'] = curl_getinfo($curl);    //add header response code to our array
        $data['curl_error_number'] = curl_errno($curl);
	$data['curl_error_message'] = curl_error($curl);

        // close cURL resource, and free up system resources
        curl_close($curl);
        
        return $data;
    }

    function getForums($APIKey){
        return $this->doCurl("http://disqus.com/api/get_forum_list?user_api_key=$APIKey&api_version=1.1");
    }


    function getUserName($APIKey){
        $postArray['user_api_key'] = $APIKey;
        $postArray['api_version'] = '1.1';
        return $this->doCurl("http://disqus.com/api/get_user_name/", TRUE, $postArray);
    }


    function getForumAPIKey($APIKey, $forumID){
        $data =  $this->doCurl("http://disqus.com/api/get_forum_api_key?user_api_key=$APIKey&forum_id=$forumID&api_version=1.1");
        return $data;
    }

    function getForumPosts($APIKey, $forumID, $limit = NULL, $start = NULL, $filter = NULL, $exclude = NULL){
        $url="http://disqus.com/api/get_forum_posts?user_api_key=$APIKey&forum_id=$forumID&api_version=1.1";
        if ($limit) {
            $url .= "&limit=$limit";
        }
        if ($start){
            $url .= "&start=$start";
        }
        if ($filter) {
            $url .= "&filter=$filter";
        }
        if ($exclude) {
            $url .= "&exclude=$exclude";
        }
        $data =  $this->doCurl($url);
        return $data;
    }

    function getThreadList($APIKey, $forumID, $limit = 25, $start = 0){
        $url="http://disqus.com/api/get_thread_list?user_api_key=$APIKey&forum_id=$forumID&limit=$limit&start=$start&api_version=1.1";
        $data =  $this->doCurl($url);
        return $data;

    }

    function createPost($threadID, $message, $authorName, $authorEmail, $forumAPIKey, $partnerAPIKey = NULL, $createdAt = NULL, $IPAddress = NULL, $authorURL = NULL, $parentPost = NULL, $state = NULL){
        $postArray['forum_api_key'] = $forumAPIKey;
        $postArray['api_version'] = '1.1';
        $postArray['thread_id'] = $threadID;
        $postArray['message'] = $message;
        $postArray['author_name'] = $authorName;
        $postArray['author_email'] = $authorEmail;
        if ($partnerAPIKey) {
            $postArray['partner_api_key'] = $partnerAPIKey;
        }
        if ($createdAt) {
            $postArray['created_ad'] = $createdAt;
        }
        if ($IPAddress) {
            $postArray['ip_address'] = $IPAddress;
        }
        if ($authorURL) {
            $postArray['author_url'] = $authorURL;
        }
        if ($parentPost) {
            $postArray['parent_post'] = $parentPost;
        }
        if ($state) {
            $postArray['state'] = $state;
        }
        return $this->doCurl("http://disqus.com/api/create_post/", TRUE, $postArray);

    }


}


// END Disqus Class

/* End of file disqus.php */
/* Location: ./system/application/libraries/disqus.php */