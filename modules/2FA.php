<?php
/**
 * 2FA - module for brainycp.
 * two-factor authentication
 *
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author      kykyiiikuh <mr.kredo2@protonmail.com>
 * @link        https://github.com/KyKyIIIKuH/brainy_2fa
 * @version     1.0
 */

require_once('/usr/local/brainycp/modules/2FA/load.module.php');
TwoFAController::getInstance()->init($smarty, $tpl);