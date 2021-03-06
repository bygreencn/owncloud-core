<?php
/**
 * @author Björn Schießle <bjoern@schiessle.org>
 * @author Christopher Schäpers <kondou@ts.unde.re>
 * @author Joas Schilling <coding@schilljs.com>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Robin McCorkell <robin@mccorkell.me.uk>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 * @author Tom Needham <tom@owncloud.com>
 *
 * @copyright Copyright (c) 2016, ownCloud GmbH.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

use OCP\API;

// Config
API::register(
	'get',
	'/config',
	['OC_OCS_Config', 'apiConfig'],
	'core',
	API::GUEST_AUTH
	);
// Person
API::register(
	'post',
	'/person/check',
	['OC_OCS_Person', 'check'],
	'core',
	API::GUEST_AUTH
	);
// Privatedata
API::register(
	'get',
	'/privatedata/getattribute',
	['OC_OCS_Privatedata', 'get'],
	'core',
	API::USER_AUTH,
	['app' => '', 'key' => '']
	);
API::register(
	'get',
	'/privatedata/getattribute/{app}',
	['OC_OCS_Privatedata', 'get'],
	'core',
	API::USER_AUTH,
	['key' => '']
	);
API::register(
	'get',
	'/privatedata/getattribute/{app}/{key}',
	['OC_OCS_Privatedata', 'get'],
	'core',
	API::USER_AUTH
	);
API::register(
	'post',
	'/privatedata/setattribute/{app}/{key}',
	['OC_OCS_Privatedata', 'set'],
	'core',
	API::USER_AUTH
	);
API::register(
	'post',
	'/privatedata/deleteattribute/{app}/{key}',
	['OC_OCS_Privatedata', 'delete'],
	'core',
	API::USER_AUTH
	);
// cloud
API::register(
	'get',
	'/cloud/capabilities',
	['OC_OCS_Cloud', 'getCapabilities'],
	'core',
	API::USER_AUTH
	);
API::register(
	'get',
	'/cloud/user',
	['OC_OCS_Cloud', 'getCurrentUser'],
	'core',
	API::USER_AUTH
);

// Server-to-Server Sharing
if (\OC::$server->getAppManager()->isEnabledForUser('files_sharing')) {
	$federatedSharingApp = new \OCA\FederatedFileSharing\AppInfo\Application('federatedfilesharing');
	$addressHandler = new \OCA\FederatedFileSharing\AddressHandler(
		\OC::$server->getURLGenerator(),
		\OC::$server->getL10N('federatedfilesharing')
	);
	$notification = new \OCA\FederatedFileSharing\Notifications(
		$addressHandler,
		\OC::$server->getHTTPClientService(),
		new \OCA\FederatedFileSharing\DiscoveryManager(\OC::$server->getMemCacheFactory(), \OC::$server->getHTTPClientService()),
		\OC::$server->getJobList()
	);
	$s2s = new OCA\FederatedFileSharing\RequestHandler(
		$federatedSharingApp->getFederatedShareProvider(),
		\OC::$server->getDatabaseConnection(),
		\OC::$server->getShareManager(),
		\OC::$server->getRequest(),
		$notification,
		$addressHandler,
		\OC::$server->getUserManager()
	);
	API::register('post',
		'/cloud/shares',
		[$s2s, 'createShare'],
		'files_sharing',
		API::GUEST_AUTH
	);

	API::register('post',
		'/cloud/shares/{id}/reshare',
		[$s2s, 'reShare'],
		'files_sharing',
		API::GUEST_AUTH
	);

	API::register('post',
		'/cloud/shares/{id}/permissions',
		[$s2s, 'updatePermissions'],
		'files_sharing',
		API::GUEST_AUTH
	);


	API::register('post',
		'/cloud/shares/{id}/accept',
		[$s2s, 'acceptShare'],
		'files_sharing',
		API::GUEST_AUTH
	);

	API::register('post',
		'/cloud/shares/{id}/decline',
		[$s2s, 'declineShare'],
		'files_sharing',
		API::GUEST_AUTH
	);

	API::register('post',
		'/cloud/shares/{id}/unshare',
		[$s2s, 'unshare'],
		'files_sharing',
		API::GUEST_AUTH
	);

	API::register('post',
		'/cloud/shares/{id}/revoke',
		[$s2s, 'revoke'],
		'files_sharing',
		API::GUEST_AUTH
	);
}
