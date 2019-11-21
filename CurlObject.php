<?php


class CurlObject
{
    /**
     * get data by post method
     * @param $url
     * @param string $cookie
     * @param string $data
     * @param string $header
     * @param int $returnCookie
     * @return bool|string
     */
    function curl_post_request($url, $cookie = '', $data = '', $header = '', $returnCookie = 0, $userAgent='')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);


        if ($header) curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        if ($cookie) curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        if($userAgent) curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);

        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        $filecontent = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if ($returnCookie) {
            list($header, $body) = explode("\r\n\r\n", $filecontent, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $filecontent, $matches);
            $cookie = '';
            foreach ($matches[1] as $value) {
                $cookie = $value . ';' . $cookie;
            }
            $info['cookie']  = preg_replace('# #', '', $cookie);
            $info['content'] = $body;
            return $info;
        } else {
            return $filecontent;
        }
    }


    /**
     * get info by any method
     * @param $url
     * @param $data
     * @param string $method
     * @param string $type
     * @return bool|string
     */
    public function curl_any_request($url,$data,$method = 'GET',$type='json'){
        //初始化
        $ch = curl_init();
        $headers = [
            'form-data' => ['Content-Type: multipart/form-data'],
            'json'      => ['Content-Type: application/json'],
        ];
        if($method == 'GET'){
            if($data){
                $querystring = http_build_query($data);
                $url = $url.'?'.$querystring;
            }
        }
        // 请求头，可以传数组
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers[$type]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);         // 执行后不直接打印出来
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');     // 请求方式
            curl_setopt($ch, CURLOPT_POST, true);               // post提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);              // post的变量
        }
        if($method == 'PUT'){
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        if($method == 'DELETE'){
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不从证书中检查SSL加密算法是否存在
        $output = curl_exec($ch); //执行获取内容
        curl_close($ch); //释放curl句柄
        return $output;
    }
}