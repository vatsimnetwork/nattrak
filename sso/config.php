<?php
/**
 * Example VATSIM SSO Script package.
 * Configuration file for OAuth integration.
 *
 * @author Kieran Hardern
 *
 * @version 0.2
 */

/*
 * Contains all temporary config variables
 */
$sso = [];

/*
 * The location of the VATSIM OAuth interface
 */
$sso['base'] = getenv('SSO_BASE');

/*
 * The consumer key for your organisation (provided by VATSIM)
 */
$sso['key'] = getenv('SSO_KEY');

/*
 * The secret key for your orgnisation (provided by VATSIM)
 * Do not give this to anyone else or display it to your users. It must be kept server-side
 */
$sso['secret'] = getenv('SSO_SECRET');

/*
 * The key for whic temporary (token) details for each user will be stored e.g. $_SESSION['mykey']
 * If you chose to handle the tokens yourself by another method, you can remove this
 */
define('SSO_SESSION', 'oauth');

/*
 * The URL users will be redirected to after they log in, this should
 * be on the same server as the request
 */

// Using https or http?
$http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';

// determing location from URL (comment out if manually defining - example below)
$sso['return'] = getenv('SSO_RETURN');
$sso_return = getenv('SSO_RETURN');

/*
 * The signing method you are using to encrypt your request signature.
 * Different options must be enabled on your account at VATSIM.
 * Options: RSA / HMAC
 */
$sso['method'] = getenv('SSO_METHOD');

/*
 * Your RSA **PRIVATE** key
 * If you are not using RSA, this value can be anything (or not set)
 */
$sso['cert'] = getenv('SSO_CERT');
