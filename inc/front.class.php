<?php

class PluginApprovebylinkFront extends CommonGLPI
{
    function showFormApprove($ticket, $get)
    {
        $title = sprintf("Approvals for the ticket: %s", $ticket['name']);
        echo "<form name='form' action=\"" . Toolbox::getItemTypeFormURL(__CLASS__) . "\" method='post'>";
        echo "<div class='center' id='tabsbody'>";
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr class='tab_bg_1'><th colspan='4'>" . __($title) . "</th></tr>";
        echo "<td >" . __('Approval comments : ') . "</td>";
        echo "<td colspan='3'><textarea rows='3' cols='90' name='comment' placeholder='Thanks, good job!.'></textarea></td></tr>";
        echo "<tr class='tab_bg_1'><td>" . __('Validate? : ') . "</td>";
        echo "<td colspan='3'>";
        Dropdown::showYesNo("approve", 1, -1);
        echo "</td>";
        echo "<td>";
        echo "<input type='hidden' name='config_class' value='" . __CLASS__ . "'>";
        echo "<input type='hidden' name='config_context' value='plugin:approvebylink'>";
        echo "<input type='hidden' name='u' value='" . $get['u'] . "'>";
        echo "<input type='hidden' name='id' value='" . $get['id'] . "'>";
        echo "<input type='hidden' name='h' value='" . $get['h'] . "'>";
        echo "</td></tr>";
        echo "<tr class='tab_bg_2'>";
        echo "<td colspan='4' class='center'>";
        echo "<input type='submit' name='update' class='submit' value=\"" . _sx('button', 'Send') . "\">";
        echo "</td></tr>";
        echo "</table></div>";
        Html::closeForm();
    }

    function updateTicket($ticketA, $post)
    {
        global $CFG_GLPI;
        $followup = new ITILFollowup();
        $comment=sprintf("%s\n\n---\n%s",$post["comment"],"This comment getting plugin approvebylink over Web interface");
        $followup->add([
            'items_id'        => $ticketA['id'],
            'itemtype'        => Ticket::class,
            'users_id'        => $post["u"],
            'content'         => __($comment, "approvebylink"),
            'is_private'      => true,
            'requesttypes_id' => 6 //other
        ]);
        if ($post['approve']){
            $input['status'] = Ticket::CLOSED;
        }else{
            $input['status'] = Ticket::ASSIGNED;
        }
        $t = new Ticket();
        $input['id'] = $ticketA['id'];

        $t->update($input);
        Html::displayTitle($CFG_GLPI['root_doc'] . '/pics/ok.png', __('Done'), __('Done'));

    }

    function handlePost($post)
    {
        if (!isset($post['approve']) or !is_numeric($post['approve'])) {
            $this->showNotFound();
            return;
        }
        if ($post['approve'] < 0 or $post['approve'] > 1) {
            $this->showNotFound();
            return;
        }
        $get = array();
        $get['u'] = $post["u"];
        $get['id'] = $post["id"];
        $get['h'] = $post["h"];
        if (!$this->testAllGet($get)) {
            $this->showNotFound();
            return;
        }
        $ticket = $this->getOneTicketByIdAndStatus($get['id'], Ticket::SOLVED);
        if (!$ticket or count($ticket) == 0) {
            $this->showNotFound();
            return;
        }
        $ticket = $ticket[intval($get['id'])];
        $config = new Config();
        $my_config = $config->getConfigurationValues('plugin:approvebylink');
        if (!$my_config['onlyOwnerApproved']) {
            $this->updateTicket($ticket, $post);
            return;
        }
        if (!$this->checkUserOwnerTicked($ticket, $get['u'])) {
            $this->showNotFound();
            return;
        }
        $this->updateTicket($ticket, $post);
    }

    function handleGetUrl($get)
    {
        if (!$this->testAllGet($get)) {
            $this->showNotFound();
            return;
        }
        $ticket = $this->getOneTicketByIdAndStatus($get['id'], Ticket::SOLVED);
        if (!$ticket or count($ticket) == 0) {
            $this->showNotFound();
            return;
        }
        $ticket = $ticket[intval($get['id'])];
        $config = new Config();
        $my_config = $config->getConfigurationValues('plugin:approvebylink');
        if (!$my_config['onlyOwnerApproved']) {
            $this->showFormApprove($ticket, $get);
            return;
        }
        if (!$this->checkUserOwnerTicked($ticket, $get['u'])) {
            $this->showNotFound();
            return;
        }
        $this->showFormApprove($ticket, $get);
    }

    function testAllGet($get): bool
    {
        if (!$this->testAllGet_url($get)) {
            return false;
        }
        if (!$this->testAllGet_Hash($get)) {
            return false;
        }
        return true;
    }

    function testAllGet_url($get): bool
    {
        if (!isset($get['u']) or empty($get['u'])) {
            return false;
        }
        if (!isset($get['id']) or empty($get['id'])) {
            return false;
        }
        if (!isset($get['h']) or empty($get['h'])) {
            return false;
        }
        if (!is_numeric($get['u'])) {
            return false;
        }
        if (strlen($get['h']) != 64) {
            return false;
        }
        return true;
    }

    function testAllGet_Hash($get): bool
    {
        include_once('../hook.php');
        $hash = plugin_approvebylink_getsignhash($get['u'], $get['id']);
        if ($hash != $get['h']) {
            return false;
        }
        return true;
    }

    function getOneTicketByIdAndStatus($id, $status): array
    {
        if (!is_numeric($status)) {
            return false;
        }
        $table = getTableForItemType(Ticket::class);
        $condidate = array();
        $condidate['id'] = $id;
        $condidate['status'] = $status;;
        return getAllDataFromTable($table, $condidate);
    }

    function showNotFound()
    {
        Html::displayNotFoundError();
    }

    function checkUserOwnerTicked($ticked, $userid)
    {
        $table = getTableForItemType(Ticket_User::class);
        $condidate = array();
        $condidate['tickets_id'] = $ticked['id'];
        $condidate['users_id'] = $userid;
        $result = getAllDataFromTable($table, $condidate);
        if (count($result) > 0) {
            return true;
        }
        return false;
    }
}