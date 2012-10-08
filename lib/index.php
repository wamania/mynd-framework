<?php

set_include_path(
	realpath(LI_LIB).
	PATH_SEPARATOR .get_include_path()
);

/**
 * Inclusion du coeur
 */
require_once LI_LIB.'core/controller.class.php';
require_once LI_LIB.'core/boot.class.php';
require_once LI_LIB.'core/helper.class.php';
require_once LI_LIB.'core/session.class.php';
require_once LI_LIB.'core/view.class.php';
require_once LI_LIB.'core/registery.class.php';
require_once LI_LIB.'core/request.class.php';
require_once LI_LIB.'core/response.class.php';
require_once LI_LIB.'core/exception.class.php';

require_once LI_LIB.'url/urlengine.interface.php';
require_once LI_LIB.'url/param_string.php';
require_once LI_LIB.'url/multiviews.urlengine.php';
require_once LI_LIB.'url/query_string.urlengine.php';
require_once LI_LIB.'url/mod_rewrite.urlengine.php';
require_once LI_LIB.'url/simple.urlengine.php';
require_once LI_LIB.'url/helper.php';

require_once LI_LIB.'database/index.php';
require_once LI_LIB.'model/index.php';
require_once LI_LIB.'html/index.php';

/**
 *  Inclusion des utils
 */
require_once LI_LIB.'utils/date.class.php';
require_once LI_LIB.'utils/minihelpers.php';
require_once LI_LIB.'utils/utf8_helper.php';

require_once LI_LIB.'cache/apc.class.php';
require_once LI_LIB.'cache/fake.class.php';
require_once LI_LIB.'cache/memcache.class.php';

require_once 'Zend/Mail.php';
