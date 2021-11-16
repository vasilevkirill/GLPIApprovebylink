<?php

const APPROVEBYLINK_VERSION = '0.0.1';


function plugin_init_approvebylink()
{

    global $PLUGIN_HOOKS;
    $PLUGIN_HOOKS['csrf_compliant']['approvebylink'] = true;
    $PLUGIN_HOOKS['item_get_datas']['approvebylink'] = ['NotificationTargetTicket' => 'plugin_approvebylink_get_datas'];
    $PLUGIN_HOOKS['item_get_event']['approvebylink'] = ['NotificationTargetTicket' => 'plugin_approvebylink_get_event'];
    if (Session::haveRight('config', UPDATE)) {
        $PLUGIN_HOOKS['config_page']['approvebylink'] = 'config.php';
    }
}


function plugin_version_approvebylink()
{
    return [
        'name' => 'Approve Tiket By Link',
        'version' => APPROVEBYLINK_VERSION,
        'author' => '<a target="_blank" href="https://vasilevkirill.com">Vasilev kirill </a>',
        'license' => 'GLPv3',
        'homepage' => 'https://vasilevkirill.com',
        'requirements' => [
            'glpi' => [
                'min' => '9.1'
            ]
        ]
    ];
}


function plugin_approvebylink_check_config($verbose = false) {
    if (true) { 
        return true;
    }

    if ($verbose) {
        echo __('Installed / not configured', 'approvebylink');
    }
    return false;
}
