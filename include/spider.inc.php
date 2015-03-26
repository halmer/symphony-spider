<?php
    function getFileContent($url, $divId)
    {
        set_time_limit(60);
        $fileContent = file_get_contents($url);
        return ($fileContent === false)? "" : $fileContent;
    }
    
    function saveFileContent($fileContent, $fileUri)
    {
        //debug
        echo 'saved ' . $fileUri . "\n";
    }
    
    function getLinks($fileContent, &$linkContainer)
    {
        $links = array();
        preg_match_all('/href="[^"]+"/', $fileContent, $links);
        foreach($links[0] as $value)
        {
            $url = parse_url(preg_replace('/href="([^\']+)"/', '$1', $value));
            //if ($url['host'] == $linkContainer['homeUrl']['host'])
            //{
                if (strpos($url['path'], $linkContainer['homeUrl']['path']) !== false)
                {
                    $link = $url['path'] . '?' . $url['query'];
                    if (!isset($linkContainer[$link]))
                    {
                        $linkContainer[$link] = false;
                    }
                }
            //}
        }
    }
    
    function runSpider($homeUrl)
    {
        $linkContainer = array();
        $linkContainer['homeUrl'] = parse_url($homeUrl);
        $content = getFileContent($homeUrl, '');
        saveFileContent($content, $homeUrl);
        getLinks($content, $linkContainer);
        $wasUnsavedLink = true;
        $i = 0;
        while (($wasUnsavedLink) && ($i<1000))
        {
            $wasNewLink = false;
            foreach ($linkContainer as $link => &$isLinkDone)
            {
                if ($link != 'homeUrl')
                {
                    if (!$isLinkDone) {
                        $url = $linkContainer['homeUrl']['scheme'] . '://' . $linkContainer['homeUrl']['host'] . $link;
                        $content = getFileContent($url, '');
                        saveFileContent($content, $url);
                        getLinks($content, $linkContainer);
                        $isLinkDone = true;
                        $wasUnsavedLink = true;
                        $i++;
                    }  
                }
            }
        }
        
    }