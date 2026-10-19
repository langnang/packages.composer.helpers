<?php
function xpath_query($htmlContent, $xpathQuery)
{
  $dom = new DOMDocument();
  @$dom->loadHTML($htmlContent); // 使用@抑制警告信息，以便于示例清晰
  $xpath = new DOMXPath($dom);

  $elements = $xpath->query($xpathQuery);

  $results = [];
  foreach ($elements as $node) {
    $results[] = [
      "title" => $node->nodeValue,
      "url" => $node->getAttribute('href')
    ];
  }
  return $results;
}
