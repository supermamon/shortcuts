<?php

    // Conversion Class
    include 'config.class.php';
    class YouTubeToMp3Converter extends Config
    {
        // Private Fields
        private $_songFileName = '';
        private $_flvUrls = array();
        private $_tempVidFileName;
        private $_uniqueID = '';
        private $_vidSrcTypes = array('source_code', 'url');
        private $_percentVidDownloaded = 0;

        #region Public Methods
        function __construct()
        {
            $this->_uniqueID = time() . "_" . uniqid('', true);
        }

        function DownloadVideo($youTubeUrl)
        {
            $file_contents = file_get_contents($youTubeUrl);
            if ($file_contents !== false)
            {
                $this->SetSongFileName($file_contents);
                $this->SetFlvUrls($file_contents, $youTubeUrl);
                if ($this->GetSongFileName() != '' && count($this->GetFlvUrls()) > 0)
                {
                    return $this->SaveVideo($this->GetFlvUrls());
                }
            }
            return false;
        }

        function GenerateMP3($audioQuality)
        {
            $qualities = $this->GetAudioQualities();
            $quality = (in_array($audioQuality, $qualities)) ? $audioQuality : $qualities[1];
            $exec_string = parent::_FFMPEG.' -i '.$this->GetTempVidFileName().' -vol '.parent::_VOLUME.' -y -acodec libmp3lame -ab '.$quality.'k '.$this->GetSongFileName() . ' 2> logs/' . $this->_uniqueID . '.txt';
            $ffmpegExecUrl = preg_replace('/(([^\/]+?)(\.php))$/', "exec_ffmpeg.php", "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
            $postData = "cmd=".urlencode($exec_string);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ffmpegExecUrl);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_exec($ch);
            curl_close($ch);
        }

        function DownloadMP3($file)
        {
            $filepath = parent::_SONGFILEDIR . urldecode($file);
            $filename = urldecode($file);
            if (parent::_ENABLE_CONCURRENCY_CONTROL)
            {
                $filename = preg_replace('/((_uuid-)(\w{13})(\.mp3))$/', "$4", $filename);
            }
            if (is_file($filepath))
            {
                header('Content-Type: audio/mpeg3');
                header('Content-Length: ' . filesize($filepath));
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                ob_clean();
                flush();
                readfile($filepath);
                die();
            }
            else
            {
                $redirect = explode("?", $_SERVER['REQUEST_URI']);
                header('Location: ' . $redirect[0]);
            }
        }

        function ExtractSongTrackName($vidSrc, $srcType)
        {
            $name = '';
            $vidSrcTypes = $this->GetVidSrcTypes();
            if (in_array($srcType, $vidSrcTypes))
            {
                $vidSrc = ($srcType == $vidSrcTypes[1]) ? file_get_contents($vidSrc) : $vidSrc;
                if ($vidSrc !== false)
                {
                    if (preg_match('/(<title>)(.+?)( - YouTube)(<\/title>)/', $vidSrc, $matches) == 1)
                    {
                        $name = trim($matches[2]);
                        $name = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $name);
                        $name = (!empty($name)) ? html_entity_decode($name) : 'unknown_'.time();
                    }
                }
            }
            return $name;
        }

        function ExtractVideoId($youTubeUrl)
        {
            $v = '';
            $urlQueryStr = parse_url(trim($youTubeUrl), PHP_URL_QUERY);
            if ($urlQueryStr !== false && !empty($urlQueryStr))
            {
                parse_str($urlQueryStr);
            }
            return $v;
        }

        function UpdateVideoDownloadProgress($downloadSize, $downloaded, $uploadSize, $uploaded)
        {
            $percent = round($downloaded/$downloadSize, 2) * 100;
            if ($percent > $this->_percentVidDownloaded)
            {
                $this->_percentVidDownloaded++;
                echo '<script type="text/javascript">updateVideoDownloadProgress("'. $percent .'");</script>';
                ob_end_flush();
                ob_flush();
                flush();
            }
        }
        #endregion

        #region Private "Helper" Methods
        private function SaveVideo(array $urls)
        {
            $success = false;
            $vidCount = -1;
            while (!$success && ++$vidCount < count($urls))
            {
                $this->_percentVidDownloaded = 0;
                $this->SetTempVidFileName();
                $file = fopen($this->GetTempVidFileName(), 'w');
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_FILE, $file);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL, $urls[$vidCount]);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_NOPROGRESS, false);
                curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this, 'UpdateVideoDownloadProgress'));
                curl_setopt($ch, CURLOPT_BUFFERSIZE, 4096000);
                curl_exec($ch);
                curl_close($ch);
                fclose($file);
                if (is_file($this->GetTempVidFileName()))
                {
                    if (!filesize($this->GetTempVidFileName()) || filesize($this->GetTempVidFileName()) < 10000)
                    {
                        unlink($this->GetTempVidFileName());
                    }
                    else
                    {
                        $success = true;
                    }
                }
            }
            return $success;
        }

        private function DecryptYouTubeCypher($signature)
        {
            $sigParts = explode('.', $signature);
            if (count($sigParts) == 2)
            {
                $sigParts[1] = substr(substr($sigParts[1], 0, 8) . substr($sigParts[0], 0, 1) . substr($sigParts[1], 9, 9) . substr($sigParts[1], -4, 1) . substr($sigParts[1], 19, 20) . substr($sigParts[1], 18, 1), 0, 40);
                $sigParts[0] = substr($sigParts[0], -40);
                $signature = strrev($sigParts[0] . '.' . $sigParts[1]);
            }
            return $signature;
        }
        #endregion

        #region Properties
        public function GetSongFileName()
        {
            return $this->_songFileName;
        }
        private function SetSongFileName($file_contents)
        {
            $vidSrcTypes = $this->GetVidSrcTypes();
            $trackName = $this->ExtractSongTrackName($file_contents, $vidSrcTypes[0]);
            if (!empty($trackName))
            {
                $fname = parent::_SONGFILEDIR . preg_replace('/_{2,}/','_',preg_replace('/ /','_',preg_replace('/[^A-Za-z0-9 _-]/','',$trackName)));
                $fname .= (parent::_ENABLE_CONCURRENCY_CONTROL) ? uniqid('_uuid-') : '';
                $this->_songFileName = $fname . '.mp3';
            }
        }

        public function GetFlvUrls()
        {
            return $this->_flvUrls;
        }
        private function SetFlvUrls($file_contents, $youTubeUrl)
        {
            $vidUrls = array();
            $vidSrcTypes = $this->GetVidSrcTypes();
            $cypherUsed = false;
            $vidInfoTypes = array('&el=embedded', '&el=detailpage', '&el=vevo', '');
            $vidId = $this->ExtractVideoId($youTubeUrl);
            foreach ($vidInfoTypes as $infotype)
            {
                $content = file_get_contents('https://www.youtube.com/get_video_info?&video_id='.$vidId.$infotype.'&ps=default&eurl=&gl=US&hl=en');
                parse_str($content, $output);
                //@print_r($output);
                //echo "\n\n";
                if (isset($output['status']) && $output['status'] == 'ok')
                {
                    //die(print_r($output));
                    $cypherUsed = isset($output['use_cipher_signature']) && $output['use_cipher_signature'] == 'True';
                    break;
                }
            }
            if (preg_match('/(ytplayer\.config = )([^\r\n]+?)(;<\/script>)/', $file_contents, $matches) == 1)
            {
                $jsonObj = json_decode(trim($matches[2], ';'));
                if (isset($jsonObj->args->url_encoded_fmt_stream_map))
                {
                    $urls = urldecode(urldecode($jsonObj->args->url_encoded_fmt_stream_map));
                    //$urls = urldecode(urldecode($jsonObj->args->adaptive_fmts));
                    //die($urls);
                    if (preg_match('/^((.+?)(=))/', $urls, $matches) == 1)
                    {
                        $urlsArr = preg_split('/,'.preg_quote($matches[0], '/').'/', $urls, -1, PREG_SPLIT_NO_EMPTY);
                        foreach ($urlsArr as $url)
                        {
                            if ($matches[0] != 'url=')
                            {
                                $url = ($url != $urlsArr[0]) ? $matches[0].$url : $url;
                                $urlBase = preg_replace('/(.+?)(url=)(.+?)(\?)(.+)/', "$3$4", $url);
                                $urlParams = preg_replace('/(.+?)(url=)(.+?)(\?)(.+)/', "$1$5", $url);
                                $url = $urlBase . "&" . $urlParams;
                            }
                            else
                            {
                                $url = preg_replace('/^(url=)/', "", $url);
                            }
                            $url = preg_replace('/(.*)(itag=\d+&)(.*?)/', '$1$3', $url, 1);
                            if (preg_match('/quality=small/', $url) != 1)
                            {
                                $url = preg_replace('/&sig=|&s=/', "&signature=", $url);
                                $url = trim($url, ',');
                                $url .= '&title=' . urlencode($this->ExtractSongTrackName($file_contents, $vidSrcTypes[0]));
                                $url = preg_replace_callback('/(&type=)(.+?)(&)/', function($match){return $match[1].urlencode($match[2]).$match[3];}, $url);
                                if ($cypherUsed)
                                {
                                    $urlParts = parse_url($url);
                                    parse_str($urlParts['query'], $vars);
                                    //echo $vars['signature'] . "\n\n";
                                    $vars['signature'] = $this->DecryptYouTubeCypher($vars['signature']);
                                    //die($vars['signature'] . "\n\n");
                                    $queryStr = http_build_query($vars, '', '&');
                                    $url = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'] . '?' . $queryStr;
                                }
                                $vidUrls[] = $url;
                            }
                        }
                        $vidUrls = array_reverse($vidUrls);
                        //die(print_r($vidUrls));
                    }
                }
            }
            $this->_flvUrls = $vidUrls;
        }

        public function GetAudioQualities()
        {
            return $this->_audioQualities;
        }

        private function GetTempVidFileName()
        {
            return $this->_tempVidFileName;
        }
        private function SetTempVidFileName()
        {
            $this->_tempVidFileName = parent::_TEMPVIDDIR . $this->_uniqueID .'.flv';
        }

        public function GetVidSrcTypes()
        {
            return $this->_vidSrcTypes;
        }

        public function GetUniqueID()
        {
            return $this->_uniqueID;
        }
        #endregion
    }

?>