<?php

set_include_path(
    realpath(LI_LIB).
    PATH_SEPARATOR .get_include_path()
);

require_once LI_LIB.'/ClassLoader.php';
require_once LI_LIB.'/Helper.php';
require_once LI_LIB.'/Lib/Html/index.php';
