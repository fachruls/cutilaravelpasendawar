<?php
$contents = file_get_contents('test_results.txt');
echo mb_substr($contents, mb_strpos($contents, "Failed asserting that"), 2000);
