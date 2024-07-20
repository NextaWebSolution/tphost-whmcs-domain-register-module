<?php

use WHMCS\Module\Registrar\tphost\Request;

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\View\Menu\Item as MenuItem;

function tphost_sidebar(MenuItem $primarySidebar)
{
    $currentUser = new \WHMCS\Authentication\CurrentUser;
    $user = $currentUser->client();
    if (!$user) {
        return;
    }
    if (!is_null($primarySidebar->getChild('Domain Details Management'))) {
        $primarySidebar->getChild('Domain Details Management')
            ->addChild('Get EPP Code')
            ->setLabel('Get EPP Code')
            ->setUri('clientarea.php?action=domaingetepp&id=' . intval($_REQUEST['id']))
            ->setOrder(100);
    }
}

add_hook('ClientAreaPrimarySidebar', 1, "tphost_sidebar");

add_hook('AdminHomeWidgets', 1, function () {
    return new tphostAdminWidget();
});


class tphostAdminWidget extends \WHMCS\Module\AbstractWidget
{
    protected $title = 'TPHOST Credits';
    protected $description = '';
    protected $weight = 150;
    protected $columns = 1;
    protected $cache = false;
    protected $cacheExpiry = 60;
    protected $requiredPermission = '';

    public function getData()
    {
        return Request::call([]);
    }

    public function generateOutput($data)
    {
        return '<div style="margin:10px;padding:10px;text-align:center;font-size:16px;color:#000;">Credits: <b>' . $data['data'] . '</b></div>';
    }
}


add_hook('AfterCronJob', 1, function ($vars) {
    $last_check = \WHMCS\Config\Setting::getValue('tphost_domains_last_sync');
    if (!$last_check || (\Carbon\Carbon::parse($last_check)->addMinutes(30)->toDateTimeString() <= \Carbon\Carbon::now()->toDateTimeString())) {
        $data = Request::call(['action' => 'domains']);
        if (is_array($data['data'])) {
            foreach ($data['data'] as $domain) {
                if (!Capsule::table('tbldomains')->where('registrar', 'tphost')->where('domain', $domain['domain'])->where('status', $domain['status'])->count()) {
                    Capsule::table('tbldomains')->where('registrar', 'tphost')->where('domain', $domain['domain'])->update(['status' => $domain['status']]);
                }
                if (!Capsule::table('tbldomains')->where('registrar', 'tphost')->where('domain', $domain['domain'])->where('expirydate', $domain['expirydate'])->count()) {
                    Capsule::table('tbldomains')->where('registrar', 'tphost')->where('domain', $domain['domain'])->update(['expirydate' => $domain['expirydate']]);
                }
            }
        }
        \WHMCS\Config\Setting::setValue('tphost_domains_last_sync', \Carbon\Carbon::now()->toDateTimeString());
    }

});
