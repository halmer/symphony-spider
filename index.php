<?php
    require_once("include/spider.inc.php");
    
    //TEST getBlockCpntent:
    //echo '0         V10       V20       V30       V40       V50       V60       V70       V80       V90' . "\n"; 
    //echo '012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012' . "\n"; 
    //echo '<div><div class="blockId"><div></div><div><div> ITS MADNESS! </div></div></div></div>' . "\n";  
    //echo getBlockContent('<div><div class="blockId"><div></div><div><div>ITS MADNESS!</div></div></div></div>' , 'blockId');
	
	//TEST getFileContent:
    //echo getFileContent('http://symfony.com/legacy/doc/jobeet/1_4/en?orm=Propel', '');

    runSpider('http://symfony.com/legacy/doc/jobeet/1_4/en?orm=Propel');