<?php
    function runSpider($homeUrl)
    {
        set_time_limit(120);
        $linkContainer = array();
        $linkContainer['homeUrl'] = parse_url($homeUrl);
        $content = getFileContent($homeUrl, '');
        saveFileContent($content, $homeUrl);
        getLinks($content, $linkContainer);
    }

    function processContent(&$linkContainer)
    {
        $wasUnsavedLink = true;
        while ($wasUnsavedLink)
        {
            $wasUnsavedLink = false;
            foreach ($linkContainer as $link => &$isLinkDone)
            {
                if ($link != 'homeUrl')
                {
                    if (!$isLinkDone)
                    {
                        $url = makeUrl($linkContainer['homeUrl']['scheme'], $linkContainer['homeUrl']['host'], $link);
                        $content = getFileContent($url, '');
                        saveFileContent($content, $url);
                        getLinks($content, $linkContainer);
                        $isLinkDone = true;
                        $wasUnsavedLink = true;
                    }  
                }
            }
        }
    }

    function makeUrl($scheme, $host, $link)
    {
        return $scheme . '://' . $host . $link;
    }

    function makeLink($path, $query)
    {
        return $path . '?' . $query;
    }
    
    function getFileContent($url, $divId)
    {
        $fileContent = getFileByCurl($url);
        return ($fileContent === false)? "" : getBlockContent($fileContent, 'column_02');
    }
    
    function saveFileContent($fileContent, $fileUri)
    {
        $filename = parse_url($fileUri);
        file_put_contents(str_replace('/', '-', $filename['path']) . '.html', $fileContent);

    }
    
    function getLinks($fileContent, &$linkContainer)
    {
        $links = array();
        preg_match_all('/href="[^"]+"/', $fileContent, $links);
        if ((!empty($links[0])) && (is_array($links[0])))
        {
            foreach($links[0] as $value)
            {
                $url = parse_url(preg_replace('/href="([^\']+)"/', '$1', $value));
                if (strpos($url['path'], $linkContainer['homeUrl']['path']) !== false)
                {
                    $link = makeLink($url['path'], $url['query']);
                    if (!isset($linkContainer[$link]))
                    {
                        $linkContainer[$link] = false;
                    }
                }
            }
        }
    }

    function getFileByCurl($url)
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, 60);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($curlHandler);
        curl_close($curlHandler);
        return ($content !== false) ? $content : "";
    }

    function getBlockContent($content, $blockId)
    {
        $blockNameOpen = '<div';
        $blockNameClose = '</div>';
        $posOpen = -1;
        $posClose = -1;
        $numBlocksToClose = 1;
        $blockStart = strpos($content, $blockNameOpen . ' class="' . $blockId . '"');
        if ($blockStart === false)
        {
            return "";
        }
        $blockEnd = $blockStart + 1;
        while ($numBlocksToClose > 0)
        {
            $posOpen = strpos($content, $blockNameOpen, $blockEnd +1);
            if ($posOpen === false) $posOpen = INF;
            $posClose = strpos($content, $blockNameClose, $blockEnd +1);
            if ($posClose === false) $posClose = INF;
            if ($posClose < $posOpen)
            {
                $numBlocksToClose--;
                $blockEnd = $posClose;
            }
            else
            {
                $numBlocksToClose++;
                $blockEnd = $posOpen;

            }
        }
        $blockEnd += strlen($blockNameClose);
        return substr($content, $blockStart, ($blockEnd - $blockStart));
    }
