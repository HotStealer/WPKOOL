=== External Media ===
Author: Minnur Yunusov
Author URI: http://www.minnur.com/
Contributors: minnur
Donate link: https://goo.gl/C2cBDF
Tags: Dropbox, Box, OneDrive, Google Drive, Instagram, CloudApp, Upload from remote, remote media, remote URL, remote image, remote file, external media, wp remote upload, external media upload, external image upload
Requires at least: 4.4
Tested up to: 4.9
Stable tag: 1.0.18
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import files from thrid-party services (Dropbox, Box, OneDrive, Google Drive, Instagram, CloudApp and any external file).

== Description ==

Import files from or create external links from third-party services (Dropbox, Box, OneDrive, Google Drive, Instagram, [CloudApp](http://www.shareasale.com/r.cfm?B=1027572&U=1597643&M=71652&urllink=) and any other external file from URL).

This plugin provides convenient way of linking and using files from the services listed above. It is intuitive, controls located where you expect them to be. For instance if you would like to create a link to your file in your Dropbox account, just press Add Media and choose Insert from URL media tab and then click Link to Dropbox button.

The plugin provide two options:
- Insert from URL (this will link to a file located on one of the services)
- Import file (this will upload a file from a remote service and store the file in Wordpress)

Note: Imported files from a remote services become a permanent file and changes made to the file on the service won't reflect on the site. You would have to re-import the file using External Media plugin.

The plugin won't upload the same file twice, instead it will check if the file already exists in your Wordpress site and re-use it.

Instagram plugin allows you to easily upload images from your Instagram to your website (permanently) this won't be a link to a photo if you choose Upload option.

> [Donations](https://goo.gl/C2cBDF) are always appreciated. This will help me to continue support and add new features for free. Thank you for using my plugin.

== Installation ==

**Getting started.**

  NOTE: Some steps require you to have developer accounts in third-party 
    services and obtain API Keys in order to configure the plugin. 
    If you're not a developer you might need help from someone you know 
    or contact the author for support.

  - Please enable the External Media plugin.
  - Configure one or more services. Each field has a description with links.
  - Please follow instructions to generate all required keys in those links.

**Configration.**

  - Enable plugins you would like to see in the Add Media library.
  - Set "Only allow inser to remote files" if you would like to 
    use the plugin only in "Insert from URL" media tab.

**Dropbox.**

  - Use your existing account or create new account in Dropbox (dropbox.com).
  - Open https://www.dropbox.com/developers/apps and create Drop-in app.
  - Enter all domain names in ChooserSaver domains field.
  - Copy the "App Key" and use it in the plugin to enable insert links or
    imports from Dropbox.

**Box.**

  - Use your existing account or create new account in Box (box.com).
  - Open https://app.box.com/developers/services and create a new Box Application.
  - Once created you should be able to see the Api Key.
  - Copy the "Api Key" (see under "Backend Parameters") and use it in the 
    plugin to enable insert links or imports from Box.

**Instagram.**

  NOTE: Please note you might need to re-open Instagram file picker popup after you
    first time login. Please also note that users only can choose their own photos.

  - Open https://www.instagram.com/developer/clients/manage/ and register
    a New Client.
  - Provide Redirect URI (this can be found on the plugin configuration page)
  - Use Client ID and Client Secret in the plugin.
  - NOTE: This plugin ONLY allows to use your own Instagram pictures.

**OneDrive (Microsoft).**

  NOTE: OneDrive button doesn't always trigger the popup. You have to keep pressing the
    button until the popup shows up. It behaves the same even on the MS's website.
    See https://dev.onedrive.com/sdk/javascript-picker-saver.htm
    It might start working well once they fixe the issue.

  - Please Register (https://account.live.com/developers/applications) your app 
    to get an app ID (client ID), if you haven't already done so.
  - Ensure that the web page that is going to reference the SDK is a 
    Redirect URL under Application Settings.
  - Set Mobile or desktop client app to No.
  - Leave Target domain empty.
  - Set Restrict JWT issuing to Yes.
  - Copy the Client ID and use it on the plugin configuration page.

  IMPORTANT: Most people have problems with properly configuring the OneDrive app. 
    You have to add your wp-admin/edit.php?post_type=page page paths as Redirect URLs.
    For instance: http://example.com/wp-admin/post-new.php,
    http://wp.local.com:8888/wp-admin/post-new.php?post_type=page. 
    To FIX this issue you would have to provide all the page URLs where you are going to use the uploader.

**GoogleDrive.**

  - To get started using Google Picker API, you need to first create or select a
    project in the Google Developers Console and enable the API.
    https://console.developers.google.com/flows/enableapi?apiid=picker
  - Add your Client ID obtained from the Google Developers Console. 
    Example format: 886162316824-pfrtpjns2mqnek6e35gv321tggtmp8vq.apps.googleusercontent.com
  - Application ID. Its the first number in your Client ID. e.g. 886162316824
  - Add scopes or use the default scope.
  - Add your domain to Authorized JavaScript origins.
    More about scopes: https://developers.google.com/picker/docs/#otherviews
  - Make sure you enable Picker API.

**CloudApp.**

  - Use your existing account or create new account in [CloudApp](http://www.shareasale.com/r.cfm?B=1027572&U=1597643&M=71652&urllink=).
  - Enter your Email address and password
  - Save configuration.

**Usage.**

  - Create or edit any page or other content.
  - Press Add Media button.
  - Choose either you would like to insert a link to the file (Insert from URL)
    or import file from third-party service (this will save files in Wordpress
    and will become permanent, further changes to files on the third-party service
    won't reflect on the site).

For more information or customization please contact the author of this plugin.

== Frequently Asked Questions ==

Not available at the moment.

== Upgrade Notice ==

Not available at the moment.

== Screenshots ==

1. /assets/screenshot-1.png
2. /assets/screenshot-2.png
3. /assets/screenshot-3.png

== Changelog ==

= 1.0.18 =
* Add donation box
= 1.0.17 =
* Version bump. Test with Wordpress 4.9
= 1.0.16 =
* Remove anonymous callback function from uasort function.
* Fix PHP incompatibility issues.
= 1.0.15 =
* Fix issues
= 1.0.14 =
* Add [CloudApp](http://www.shareasale.com/r.cfm?B=1027572&U=1597643&M=71652&urllink=) support
* Refine plugin system
= 1.0.13 =
* Test with Wordpress 4.8
= 1.0.12 =
* Add CURLOPT_FOLLOWLOCATION option to follow remote media redirects.
= 1.0.11 =
* Fix JS issue.
* Version bump. Test with Wordpress 4.7.3
= 1.0.10 =
* Fix issues.
* Version bump. Test with Wordpress 4.6
= 1.0.9 =
* Fix issue with Insert from URL.
= 1.0.8 =
* Version update. Test with Wordpress 4.5
= 1.0.7 =
* Refine Google Drive picker implementation (add more control options).
* Add filename sanitization logic. Reported by ceedric.
* A new plugin to import files from remote URLs.
= 1.0.6 =
* Refine readme file.
= 1.0.5 =
* Change GoogleDrive viewer settings to display folders.
= 1.0.4 =
* Refine instructions.
= 1.0.3 =
* Refine readme file.
= 1.0.2 =
* Add 128x128 icon.
= 1.0.1 =
* Add 256x256 icon.
= 1.0 =
* Initial release.
