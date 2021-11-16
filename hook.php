<?php
/**
 * Install hook
 *
 * @return boolean
 */
function plugin_approvebylink_install(): bool
{
    $config = new Config();
    $config->setConfigurationValues('plugin:approvebylink', ['salt1' => plugin_approvebylink_genhash()]);
    $config->setConfigurationValues('plugin:approvebylink', ['salt2' => plugin_approvebylink_genhash()]);
    $config->setConfigurationValues('plugin:approvebylink', ['onlyOwnerApproved' => true]);
    //ProfileRight::addProfileRights(['approvebylink:read']);
    return true;
}

/**
 * Uninstall hook
 *
 * @return boolean
 */
function plugin_approvebylink_uninstall(): bool
{
    $config = new Config();
    $config->deleteConfigurationValues('plugin:approvebylink', ['salt1' => false]);
    $config->deleteConfigurationValues('plugin:approvebylink', ['salt2' => false]);
    $config->deleteConfigurationValues('plugin:approvebylink', ['onlyOwnerApproved' => false]);
    return true;
}
function plugin_approvebylink_get_datas(NotificationTargetTicket $target) {
    if ($target->obj->fields['status']!=Ticket::SOLVED){
        return;
    }
    global $CFG_GLPI;

    $u=$target->data['authors'][0]["##author.id##"];
    $id=$target->data['##ticket.id##'];
    $sign=plugin_approvebylink_getsignhash($u,$id);
    $link=sprintf("%s%s?u=%s&id=%s&h=%s",__s($CFG_GLPI['url_base']),Toolbox::getItemTypeFormURL(PluginApprovebylinkFront::class),$u,$id,$sign);
    $target->data['##lang.approvebylink.url##'] = __('Link');
    $target->data['##approvebylink.url##'] = __s($link,'approvebylink');

}

function plugin_approvebylink_genhash($length = 200) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function plugin_approvebylink_getsignhash($u,$id){
    $config = new Config();
    $my_config=$config->getConfigurationValues('plugin:approvebylink');
    $salt1=$my_config['salt1'];
    $salt2=$my_config['salt2'];
    $salt1h=hash('sha512', $salt1);
    $salt2h=hash('sha512', $salt2);
    $s=hash("sha256",sprintf("%s%s%s%s%s%s",$salt1,$salt1h,$salt2,$salt2h,$u,$id));
    return hash("sha256",sprintf("%s%s",md5($s),md5($s)));
}
