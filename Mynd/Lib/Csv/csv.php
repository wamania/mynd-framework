<?php

namespace Mynd\Lib\Csv;

class utf8encode_filter extends php_user_filter
{
    function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = utf8_encode($bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }
}

function fileHandleFromIso8859($handle)
{
    stream_filter_register("utf8encode", "utf8encode_filter") or die("Failed to register filter");
    stream_filter_prepend($handle, "utf8encode");

    return $handle;
}