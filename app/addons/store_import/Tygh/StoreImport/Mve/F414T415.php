<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

namespace Tygh\StoreImport\Mve;
use Tygh\StoreImport\General;
use Tygh\Registry;

class F414T415
{
    protected $store_data = array();
    protected $main_sql_filename = 'mve_F414T415.sql';

    public function __construct($store_data)
    {
        $store_data['product_edition'] = 'MULTIVENDOR';
        $this->store_data = $store_data;
    }

    public function import($db_already_cloned)
    {
        General::setProgressTitle(__CLASS__);
        if (!$db_already_cloned) {
            if (!General::cloneImportedDB($this->store_data)) {
                return false;
            }
        } else {
            General::setEmptyProgressBar(__('importing_data'));
            General::setEmptyProgressBar(__('importing_data'));
        }

        $main_sql = Registry::get('config.dir.addons') . 'store_import/database/' . $this->main_sql_filename;
        if (is_file($main_sql)) {
            //Process main sql
            if (!db_import_sql_file($main_sql)) {
                return false;
            }
        }

        General::restoreSettings();
        if (db_get_field("SELECT status FROM ?:addons WHERE addon = 'searchanise'") != 'D') {
            db_query("UPDATE ?:addons SET status = 'D' WHERE addon = 'searchanise'");
            fn_set_notification('W', __('warning'), General::getUnavailableLangVar('uc_searchanise_disabled'));
        }

        General::setActualLangValues();
        General::updateAltLanguages('language_values', 'name');
        General::updateAltLanguages('settings_descriptions', array('object_id', 'object_type'));

        General::setEmptyProgressBar();
        General::setEmptyProgressBar();
        General::setEmptyProgressBar();
        General::setEmptyProgressBar();
        return true;
    }
}
