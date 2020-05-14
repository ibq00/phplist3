<?php

/**
 * @brief Ajax handler for generating the message body preview line shown in some mail clients below the subject
 * @note based on generatetext.php
 */

verifyCsrfGetToken();

// generate text content
$msgid = sprintf('%d', $_GET['id']);
$messagedata = loadMessageData($msgid);
// shorten to 90 chars (max message preview length)

// Check if the campaign content is a 'send as a web page' url, and if so, convert that to text instead
if (preg_match('/\[URL:(.+)\]/', $messagedata['message'], $regs)) {
    $content = fetchUrl($regs[1]);
    $previewText = HTML2Text($content);
} else {
    $previewText = HTML2Text($messagedata['message']);
}

$shortPreviewText = substr( $previewText, 0, 90);

// convert to visual preview
// FIXME this fails when the text is large, or contains £

// replace newlines with spaces
$shortPreviewText = str_replace("\r", "", $shortPreviewText);
$shortPreviewText = str_replace("\n", "", $shortPreviewText);
// replace escaped newlines
$shortPreviewText = trim($shortPreviewText);
$shortPreviewText = preg_replace("/\n/", '\\n', $shortPreviewText);
$shortPreviewText = preg_replace("/\r/", '', $shortPreviewText);

// fix entities
$shortPreviewText = htmlentities($shortPreviewText, ENT_IGNORE, 'UTF-8', true);

// replace quotes
$shortPreviewText = str_replace('"', '&quot;', $shortPreviewText);

$status = 
'<script type="text/javascript">
    $("#messagepreview").val("' .$shortPreviewText.'");
</script>
';
